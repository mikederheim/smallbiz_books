CREATE TABLE IF NOT EXISTS audit_trail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  user_id INT NULL,
  action VARCHAR(50) NOT NULL,
  entity_type VARCHAR(80) NOT NULL,
  entity_id INT NULL,
  description TEXT NULL,
  metadata JSON NULL,
  ip_address VARCHAR(64) NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_company_created (company_id, created_at),
  INDEX idx_audit_entity (company_id, entity_type, entity_id),
  CONSTRAINT fk_audit_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS period_locks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  locked_through DATE NOT NULL,
  locked_by INT NULL,
  note VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_period_locks_company_date (company_id, locked_through),
  CONSTRAINT fk_period_locks_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
  CONSTRAINT fk_period_locks_user FOREIGN KEY (locked_by) REFERENCES users(id) ON DELETE SET NULL
);
