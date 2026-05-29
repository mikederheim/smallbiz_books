# SmallBiz Books

A lightweight PHP/MySQL accounting MVP for multiple small businesses.

## What it includes

- Multi-company support
- User login
- Chart of accounts
- Customers and vendors
- Invoices and invoice payments
- Bills and bill payments
- Double-entry journal posting
- Manual journal entry screen
- Dashboard
- Profit & Loss report
- Balance Sheet report
- Customer insights report
- Vendor insights report

## What it does not yet include

- Payroll
- Bank-feed integrations
- Payment processing
- Inventory costing
- Sales tax filing
- Accountant/audit workflows
- PDF invoice generation
- Multi-user permissions beyond login
- Automatic backups

## Installation

1. Create a MySQL database/user on your server.
2. Import `database/schema.sql` into MySQL.
3. Copy `config/config.example.php` to `config/config.php` if needed.
4. Edit `config/config.php` with your database credentials.
5. Point your web server document root to the `public` folder.
6. Visit `/public/index.php?r=register` and create the first user.
7. Add a company. Default accounts will be created automatically.

## Server requirements

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- PDO MySQL extension enabled
- HTTPS strongly recommended

## Important note

This is a starter MVP and should be reviewed before relying on it as your official accounting system. Back up your database regularly and have your accountant verify setup, chart of accounts, and reporting logic.

## 2026-05 transaction/report update

This build adds:

- Edit/delete for manual journal entries.
- Edit/delete for invoices and bills. When an invoice or bill is edited, its source journal entry is rebuilt so the ledger stays balanced.
- Delete for invoices and bills, including related source ledger entries and payments.
- Printable Profit & Loss report with a Print button.
- Profit & Loss Excel download using an `.xls` HTML table export that opens in Microsoft Excel.
- Month-to-date income, expenses, and profit/loss dashboard cards.
- Additional safeguards so accounts and reports remain company-specific.

### Notes on accounting rules

Manual journal entries can only be saved if debits equal credits, each line has either a debit or credit, and every account belongs to the selected company.

Journal entries created by invoices, bills, and payments are not edited directly from the Journal screen. Edit or delete the original invoice or bill instead.

### Existing installations

Replace the PHP/CSS files with this package. The original schema already contains company-specific accounts via `accounts.company_id`. An optional upgrade helper is included at:

`database/upgrade_2026_05_transactions_reports.sql`

Back up your database before running any upgrade SQL. If an index already exists, your MySQL version may report a duplicate-index warning/error; that is generally safe to ignore.


## 2026-05 login-required user creation update

Registration is public only when there are no users in the database. After the first user exists, a logged-in user must create any additional users from the Add User link in the sidebar.
