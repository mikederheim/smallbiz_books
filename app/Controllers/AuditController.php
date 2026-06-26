<?php
class AuditController extends Controller {
    public function index(): void {
        $cid = $this->companyId();
        $stmt = $this->db->prepare('SELECT at.*, u.name user_name, u.email user_email FROM audit_trail at LEFT JOIN users u ON u.id=at.user_id WHERE at.company_id=? ORDER BY at.created_at DESC, at.id DESC LIMIT 300');
        $stmt->execute([$cid]);
        $events = $stmt->fetchAll();
        $this->render('audit/index', compact('events'));
    }
}
