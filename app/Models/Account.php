<?php

namespace App\Models;

use Core\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $fillable = [
        'account_code', 'account_name', 'group_id', 'subgroup_id',
        'opening_balance', 'opening_balance_type', 'current_balance',
        'description', 'is_system', 'is_bank', 'is_cash',
        'bank_details', 'status', 'created_by'
    ];

    public function getWithGroup()
    {
        return $this->db->fetchAll("
            SELECT a.*,
                   g.name as group_name,
                   g.type as group_type,
                   s.name as subgroup_name
            FROM accounts a
            LEFT JOIN account_groups g ON a.group_id = g.id
            LEFT JOIN account_subgroups s ON a.subgroup_id = s.id
            ORDER BY a.account_code
        ");
    }

    public function getBankAccounts()
    {
        return $this->where(['is_bank' => 1, 'status' => 'active'], 'account_name ASC');
    }

    public function getCashAccounts()
    {
        return $this->where(['is_cash' => 1, 'status' => 'active'], 'account_name ASC');
    }

    public function updateBalance($accountId, $amount, $type = 'debit')
    {
        $account = $this->find($accountId);
        if (!$account) return false;

        // Get account nature from group
        $group = $this->db->fetch("SELECT nature FROM account_groups WHERE id = ?", [$account['group_id']]);

        $currentBalance = $account['current_balance'];

        // Debit increases debit accounts, decreases credit accounts
        // Credit increases credit accounts, decreases debit accounts
        if ($group['nature'] == 'debit') {
            $newBalance = $type == 'debit'
                ? $currentBalance + $amount
                : $currentBalance - $amount;
        } else {
            $newBalance = $type == 'credit'
                ? $currentBalance + $amount
                : $currentBalance - $amount;
        }

        return $this->update($accountId, ['current_balance' => $newBalance]);
    }

    public function getBalance($accountId)
    {
        $account = $this->find($accountId);
        return $account ? $account['current_balance'] : 0;
    }

    public function getLedger($accountId, $fromDate = null, $toDate = null)
    {
        $params = ['account_id' => $accountId];
        $dateFilter = '';

        if ($fromDate) {
            $dateFilter .= " AND je.created_at >= :from_date";
            $params['from_date'] = $fromDate;
        }
        if ($toDate) {
            $dateFilter .= " AND je.created_at <= :to_date";
            $params['to_date'] = $toDate . ' 23:59:59';
        }

        return $this->db->fetchAll("
            SELECT
                j.journal_date as date,
                j.journal_number as ref_number,
                'Journal' as type,
                je.description,
                je.debit,
                je.credit
            FROM journal_entries je
            JOIN journals j ON je.journal_id = j.id
            WHERE je.account_id = :account_id
            AND j.status = 'posted'
            {$dateFilter}
            ORDER BY j.journal_date, j.id
        ", $params);
    }
}
