<?php
class CustomerController extends Controller {
    public function index(): void {
        $cid = $this->companyId();
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE company_id=? ORDER BY name');
        $stmt->execute([$cid]);
        $customers = $stmt->fetchAll();
        $this->render('customers/index', compact('customers'));
    }

    public function create(): void {
        $customer = ['id'=>null,'name'=>'','email'=>'','phone'=>'','address'=>''];
        $mode = 'create';
        $this->render('customers/form', compact('customer','mode'));
    }

    public function edit(): void {
        $cid = $this->companyId();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE id=? AND company_id=?');
        $stmt->execute([$id, $cid]);
        $customer = $stmt->fetch();
        if (!$customer) die('Customer not found');
        $mode = 'edit';
        $this->render('customers/form', compact('customer','mode'));
    }

    public function save(): void {
        check_csrf();
        $cid = $this->companyId();
        $stmt = $this->db->prepare('INSERT INTO customers(company_id,name,email,phone,address) VALUES(?,?,?,?,?)');
        $stmt->execute([$cid, trim($_POST['name'] ?? ''), trim($_POST['email'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? '')]);
        $this->redirect('customers');
    }

    public function update(): void {
        check_csrf();
        $cid = $this->companyId();
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->db->prepare('UPDATE customers SET name=?, email=?, phone=?, address=? WHERE id=? AND company_id=?');
        $stmt->execute([trim($_POST['name'] ?? ''), trim($_POST['email'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['address'] ?? ''), $id, $cid]);
        $this->redirect('customers');
    }

    public function delete(): void {
        check_csrf();
        $cid = $this->companyId();
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT COUNT(*) AS cnt FROM invoices WHERE customer_id=? AND company_id=?');
        $stmt->execute([$id, $cid]);
        if ((int)$stmt->fetch()['cnt'] > 0) {
            $_SESSION['flash_error'] = 'That customer has invoices and cannot be deleted. Edit the customer instead.';
            $this->redirect('customers');
        }
        $stmt = $this->db->prepare('DELETE FROM customers WHERE id=? AND company_id=?');
        $stmt->execute([$id, $cid]);
        $this->redirect('customers');
    }
}
