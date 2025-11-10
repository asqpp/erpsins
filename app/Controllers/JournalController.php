<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Journal;
use App\Models\Account;

class JournalController extends Controller
{
    private $journalModel;
    private $accountModel;

    public function __construct()
    {
        parent::__construct();
        $this->journalModel = new Journal();
        $this->accountModel = new Account();
    }

    public function index()
    {
        $journals = $this->db->fetchAll("
            SELECT j.*, u.name as created_by_name
            FROM journals j
            LEFT JOIN users u ON j.created_by = u.id
            ORDER BY j.created_at DESC
        ");

        $this->view('accounting/journals/index', [
            'journals' => $journals,
            'user' => auth()->user()
        ]);
    }

    public function create()
    {
        $accounts = $this->accountModel->where(['status' => 'active'], 'account_name ASC');
        $nextNumber = $this->journalModel->generateJournalNumber();

        $this->view('accounting/journals/create', [
            'accounts' => $accounts,
            'nextNumber' => $nextNumber,
            'user' => auth()->user()
        ]);
    }

    public function store()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $errors = $this->validate($_POST, [
            'journal_date' => 'required',
        ]);

        if (empty($_POST['entries']) || !is_array($_POST['entries'])) {
            $errors['entries'] = 'At least two journal entries are required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            return $this->back();
        }

        // Prepare journal data
        $journalData = [
            'journal_date' => $_POST['journal_date'],
            'reference' => $_POST['reference'] ?? null,
            'description' => $_POST['description'] ?? null,
            'status' => 'draft',
            'created_by' => auth()->id()
        ];

        // Prepare entries
        $entries = [];
        foreach ($_POST['entries'] as $entry) {
            if (!empty($entry['account_id']) && ($entry['debit'] > 0 || $entry['credit'] > 0)) {
                $entries[] = [
                    'account_id' => $entry['account_id'],
                    'description' => $entry['description'] ?? '',
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0
                ];
            }
        }

        try {
            $journalId = $this->journalModel->createWithEntries($journalData, $entries);

            // Auto-post if requested
            if (isset($_POST['post_immediately']) && $_POST['post_immediately'] == '1') {
                $this->journalModel->postJournal($journalId, auth()->id());
                $this->setFlash('success', 'Journal created and posted successfully');
            } else {
                $this->setFlash('success', 'Journal created successfully');
            }

            $this->redirect('/accounting/journals/' . $journalId);
        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to create journal: ' . $e->getMessage());
            return $this->back();
        }
    }

    public function show($id)
    {
        $journal = $this->journalModel->find($id);

        if (!$journal) {
            $this->setFlash('error', 'Journal not found');
            return $this->redirect('/accounting/journals');
        }

        $entries = $this->journalModel->getEntries($id);

        $this->view('accounting/journals/show', [
            'journal' => $journal,
            'entries' => $entries,
            'user' => auth()->user()
        ]);
    }

    public function post($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        try {
            $posted = $this->journalModel->postJournal($id, auth()->id());

            if ($posted) {
                $this->setFlash('success', 'Journal posted successfully');
            } else {
                $this->setFlash('error', 'Failed to post journal');
            }
        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to post journal: ' . $e->getMessage());
        }

        $this->redirect('/accounting/journals/' . $id);
    }

    public function destroy($id)
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== csrf_token()) {
            $this->setFlash('error', 'Invalid request');
            return $this->back();
        }

        $journal = $this->journalModel->find($id);

        if ($journal && $journal['status'] === 'posted') {
            $this->setFlash('error', 'Cannot delete posted journal');
            return $this->back();
        }

        $deleted = $this->journalModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Journal deleted successfully');
        } else {
            $this->setFlash('error', 'Failed to delete journal');
        }

        $this->redirect('/accounting/journals');
    }
}
