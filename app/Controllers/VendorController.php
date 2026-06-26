<?php
class VendorController extends Controller {
    public function index(): void {
        $cid = $this->companyId();
        $stmt = $this->db->prepare('SELECT * FROM vendors WHERE company_id=? ORDER BY name');
        $stmt->execute([$cid]);
        $vendors = $stmt->fetchAll();
        $this->render('vendors/index', compact('vendors'));
    }

    public function create(): void {
        $vendor = ['id'=>null,'name'=>'','email'=>'','phone'=>'','address'=>''];
        $mode = 'create';
        $this->render('vendors/form', compact('vendor','mode'));
    }

    public function edit(): void {
        $cid = $this->companyId();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT * FROM vendors WHERE id=? AND company_id=?');
        $stmt->execute([$id, $cid]);
        $vendor = $stmt->fetch();
        if (!$vendor) die('Vendor not found');
        $mode = 'edit';
        $this->render('vendors/form', compact('vendor','mode'));
    }

    public function save(): void {
        check_csrf();
        $cid = $this->companyId();
        $stmt = $this->db->prepare('INSERT INTO vendors(company_id,name,email,phone,address) VALUES(?,?,?,?,?)');
        $stmt->execute([$cid, trim($_POST['name'] ?? ''), trim($_POST['email'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? '')]);
        $this->redirect('vendors');
    }

    public function update(): void {
        check_csrf();
        $cid = $this->companyId();
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->db->prepare('UPDATE vendors SET name=?, email=?, phone=?, address=? WHERE id=? AND company_id=?');
        $stmt->execute([trim($_POST['name'] ?? ''), trim($_POST['email'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), $id, $cid]);
        $this->redirect('vendors');
    }

    public function delete(): void {
        check_csrf();
        $cid = $this->companyId();
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT COUNT(*) AS cnt FROM bills WHERE vendor_id=? AND company_id=?');
        $stmt->execute([$id, $cid]);
        if ((int)$stmt->fetch()['cnt'] > 0) {
            $_SESSION['flash_error'] = 'That vendor has bills and cannot be deleted. Edit the vendor instead.';
            $this->redirect('vendors');
        }
        $stmt = $this->db->prepare('DELETE FROM vendors WHERE id=? AND company_id=?');
        $stmt->execute([$id, $cid]);
        $this->redirect('vendors');
    }
}
