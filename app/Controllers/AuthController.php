<?php
class AuthController extends Controller {
    public function login(): void {
        $error=null;
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            if (Auth::login($_POST['email'], $_POST['password'])) $this->redirect('dashboard');
            $error='Invalid email or password';
        }
        $this->render('layouts/login', compact('error'));
    }

    public function register(): void {
        $firstUser = !Auth::hasUsers();
        if (!$firstUser) { Auth::requireLogin(); $this->redirect('users'); }
        $error=null;
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');
            if ($name === '' || $email === '' || $password === '') $error = 'Name, email, and password are required.';
            elseif (strlen($password) < 8) $error = 'Password must be at least 8 characters.';
            else {
                $isMike = strtolower($email) === 'mike.derheim@mivent.com' ? 1 : 0;
                $stmt=$this->db->prepare('INSERT INTO users(name,email,password_hash,is_superuser,can_manage_users,can_manage_companies,can_view_reports,can_manage_transactions) VALUES(?,?,?,?,?,?,?,?)');
                try {
                    $stmt->execute([$name,$email,password_hash($password, PASSWORD_DEFAULT),$isMike,1,1,1,1]);
                    Auth::login($email, $password);
                    $this->redirect('companies');
                } catch(Exception $e){ $error='Could not create account. Email may already exist.'; }
            }
        }
        $this->render('layouts/register', compact('error','firstUser'));
    }

    public function users(): void {
        Auth::requirePermission('can_manage_users');
        $stmt = $this->db->query('SELECT id, name, email, created_at, is_superuser, can_manage_users, can_manage_companies, can_view_reports, can_manage_transactions FROM users ORDER BY name, email');
        $users = $stmt->fetchAll();
        $currentUserId = (int)(Auth::user()['id'] ?? 0);
        $this->render('users/index', compact('users', 'currentUserId'));
    }

    private function companies(): array { return $this->db->query('SELECT id,name FROM companies ORDER BY name')->fetchAll(); }
    private function userCompanyIds(int $id): array { $s=$this->db->prepare('SELECT company_id FROM user_company_access WHERE user_id=?'); $s->execute([$id]); return array_map('intval', $s->fetchAll(PDO::FETCH_COLUMN)); }

    public function createUser(): void {
        Auth::requirePermission('can_manage_users');
        $user = ['id'=>null, 'name'=>'', 'email'=>'', 'is_superuser'=>0, 'can_manage_users'=>0, 'can_manage_companies'=>0, 'can_view_reports'=>1, 'can_manage_transactions'=>1];
        $companies = $this->companies(); $userCompanyIds=[]; $error = null; $isEdit = false;
        $this->render('users/form', compact('user', 'error', 'isEdit','companies','userCompanyIds'));
    }

    public function saveUser(): void {
        Auth::requirePermission('can_manage_users'); check_csrf();
        $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $password=(string)($_POST['password']??'');
        $isMike = strtolower($email) === 'mike.derheim@mivent.com';
        $perms = $this->postedPerms($isMike); $companyIds = array_map('intval', $_POST['company_ids'] ?? []);
        if ($name==='' || $email==='' || $password==='') { $this->userFormError('Name, email, and password are required.', false, ['id'=>null,'name'=>$name,'email'=>$email]+$perms, $companyIds); return; }
        if (strlen($password)<8) { $this->userFormError('Password must be at least 8 characters.', false, ['id'=>null,'name'=>$name,'email'=>$email]+$perms, $companyIds); return; }
        try {
            $stmt=$this->db->prepare('INSERT INTO users(name,email,password_hash,is_superuser,can_manage_users,can_manage_companies,can_view_reports,can_manage_transactions) VALUES(?,?,?,?,?,?,?,?)');
            $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$perms['is_superuser'],$perms['can_manage_users'],$perms['can_manage_companies'],$perms['can_view_reports'],$perms['can_manage_transactions']]);
            $this->saveCompanyAccess((int)$this->db->lastInsertId(), $companyIds, (bool)$perms['is_superuser']);
            $this->redirect('users');
        } catch(Exception $e){ $this->userFormError('Could not create account. Email may already exist.', false, ['id'=>null,'name'=>$name,'email'=>$email]+$perms, $companyIds); }
    }

    public function editUser(): void {
        Auth::requirePermission('can_manage_users');
        $id=(int)($_GET['id']??0);
        $stmt=$this->db->prepare('SELECT id, name, email, created_at, is_superuser, can_manage_users, can_manage_companies, can_view_reports, can_manage_transactions FROM users WHERE id=?');
        $stmt->execute([$id]); $user=$stmt->fetch(); if(!$user) die('User not found');
        if (strtolower($user['email']) === 'mike.derheim@mivent.com') $user['is_superuser']=1;
        $companies=$this->companies(); $userCompanyIds=$this->userCompanyIds($id); $error=null; $isEdit=true;
        $this->render('users/form', compact('user','error','isEdit','companies','userCompanyIds'));
    }

    public function updateUser(): void {
        Auth::requirePermission('can_manage_users'); check_csrf();
        $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $password=(string)($_POST['password']??'');
        $old=$this->db->prepare('SELECT * FROM users WHERE id=?'); $old->execute([$id]); $existing=$old->fetch(); if(!$existing) die('User not found');
        $isMike = strtolower($email) === 'mike.derheim@mivent.com' || strtolower($existing['email']) === 'mike.derheim@mivent.com';
        $perms = $this->postedPerms($isMike); $companyIds = array_map('intval', $_POST['company_ids'] ?? []);
        if ($name==='' || $email==='') { $this->userFormError('Name and email are required.', true, ['id'=>$id,'name'=>$name,'email'=>$email]+$perms, $companyIds); return; }
        if ($password!=='' && strlen($password)<8) { $this->userFormError('New password must be at least 8 characters.', true, ['id'=>$id,'name'=>$name,'email'=>$email]+$perms, $companyIds); return; }
        try {
            if ($password !== '') {
                $stmt=$this->db->prepare('UPDATE users SET name=?, email=?, password_hash=?, is_superuser=?, can_manage_users=?, can_manage_companies=?, can_view_reports=?, can_manage_transactions=? WHERE id=?');
                $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$perms['is_superuser'],$perms['can_manage_users'],$perms['can_manage_companies'],$perms['can_view_reports'],$perms['can_manage_transactions'],$id]);
            } else {
                $stmt=$this->db->prepare('UPDATE users SET name=?, email=?, is_superuser=?, can_manage_users=?, can_manage_companies=?, can_view_reports=?, can_manage_transactions=? WHERE id=?');
                $stmt->execute([$name,$email,$perms['is_superuser'],$perms['can_manage_users'],$perms['can_manage_companies'],$perms['can_view_reports'],$perms['can_manage_transactions'],$id]);
            }
            $this->saveCompanyAccess($id, $companyIds, (bool)$perms['is_superuser']);
            if ((int)(Auth::user()['id'] ?? 0) === $id) Auth::login($email, $password ?: '__keep_session_perms_refresh_not_possible__');
            if ((int)(Auth::user()['id'] ?? 0) === $id) { $_SESSION['user']['name']=$name; $_SESSION['user']['email']=$email; $_SESSION['user']=array_merge($_SESSION['user'],$perms); }
            $this->redirect('users');
        } catch(Exception $e){ $this->userFormError('Could not update account. Email may already exist.', true, ['id'=>$id,'name'=>$name,'email'=>$email]+$perms, $companyIds); }
    }

    public function deleteUser(): void {
        Auth::requirePermission('can_manage_users'); check_csrf();
        $id=(int)($_POST['id']??0); $currentUserId=(int)(Auth::user()['id']??0);
        $stmt=$this->db->prepare('SELECT email FROM users WHERE id=?'); $stmt->execute([$id]); $email=(string)$stmt->fetchColumn();
        if (strtolower($email)==='mike.derheim@mivent.com') { $_SESSION['flash_error']='The superuser account cannot be removed.'; $this->redirect('users'); }
        $count=(int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if($count<=1){ $_SESSION['flash_error']='You cannot delete the only user account.'; $this->redirect('users'); }
        if($id===$currentUserId){ $_SESSION['flash_error']='You cannot delete the account you are currently logged in as.'; $this->redirect('users'); }
        $stmt=$this->db->prepare('DELETE FROM users WHERE id=?'); $stmt->execute([$id]); $this->redirect('users');
    }

    private function postedPerms(bool $forceSuper): array {
        if ($forceSuper) return ['is_superuser'=>1,'can_manage_users'=>1,'can_manage_companies'=>1,'can_view_reports'=>1,'can_manage_transactions'=>1];
        return [
            'is_superuser'=>!empty($_POST['is_superuser']) ? 1 : 0,
            'can_manage_users'=>!empty($_POST['can_manage_users']) ? 1 : 0,
            'can_manage_companies'=>!empty($_POST['can_manage_companies']) ? 1 : 0,
            'can_view_reports'=>!empty($_POST['can_view_reports']) ? 1 : 0,
            'can_manage_transactions'=>!empty($_POST['can_manage_transactions']) ? 1 : 0,
        ];
    }
    private function saveCompanyAccess(int $userId, array $companyIds, bool $superuser): void {
        $this->db->prepare('DELETE FROM user_company_access WHERE user_id=?')->execute([$userId]);
        if ($superuser) { $companyIds=$this->db->query('SELECT id FROM companies')->fetchAll(PDO::FETCH_COLUMN); }
        $stmt=$this->db->prepare('INSERT IGNORE INTO user_company_access(user_id, company_id) VALUES(?,?)');
        foreach($companyIds as $cid) if((int)$cid>0) $stmt->execute([$userId,(int)$cid]);
    }
    private function userFormError(string $error, bool $isEdit, array $user, array $userCompanyIds): void { $companies=$this->companies(); $this->render('users/form', compact('user','error','isEdit','companies','userCompanyIds')); }

    public function logout(): void { Auth::logout(); $this->redirect('login'); }
}
