<?php
declare(strict_types=1);
namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    protected array $config;
    protected ?PDO $db = null;
    private static array $pool = [];

    public function __construct(?array $config = null)
    {
        global $dbconfig;
        $this->config = $config ?? (is_array($dbconfig) ? $dbconfig : []);
        $this->db = $this->connect();
    }

    public function connect(): ?PDO
    {
        if ($this->db instanceof PDO) return $this->db;
        if (($this->config['database'] ?? '') === '') return null;

        $driver = strtolower((string) ($this->config['driver'] ?? 'mysql'));
        $dsn = $this->dsn($driver);
        $poolKey = hash('sha256', $dsn . '|' . (string) ($this->config['username'] ?? ''));
        if (isset(self::$pool[$poolKey]) && self::$pool[$poolKey] instanceof PDO) {
            return $this->db = self::$pool[$poolKey];
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT          => (bool) ($this->config['persistent'] ?? false),
        ] + (array) ($this->config['options'] ?? []);

        try {
            $this->db = new PDO(
                $dsn,
                (string) ($this->config['username'] ?? ''),
                (string) ($this->config['password'] ?? ''),
                $options
            );
            self::$pool[$poolKey] = $this->db;
            return $this->db;
        } catch (PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
            if ((bool) ($this->config['throw_exceptions'] ?? false)) {
                throw new RuntimeException('Database connection failed.', 0, $exception);
            }
            return null;
        }
    }

    public function pdo(): ?PDO { return $this->connect(); }

    private function dsn(string $driver): string
    {
        if ($driver === 'sqlite') {
            return 'sqlite:' . (string) $this->config['database'];
        }
        if ($driver !== 'mysql') {
            throw new RuntimeException('Unsupported database driver: ' . $driver);
        }
        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            (string) ($this->config['hostname'] ?? '127.0.0.1'),
            (int) ($this->config['port'] ?? 3306),
            (string) $this->config['database'],
            (string) ($this->config['charset'] ?? 'utf8mb4')
        );
    }
}
