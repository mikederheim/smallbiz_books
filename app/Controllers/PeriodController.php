<?php
class PeriodController extends Controller {
    public function index(): void {
        $cid = $this->companyId();
        $lockedThrough = PeriodLock::lockedThrough($this->db, $cid);
        $stmt = $this->db->prepare('SELECT pl.*, u.name user_name, u.email user_email FROM period_locks pl LEFT JOIN users u ON u.id=pl.locked_by WHERE pl.company_id=? ORDER BY pl.locked_through DESC, pl.id DESC');
        $stmt->execute([$cid]);
        $locks = $stmt->fetchAll();
        $this->render('periods/index', compact('lockedThrough','locks'));
    }

    public function close(): void {
        check_csrf();
        $cid = $this->companyId();
        $date = $_POST['locked_through'] ?? '';
        if (!$date) die('Closing date is required.');
        $user = Auth::user();
        $stmt = $this->db->prepare('INSERT INTO period_locks(company_id,locked_through,locked_by,note) VALUES(?,?,?,?)');
        $stmt->execute([$cid,$date,$user['id'] ?? null,$_POST['note'] ?? null]);
        AuditTrail::log($this->db,$cid,'closed','period_lock',null,'Closed books through '.$date);
        $this->redirect('periods');
    }

    public function unlock(): void {
        check_csrf();
        $cid = $this->companyId();
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT * FROM period_locks WHERE id=? AND company_id=?');
        $stmt->execute([$id,$cid]);
        $lock = $stmt->fetch();
        if (!$lock) die('Period lock not found.');
        $this->db->prepare('DELETE FROM period_locks WHERE id=? AND company_id=?')->execute([$id,$cid]);
        AuditTrail::log($this->db,$cid,'reopened','period_lock',$id,'Removed period close through '.$lock['locked_through']);
        $this->redirect('periods');
    }
}
