<?php
declare(strict_types=1);
namespace System;
defined('BASEPATH') || exit('No direct script access allowed');

class Crud extends \System\Database
{
    private function identifier(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(?:\.[A-Za-z_][A-Za-z0-9_]*)?$/', $identifier)) {
            throw new \InvalidArgumentException('Invalid SQL identifier.');
        }
        return $identifier;
    }

    public function get(string $table, ?string $order = null): \PDOStatement
    {
        $sql = 'SELECT * FROM ' . $this->identifier($table);
        if ($order !== null && $order !== '') $sql .= ' ORDER BY ' . $this->identifier($order);
        return $this->db->query($sql);
    }

    public function get_where(string $table, array $where): \PDOStatement
    {
        $keys = array_keys($where);
        $columns = array_map(fn(string $column): string => $this->identifier($column), $keys);
        $sql = 'SELECT * FROM ' . $this->identifier($table) . ' WHERE ' . implode('=? AND ', $columns) . '=?';
        $row = $this->db->prepare($sql);
        $row->execute(array_values($where));
        return $row;
    }

    public function get_where_or(string $table, array $where): \PDOStatement
    {
        $keys = array_keys($where);
        $columns = array_map(fn(string $column): string => $this->identifier($column), $keys);
        $sql = 'SELECT * FROM ' . $this->identifier($table) . ' WHERE ' . implode('=? OR ', $columns) . '=?';
        $row = $this->db->prepare($sql);
        $row->execute(array_values($where));
        return $row;
    }

    public function insert(string $table, array $data): bool
    {
        $keys = array_keys($data);
        $columns = array_map(fn(string $column): string => $this->identifier($column), $keys);
        $sql = 'INSERT INTO ' . $this->identifier($table) . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')';
        return $this->db->prepare($sql)->execute(array_values($data));
    }

    public function update(string $table, array $data, string $where, mixed $id): bool
    {
        $parts = [];
        foreach ($data as $key => $value) {
            $safeKey = $this->identifier((string) $key);
            $parts[] = $safeKey . '=:' . $safeKey;
        }
        $sql = 'UPDATE ' . $this->identifier($table) . ' SET ' . implode(', ', $parts) . ' WHERE ' . $this->identifier($where) . ' = :id';
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        foreach ($data as $key => $value) $statement->bindValue((string) $key, $value);
        return $statement->execute();
    }

    public function delete(string $table, string $where, mixed $id): bool
    {
        $sql = 'DELETE FROM ' . $this->identifier($table) . ' WHERE ' . $this->identifier($where) . ' = ?';
        return $this->db->prepare($sql)->execute([$id]);
    }
}
