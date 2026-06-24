-- User permissions and company access update
-- Run this once against your existing SmallBiz Books database before copying the updated files.

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS is_superuser TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS can_manage_users TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS can_manage_companies TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS can_view_reports TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN IF NOT EXISTS can_manage_transactions TINYINT(1) NOT NULL DEFAULT 1;

CREATE TABLE IF NOT EXISTS user_company_access (
  user_id INT NOT NULL,
  company_id INT NOT NULL,
  PRIMARY KEY (user_id, company_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Preserve existing behavior by giving current users access to all existing companies.
INSERT IGNORE INTO user_company_access(user_id, company_id)
SELECT u.id, c.id FROM users u CROSS JOIN companies c;

-- Mike is the permanent superuser account.
UPDATE users
SET is_superuser = 1,
    can_manage_users = 1,
    can_manage_companies = 1,
    can_view_reports = 1,
    can_manage_transactions = 1
WHERE email = 'mike.derheim@mivent.com';
