<?php
class PeriodLock {
    public static function lockedThrough(PDO $db, int $companyId): ?string {
        try {
            $stmt = $db->prepare('SELECT locked_through FROM period_locks WHERE company_id=? ORDER BY locked_through DESC LIMIT 1');
            $stmt->execute([$companyId]);
            $date = $stmt->fetchColumn();
            return $date ?: null;
        } catch (Throwable $e) { return null; }
    }

    public static function isLocked(PDO $db, int $companyId, ?string $date): bool {
        if (!$date) return false;
        $lockedThrough = self::lockedThrough($db, $companyId);
        return $lockedThrough !== null && $date <= $lockedThrough;
    }

    public static function assertUnlocked(PDO $db, int $companyId, ?string $date, string $thing='transaction'): void {
        if (self::isLocked($db, $companyId, $date)) {
            throw new Exception('This ' . $thing . ' is in a closed accounting period and cannot be changed. Re-open or move the closing date first.');
        }
    }

    public static function assertSourceUnlocked(PDO $db, int $companyId, string $sourceType, int $sourceId): void {
        $stmt = $db->prepare('SELECT entry_date FROM journal_entries WHERE company_id=? AND source_type=? AND source_id=?');
        $stmt->execute([$companyId, $sourceType, $sourceId]);
        foreach ($stmt->fetchAll() as $row) self::assertUnlocked($db, $companyId, $row['entry_date'], 'transaction');
    }
}
