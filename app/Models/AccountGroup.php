<?php

namespace App\Models;

use Core\Model;

class AccountGroup extends Model
{
    protected $table = 'account_groups';
    protected $fillable = [
        'name', 'type', 'nature', 'parent_id', 'code',
        'description', 'is_system', 'status'
    ];

    public function getHierarchical()
    {
        return $this->db->fetchAll("
            SELECT g1.*, g2.name as parent_name,
                   (SELECT COUNT(*) FROM accounts WHERE group_id = g1.id) as account_count
            FROM account_groups g1
            LEFT JOIN account_groups g2 ON g1.parent_id = g2.id
            ORDER BY g1.type, g1.code
        ");
    }

    public function getByType($type)
    {
        return $this->where(['type' => $type, 'status' => 'active'], 'name ASC');
    }

    public function getChildren($parentId)
    {
        return $this->where(['parent_id' => $parentId, 'status' => 'active'], 'name ASC');
    }
}
