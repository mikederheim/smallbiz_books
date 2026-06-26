<?php
class AuditTrail {
    public static function log(PDO $db, int $companyId, string $action, string $entityType, ?int $entityId=null, string $description='', ?array $metadata=null): void {
        try {
            $user = Auth::user();
            $stmt = $db->prepare('INSERT INTO audit_trail(company_id,user_id,action,entity_type,entity_id,description,metadata,ip_address,user_agent) VALUES(?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $companyId,
                $user['id'] ?? null,
                $action,
                $entityType,
                $entityId,
                $description,
                $metadata ? json_encode($metadata) : null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (Throwable $e) {
            // Audit logging should never prevent normal bookkeeping from completing.
        }
    }
}
