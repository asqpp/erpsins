-- ===============================================================================
-- ENHANCED ERP SYSTEM - ACCOUNTING MODULE DATABASE SCHEMA
-- ===============================================================================

-- --------------------------------------------------------
-- Chart of Accounts Structure
-- --------------------------------------------------------

CREATE TABLE `account_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('asset','liability','income','expense','equity') NOT NULL,
  `nature` enum('debit','credit') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  FOREIGN KEY (`parent_id`) REFERENCES `account_groups`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `account_subgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`group_id`) REFERENCES `account_groups`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(50) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `group_id` int(11) NOT NULL,
  `subgroup_id` int(11) DEFAULT NULL,
  `opening_balance` decimal(15,2) DEFAULT 0.00,
  `opening_balance_type` enum('debit','credit') DEFAULT 'debit',
  `current_balance` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `is_bank` tinyint(1) DEFAULT 0,
  `is_cash` tinyint(1) DEFAULT 0,
  `bank_details` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_code` (`account_code`),
  KEY `group_id` (`group_id`),
  KEY `subgroup_id` (`subgroup_id`),
  KEY `status` (`status`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`group_id`) REFERENCES `account_groups`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`subgroup_id`) REFERENCES `account_subgroups`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Journal & Transaction Tables (Double Entry Bookkeeping)
-- --------------------------------------------------------

CREATE TABLE `journals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_number` varchar(50) NOT NULL,
  `journal_date` date NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `total_debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `posted_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `posted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `journal_number` (`journal_number`),
  KEY `journal_date` (`journal_date`),
  KEY `status` (`status`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `journal_id` (`journal_id`),
  KEY `account_id` (`account_id`),
  FOREIGN KEY (`journal_id`) REFERENCES `journals`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Receipt & Payment Vouchers
-- --------------------------------------------------------

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(50) NOT NULL,
  `receipt_date` date NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `account_id` int(11) NOT NULL COMMENT 'Cash/Bank Account',
  `amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','cheque','bank_transfer','card','online') NOT NULL DEFAULT 'cash',
  `cheque_number` varchar(50) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `journal_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `receipt_date` (`receipt_date`),
  KEY `customer_id` (`customer_id`),
  KEY `account_id` (`account_id`),
  KEY `status` (`status`),
  KEY `journal_id` (`journal_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`journal_id`) REFERENCES `journals`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `receipt_allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `receipt_id` (`receipt_id`),
  KEY `invoice_id` (`invoice_id`),
  FOREIGN KEY (`receipt_id`) REFERENCES `receipts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payment_vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `payee_type` enum('supplier','employee','other') NOT NULL,
  `payee_id` int(11) DEFAULT NULL,
  `payee_name` varchar(255) DEFAULT NULL,
  `account_id` int(11) NOT NULL COMMENT 'Cash/Bank Account',
  `amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','cheque','bank_transfer','card','online') NOT NULL DEFAULT 'cash',
  `cheque_number` varchar(50) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `journal_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_number` (`payment_number`),
  KEY `payment_date` (`payment_date`),
  KEY `account_id` (`account_id`),
  KEY `status` (`status`),
  KEY `journal_id` (`journal_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`journal_id`) REFERENCES `journals`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Bank Reconciliation
-- --------------------------------------------------------

CREATE TABLE `bank_reconciliation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL COMMENT 'Bank Account',
  `reconciliation_date` date NOT NULL,
  `statement_balance` decimal(15,2) NOT NULL,
  `book_balance` decimal(15,2) NOT NULL,
  `difference` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','reconciled') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `reconciled_by` int(11) DEFAULT NULL,
  `reconciled_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `reconciliation_date` (`reconciliation_date`),
  KEY `status` (`status`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bank_reconciliation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reconciliation_id` int(11) NOT NULL,
  `transaction_type` enum('journal','receipt','payment','invoice') NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `is_reconciled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reconciliation_id` (`reconciliation_id`),
  KEY `transaction_type` (`transaction_type`),
  FOREIGN KEY (`reconciliation_id`) REFERENCES `bank_reconciliation`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Commission Management
-- --------------------------------------------------------

CREATE TABLE `brokers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `commission_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Percentage',
  `account_id` int(11) DEFAULT NULL COMMENT 'Ledger Account',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `status` (`status`),
  KEY `account_id` (`account_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `salesmen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `commission_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Percentage',
  `account_id` int(11) DEFAULT NULL COMMENT 'Ledger Account',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `status` (`status`),
  KEY `account_id` (`account_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `commission_type` enum('broker','salesman') NOT NULL,
  `broker_id` int(11) DEFAULT NULL,
  `salesman_id` int(11) DEFAULT NULL,
  `sale_amount` decimal(15,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `commission_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `broker_id` (`broker_id`),
  KEY `salesman_id` (`salesman_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`broker_id`) REFERENCES `brokers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`salesman_id`) REFERENCES `salesmen`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`payment_id`) REFERENCES `payment_vouchers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Enhanced Invoice Table (Add broker/salesman support)
-- --------------------------------------------------------

ALTER TABLE `invoices`
ADD COLUMN `broker_id` int(11) DEFAULT NULL AFTER `customer_id`,
ADD COLUMN `salesman_id` int(11) DEFAULT NULL AFTER `broker_id`,
ADD COLUMN `journal_id` int(11) DEFAULT NULL AFTER `created_by`,
ADD KEY `broker_id` (`broker_id`),
ADD KEY `salesman_id` (`salesman_id`),
ADD KEY `journal_id` (`journal_id`);

-- --------------------------------------------------------
-- HR & Payroll Tables
-- --------------------------------------------------------

CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `designations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_of_joining` date NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(15,2) DEFAULT 0.00,
  `account_id` int(11) DEFAULT NULL COMMENT 'Salary Payable Account',
  `address` text DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','terminated') NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_code` (`employee_code`),
  KEY `email` (`email`),
  KEY `department_id` (`department_id`),
  KEY `designation_id` (`designation_id`),
  KEY `status` (`status`),
  KEY `account_id` (`account_id`),
  FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`designation_id`) REFERENCES `designations`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('present','absent','half_day','on_leave','holiday') NOT NULL DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_date` (`employee_id`, `attendance_date`),
  KEY `attendance_date` (`attendance_date`),
  KEY `status` (`status`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`marked_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `days_allowed` int(11) DEFAULT 0,
  `carry_forward` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `days` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `status` (`status`),
  KEY `from_date` (`from_date`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `salary_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('earning','deduction') NOT NULL,
  `calculation_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `default_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_number` varchar(50) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `working_days` int(11) DEFAULT 0,
  `present_days` int(11) DEFAULT 0,
  `basic_salary` decimal(15,2) DEFAULT 0.00,
  `gross_salary` decimal(15,2) DEFAULT 0.00,
  `total_deductions` decimal(15,2) DEFAULT 0.00,
  `net_salary` decimal(15,2) DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `status` enum('draft','processed','paid') NOT NULL DEFAULT 'draft',
  `journal_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_number` (`payroll_number`),
  UNIQUE KEY `employee_month_year` (`employee_id`, `month`, `year`),
  KEY `employee_id` (`employee_id`),
  KEY `status` (`status`),
  KEY `journal_id` (`journal_id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`journal_id`) REFERENCES `journals`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`payment_id`) REFERENCES `payment_vouchers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payroll_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payroll_id` (`payroll_id`),
  KEY `component_id` (`component_id`),
  FOREIGN KEY (`payroll_id`) REFERENCES `payroll`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`component_id`) REFERENCES `salary_components`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Sample Data for Account Groups (Chart of Accounts)
-- --------------------------------------------------------

INSERT INTO `account_groups` (`name`, `type`, `nature`, `code`, `is_system`, `status`) VALUES
('Assets', 'asset', 'debit', '1000', 1, 'active'),
('Liabilities', 'liability', 'credit', '2000', 1, 'active'),
('Equity', 'equity', 'credit', '3000', 1, 'active'),
('Income', 'income', 'credit', '4000', 1, 'active'),
('Expenses', 'expense', 'debit', '5000', 1, 'active');

-- Sub-groups for Assets
INSERT INTO `account_groups` (`name`, `type`, `nature`, `parent_id`, `code`, `is_system`) VALUES
('Current Assets', 'asset', 'debit', 1, '1100', 1),
('Fixed Assets', 'asset', 'debit', 1, '1200', 1),
('Accounts Receivable', 'asset', 'debit', 1, '1300', 1);

-- Sub-groups for Liabilities
INSERT INTO `account_groups` (`name`, `type`, `nature`, `parent_id`, `code`, `is_system`) VALUES
('Current Liabilities', 'liability', 'credit', 2, '2100', 1),
('Long-term Liabilities', 'liability', 'credit', 2, '2200', 1),
('Accounts Payable', 'liability', 'credit', 2, '2300', 1);

-- Sub-groups for Income
INSERT INTO `account_groups` (`name`, `type`, `nature`, `parent_id`, `code`, `is_system`) VALUES
('Sales Revenue', 'income', 'credit', 4, '4100', 1),
('Other Income', 'income', 'credit', 4, '4200', 1);

-- Sub-groups for Expenses
INSERT INTO `account_groups` (`name`, `type`, `nature`, `parent_id`, `code`, `is_system`) VALUES
('Operating Expenses', 'expense', 'debit', 5, '5100', 1),
('Cost of Goods Sold', 'expense', 'debit', 5, '5200', 1);

-- --------------------------------------------------------
-- Sample Data for Accounts
-- --------------------------------------------------------

INSERT INTO `accounts` (`account_code`, `account_name`, `group_id`, `opening_balance`, `is_system`, `is_cash`, `status`) VALUES
('1101', 'Cash in Hand', 6, 5000.00, 1, 1, 'active'),
('1102', 'Petty Cash', 6, 500.00, 1, 1, 'active');

INSERT INTO `accounts` (`account_code`, `account_name`, `group_id`, `opening_balance`, `is_system`, `is_bank`, `status`) VALUES
('1103', 'Bank Account - Main', 6, 50000.00, 1, 1, 'active'),
('1104', 'Bank Account - Payroll', 6, 25000.00, 1, 1, 'active');

INSERT INTO `accounts` (`account_code`, `account_name`, `group_id`, `opening_balance`, `is_system`, `status`) VALUES
('1301', 'Accounts Receivable', 8, 0.00, 1, 'active'),
('2301', 'Accounts Payable', 11, 0.00, 1, 'active'),
('3001', 'Capital Account', 3, 100000.00, 1, 'active'),
('3002', 'Retained Earnings', 3, 0.00, 1, 'active'),
('4101', 'Sales Revenue', 12, 0.00, 1, 'active'),
('4201', 'Interest Income', 13, 0.00, 1, 'active'),
('5101', 'Salaries & Wages', 14, 0.00, 1, 'active'),
('5102', 'Rent Expense', 14, 0.00, 1, 'active'),
('5103', 'Utilities', 14, 0.00, 1, 'active'),
('5104', 'Commission Expense', 14, 0.00, 1, 'active'),
('5201', 'Cost of Goods Sold', 15, 0.00, 1, 'active');

-- --------------------------------------------------------
-- Sample Data for HR Module
-- --------------------------------------------------------

INSERT INTO `departments` (`name`, `code`, `status`) VALUES
('Administration', 'ADM', 'active'),
('Sales', 'SAL', 'active'),
('Finance', 'FIN', 'active'),
('IT', 'IT', 'active');

INSERT INTO `designations` (`name`, `status`) VALUES
('Manager', 'active'),
('Executive', 'active'),
('Officer', 'active'),
('Assistant', 'active');

INSERT INTO `leave_types` (`name`, `days_allowed`, `carry_forward`, `status`) VALUES
('Casual Leave', 12, 0, 'active'),
('Sick Leave', 10, 1, 'active'),
('Annual Leave', 20, 1, 'active'),
('Maternity Leave', 90, 0, 'active');

INSERT INTO `salary_components` (`name`, `type`, `calculation_type`, `default_amount`, `status`) VALUES
('House Rent Allowance', 'earning', 'percentage', 40.00, 'active'),
('Medical Allowance', 'earning', 'fixed', 1000.00, 'active'),
('Transport Allowance', 'earning', 'fixed', 800.00, 'active'),
('Tax Deduction', 'deduction', 'percentage', 10.00, 'active'),
('Provident Fund', 'deduction', 'percentage', 10.00, 'active'),
('Insurance', 'deduction', 'fixed', 500.00, 'active');

-- --------------------------------------------------------
-- Sample Data for Brokers and Salesmen
-- --------------------------------------------------------

INSERT INTO `brokers` (`code`, `name`, `email`, `phone`, `commission_rate`, `status`) VALUES
('BRK001', 'Premium Brokers LLC', 'info@premiumbrokers.com', '+1-555-0201', 2.50, 'active'),
('BRK002', 'Elite Insurance Brokers', 'contact@elitebrokers.com', '+1-555-0202', 3.00, 'active');

INSERT INTO `salesmen` (`code`, `name`, `email`, `phone`, `commission_rate`, `status`) VALUES
('SM001', 'Michael Johnson', 'michael.j@company.com', '+1-555-0301', 1.50, 'active'),
('SM002', 'Sarah Williams', 'sarah.w@company.com', '+1-555-0302', 1.75, 'active'),
('SM003', 'David Brown', 'david.b@company.com', '+1-555-0303', 1.50, 'active');

-- --------------------------------------------------------
