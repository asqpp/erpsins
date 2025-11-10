<?php

namespace Core;

class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all($orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        return $this->db->fetchAll($sql);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function where($conditions, $orderBy = null, $limit = null)
    {
        $where = [];
        $params = [];

        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = :{$field}";
            $params[$field] = $value;
        }

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function first($conditions)
    {
        $results = $this->where($conditions, null, 1);
        return $results[0] ?? null;
    }

    public function create($data)
    {
        $filteredData = $this->filterFillable($data);
        return $this->db->insert($this->table, $filteredData);
    }

    public function update($id, $data)
    {
        $filteredData = $this->filterFillable($data);
        $where = "{$this->primaryKey} = :id";
        return $this->db->update($this->table, $filteredData, $where, ['id' => $id]);
    }

    public function delete($id)
    {
        $where = "{$this->primaryKey} = :id";
        return $this->db->delete($this->table, $where, ['id' => $id]);
    }

    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";

        if (!empty($conditions)) {
            $where = [];
            $params = [];

            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }

            $sql .= " WHERE " . implode(' AND ', $where);
            $result = $this->db->fetch($sql, $params);
        } else {
            $result = $this->db->fetch($sql);
        }

        return (int)$result['count'];
    }

    public function paginate($page = 1, $perPage = 20, $conditions = [])
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";
        $countSql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $whereClause = " WHERE " . implode(' AND ', $where);
            $sql .= $whereClause;
            $countSql .= $whereClause;
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->db->fetchAll($sql, $params);
        $total = $this->db->fetch($countSql, $params)['count'];

        return [
            'data' => $data,
            'total' => (int)$total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }

    public function fetch($sql, $params = [])
    {
        return $this->db->fetch($sql, $params);
    }
}
