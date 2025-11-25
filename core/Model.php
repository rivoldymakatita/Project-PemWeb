<?php

namespace Core;

class Model
{
    protected $db;
    protected $table;
    protected $fillable = [];
    protected $hidden = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Set table name
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Get all records
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Find record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Find record by column
     */
    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Find multiple records by column
     */
    public function findAllBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Create new record
     */
    public function create($data)
    {
        $data = $this->filterFillable($data);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_values($data));
        
        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update record
     */
    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        $setClause = implode(', ', array_map(fn($key) => "{$key} = ?", array_keys($data)));
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        
        return $stmt->execute($values);
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Filter data by fillable columns
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Query builder - WHERE
     */
    public function where($column, $operator = null, $value = null)
    {
        return new QueryBuilder($this->db, $this->table, $column, $operator, $value);
    }

    /**
     * Raw query
     */
    public function query($sql)
    {
        return $this->db->query($sql);
    }
}