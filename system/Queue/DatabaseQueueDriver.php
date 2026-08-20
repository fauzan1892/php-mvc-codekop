<?php
declare(strict_types=1);

namespace System\Queue;

use PDO;
use System\Database;
use System\Queue\Contracts\QueueDriver;
use Throwable;

final class DatabaseQueueDriver implements QueueDriver
{
    private PDO $pdo;
    private string $tableName;
    private string $failedTableName;
    private string $table;
    private string $failedTable;
    private bool $sqlite;

    public function __construct(array $config = [])
    {
        $database = new Database();
        $pdo = $database->pdo();
        if (!$pdo instanceof PDO) {
            throw new QueueException('Queue database driver requires a configured PDO database.');
        }
        $this->pdo = $pdo;
        $this->sqlite = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
        $this->tableName = $this->identifier((string) ($config['table'] ?? 'jobs'));
        $this->failedTableName = $this->identifier((string) ($config['failed_table'] ?? 'failed_jobs'));
        $this->table = $this->quote($this->tableName);
        $this->failedTable = $this->quote($this->failedTableName);
        if ((bool) ($config['auto_create'] ?? true)) {
            $this->ensureSchema();
        }
    }

    public function push(
        string $jobClass,
        string $payload,
        string $queue,
        int $availableAt,
        int $createdAt
    ): string {
        $statement = $this->pdo->prepare(
            "INSERT INTO {$this->table} "
            . '(queue, job, payload, attempts, available_at, created_at) '
            . 'VALUES (:queue, :job, :payload, 0, :available_at, :created_at)'
        );
        $statement->execute([
            'queue' => $queue,
            'job' => $jobClass,
            'payload' => $payload,
            'available_at' => $availableAt,
            'created_at' => $createdAt,
        ]);

        return (string) $this->pdo->lastInsertId();
    }

    public function reserve(string $queue, int $retryAfter): ?JobEnvelope
    {
        $now = time();
        $stale = $now - max(1, $retryAfter);
        $recover = $this->pdo->prepare(
            "UPDATE {$this->table} SET reserved_at = NULL "
            . 'WHERE reserved_at IS NOT NULL AND reserved_at <= :stale'
        );
        $recover->execute(['stale' => $stale]);

        $this->pdo->beginTransaction();
        try {
            $sql = "SELECT id, queue, job, payload, attempts, available_at, created_at "
                . "FROM {$this->table} "
                . 'WHERE queue = :queue AND available_at <= :available_at AND reserved_at IS NULL '
                . 'ORDER BY id ASC LIMIT 1';
            if (!$this->sqlite) $sql .= ' FOR UPDATE';
            $statement = $this->pdo->prepare($sql);
            $statement->execute(['queue' => $queue, 'available_at' => $now]);
            $data = $statement->fetch(PDO::FETCH_ASSOC);
            if (!is_array($data)) {
                $this->pdo->commit();
                return null;
            }

            $update = $this->pdo->prepare(
                "UPDATE {$this->table} SET attempts = attempts + 1, reserved_at = :reserved_at "
                . 'WHERE id = :id'
            );
            $update->execute(['reserved_at' => $now, 'id' => $data['id']]);
            $this->pdo->commit();

            return new JobEnvelope(
                (string) $data['id'],
                (string) $data['queue'],
                (string) $data['job'],
                (string) $data['payload'],
                (int) $data['attempts'] + 1,
                (int) $data['available_at'],
                (int) $data['created_at']
            );
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw new QueueException('Unable to reserve database queue job.', 0, $exception);
        }
    }

    public function delete(JobEnvelope $job): void
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $statement->execute(['id' => $job->id]);
    }

    public function release(JobEnvelope $job, int $delay): void
    {
        $statement = $this->pdo->prepare(
            "UPDATE {$this->table} SET available_at = :available_at, reserved_at = NULL "
            . 'WHERE id = :id'
        );
        $statement->execute(['available_at' => time() + max(0, $delay), 'id' => $job->id]);
    }

    public function fail(JobEnvelope $job, string $exception): void
    {
        $this->pdo->beginTransaction();
        try {
            $insert = $this->pdo->prepare(
                "INSERT INTO {$this->failedTable} "
                . '(job_id, queue, job, payload, exception, failed_at) '
                . 'VALUES (:job_id, :queue, :job, :payload, :exception, :failed_at)'
            );
            $insert->execute([
                'job_id' => $job->id,
                'queue' => $job->queue,
                'job' => $job->jobClass,
                'payload' => $job->payload,
                'exception' => substr($exception, 0, 20000),
                'failed_at' => time(),
            ]);
            $delete = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $delete->execute(['id' => $job->id]);
            $this->pdo->commit();
        } catch (Throwable $throwable) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw new QueueException('Unable to store failed database queue job.', 0, $throwable);
        }
    }

    private function ensureSchema(): void
    {
        if ($this->sqlite) {
            $this->pdo->exec(
                "CREATE TABLE IF NOT EXISTS {$this->table} ("
                . 'id INTEGER PRIMARY KEY AUTOINCREMENT, queue VARCHAR(100) NOT NULL, '
                . 'job VARCHAR(255) NOT NULL, payload TEXT NOT NULL, attempts INTEGER NOT NULL DEFAULT 0, '
                . 'reserved_at INTEGER NULL, available_at INTEGER NOT NULL, created_at INTEGER NOT NULL)'
            );
            $this->pdo->exec(
                "CREATE INDEX IF NOT EXISTS {$this->tableName}_queue_available_idx "
                . "ON {$this->table} (queue, available_at, reserved_at)"
            );
            $this->pdo->exec(
                "CREATE TABLE IF NOT EXISTS {$this->failedTable} ("
                . 'id INTEGER PRIMARY KEY AUTOINCREMENT, job_id VARCHAR(64) NOT NULL, '
                . 'queue VARCHAR(100) NOT NULL, job VARCHAR(255) NOT NULL, payload TEXT NOT NULL, '
                . 'exception TEXT NOT NULL, failed_at INTEGER NOT NULL)'
            );
            return;
        }

        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS {$this->table} ("
            . 'id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, queue VARCHAR(100) NOT NULL, '
            . 'job VARCHAR(255) NOT NULL, payload LONGTEXT NOT NULL, attempts INT UNSIGNED NOT NULL DEFAULT 0, '
            . 'reserved_at INT UNSIGNED NULL, available_at INT UNSIGNED NOT NULL, created_at INT UNSIGNED NOT NULL, '
            . "INDEX {$this->tableName}_queue_available_idx (queue, available_at, reserved_at)) ENGINE=InnoDB"
        );
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS {$this->failedTable} ("
            . 'id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, job_id VARCHAR(64) NOT NULL, '
            . 'queue VARCHAR(100) NOT NULL, job VARCHAR(255) NOT NULL, payload LONGTEXT NOT NULL, '
            . 'exception TEXT NOT NULL, failed_at INT UNSIGNED NOT NULL) ENGINE=InnoDB'
        );
    }

    private function identifier(string $value): string
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value)) {
            throw new QueueException('Invalid queue table identifier.');
        }
        return $value;
    }

    private function quote(string $value): string
    {
        return '`' . $value . '`';
    }
}
