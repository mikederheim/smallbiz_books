<?php
class Ledger {
    public static function validateLines(array $lines): void {
        $debits = round(array_sum(array_map(fn($l)=>(float)($l['debit'] ?? 0), $lines)), 2);
        $credits = round(array_sum(array_map(fn($l)=>(float)($l['credit'] ?? 0), $lines)), 2);
        if (count($lines) < 2) throw new Exception('A journal entry needs at least two lines.');
        if ($debits !== $credits) throw new Exception('Journal entry is not balanced.');
    }

    public static function post(PDO $db, int $companyId, string $date, string $memo, array $lines, ?string $sourceType=null, ?int $sourceId=null): int {
        PeriodLock::assertUnlocked($db, $companyId, $date, 'transaction');
        self::validateLines($lines);
        $stmt = $db->prepare('INSERT INTO journal_entries(company_id, entry_date, memo, source_type, source_id) VALUES(?,?,?,?,?)');
        $stmt->execute([$companyId,$date,$memo,$sourceType,$sourceId]);
        $entryId = (int)$db->lastInsertId();
        self::insertLines($db, $entryId, $lines);
        return $entryId;
    }

    public static function replaceEntry(PDO $db, int $companyId, int $entryId, string $date, string $memo, array $lines): void {
        self::validateLines($lines);
        $stmt = $db->prepare('SELECT id, entry_date, source_type FROM journal_entries WHERE id=? AND company_id=?');
        $stmt->execute([$entryId, $companyId]);
        $entry = $stmt->fetch();
        if (!$entry) throw new Exception('Journal entry not found.');
        PeriodLock::assertUnlocked($db, $companyId, $entry['entry_date'], 'transaction');
        PeriodLock::assertUnlocked($db, $companyId, $date, 'transaction');
        if (($entry['source_type'] ?? '') !== 'manual_journal') throw new Exception('Only manual journal entries can be edited here. Edit the original invoice or bill instead.');
        $db->prepare('UPDATE journal_entries SET entry_date=?, memo=? WHERE id=? AND company_id=?')->execute([$date,$memo,$entryId,$companyId]);
        $db->prepare('DELETE FROM journal_lines WHERE journal_entry_id=?')->execute([$entryId]);
        self::insertLines($db, $entryId, $lines);
    }

    public static function deleteManualEntry(PDO $db, int $companyId, int $entryId): void {
        $stmt = $db->prepare('SELECT id, entry_date, source_type FROM journal_entries WHERE id=? AND company_id=?');
        $stmt->execute([$entryId, $companyId]);
        $entry = $stmt->fetch();
        if (!$entry) throw new Exception('Journal entry not found.');
        PeriodLock::assertUnlocked($db, $companyId, $entry['entry_date'], 'transaction');
        if (($entry['source_type'] ?? '') !== 'manual_journal') throw new Exception('Only manual journal entries can be deleted here. Delete or edit the original invoice or bill instead.');
        $db->prepare('DELETE FROM journal_entries WHERE id=? AND company_id=?')->execute([$entryId,$companyId]);
    }

    public static function deleteBySource(PDO $db, int $companyId, string $sourceType, int $sourceId): void {
        PeriodLock::assertSourceUnlocked($db, $companyId, $sourceType, $sourceId);
        $stmt = $db->prepare('DELETE FROM journal_entries WHERE company_id=? AND source_type=? AND source_id=?');
        $stmt->execute([$companyId, $sourceType, $sourceId]);
    }

    private static function insertLines(PDO $db, int $entryId, array $lines): void {
        $lineStmt = $db->prepare('INSERT INTO journal_lines(journal_entry_id, account_id, debit, credit, description) VALUES(?,?,?,?,?)');
        foreach ($lines as $l) {
            $lineStmt->execute([$entryId,$l['account_id'],$l['debit'] ?? 0,$l['credit'] ?? 0,$l['description'] ?? null]);
        }
    }
}
