CREATE TABLE IF NOT EXISTS bank_reconciliations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  account_id INT NOT NULL,
  statement_date DATE NOT NULL,
  beginning_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
  ending_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
  cleared_debits DECIMAL(12,2) NOT NULL DEFAULT 0,
  cleared_credits DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
  FOREIGN KEY (account_id) REFERENCES accounts(id),
  KEY idx_reconcile_company_account_date (company_id, account_id, statement_date)
);

CREATE TABLE IF NOT EXISTS bank_reconciliation_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reconciliation_id INT NOT NULL,
  journal_line_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_reconciled_line (journal_line_id),
  FOREIGN KEY (reconciliation_id) REFERENCES bank_reconciliations(id) ON DELETE CASCADE,
  FOREIGN KEY (journal_line_id) REFERENCES journal_lines(id) ON DELETE CASCADE
);
