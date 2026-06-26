# Smallbooks Update: Audit Trail, Account Registers, Period Closing

## Install
1. Back up your files and database.
2. Run `database/upgrade_2026_06_audit_register_periods.sql` in MySQL.
3. Copy the included files over your existing Smallbooks install, preserving paths.
4. Log out and back in.

## Adds
- Audit Trail report for creates/updates/deletes/payments/period closing.
- Account Register link from Chart of Accounts with running balances and printable output.
- Period Closing screen to lock all transactions dated on or before the close date.
- Locked-period safeguards in invoice, bill, payment, and journal-entry posting/deleting.

## Notes
- Audit trail starts recording new changes after this update is installed.
- Re-opening a closed period is available from the Period Closing screen.
