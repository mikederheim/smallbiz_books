-- Upgrade script for existing SmallBiz Books installs.
-- Run this after backing up your database, then replace the PHP files with this package.

-- The app already uses company_id on accounts. These statements make sure the key/index exists.
ALTER TABLE accounts ADD INDEX idx_accounts_company_type (company_id, type);
ALTER TABLE journal_entries ADD INDEX idx_journal_company_source (company_id, source_type, source_id);
ALTER TABLE journal_entries ADD INDEX idx_journal_company_date (company_id, entry_date);
ALTER TABLE invoices ADD INDEX idx_invoices_company_customer (company_id, customer_id);
ALTER TABLE bills ADD INDEX idx_bills_company_vendor (company_id, vendor_id);
ALTER TABLE payments ADD INDEX idx_payments_company_invoice (company_id, invoice_id);
