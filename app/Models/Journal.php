<?php

namespace App\Models;

use Core\Model;

class Journal extends Model
{
    protected $table = 'journals';
    protected $fillable = [
        'journal_number', 'journal_date', 'reference', 'description',
        'total_debit', 'total_credit', 'status', 'posted_date',
        'created_by', 'posted_by'
    ];

    public function getEntries($journalId)
    {
        return $this->db->fetchAll("
            SELECT je.*, a.account_code, a.account_name
            FROM journal_entries je
            LEFT JOIN accounts a ON je.account_id = a.id
            WHERE je.journal_id = :journal_id
            ORDER BY je.id
        ", ['journal_id' => $journalId]);
    }

    public function createWithEntries($journalData, $entries)
    {
        $this->db->beginTransaction();

        try {
            // Generate journal number
            $lastJournal = $this->db->fetch("SELECT journal_number FROM journals ORDER BY id DESC LIMIT 1");
            if ($lastJournal) {
                $lastNumber = (int)substr($lastJournal['journal_number'], -5);
                $journalData['journal_number'] = 'JV-' . date('Y') . '-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $journalData['journal_number'] = 'JV-' . date('Y') . '-00001';
            }

            // Calculate totals
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($entries as $entry) {
                $totalDebit += $entry['debit'];
                $totalCredit += $entry['credit'];
            }

            // Validate balanced entry
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception("Journal entries must be balanced. Debit: $totalDebit, Credit: $totalCredit");
            }

            $journalData['total_debit'] = $totalDebit;
            $journalData['total_credit'] = $totalCredit;

            // Create journal
            $journalId = $this->create($journalData);

            // Create entries
            foreach ($entries as $entry) {
                $this->db->insert('journal_entries', [
                    'journal_id' => $journalId,
                    'account_id' => $entry['account_id'],
                    'description' => $entry['description'] ?? '',
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit']
                ]);
            }

            $this->db->commit();
            return $journalId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function postJournal($journalId, $userId)
    {
        $journal = $this->find($journalId);
        if (!$journal || $journal['status'] !== 'draft') {
            return false;
        }

        $this->db->beginTransaction();

        try {
            // Get entries
            $entries = $this->getEntries($journalId);

            // Update account balances
            $accountModel = new Account();
            foreach ($entries as $entry) {
                if ($entry['debit'] > 0) {
                    $accountModel->updateBalance($entry['account_id'], $entry['debit'], 'debit');
                }
                if ($entry['credit'] > 0) {
                    $accountModel->updateBalance($entry['account_id'], $entry['credit'], 'credit');
                }
            }

            // Update journal status
            $this->update($journalId, [
                'status' => 'posted',
                'posted_date' => date('Y-m-d H:i:s'),
                'posted_by' => $userId
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function generateJournalNumber()
    {
        $lastJournal = $this->db->fetch("SELECT journal_number FROM journals ORDER BY id DESC LIMIT 1");

        if ($lastJournal) {
            $lastNumber = (int)substr($lastJournal['journal_number'], -5);
            return 'JV-' . date('Y') . '-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return 'JV-' . date('Y') . '-00001';
    }
}
