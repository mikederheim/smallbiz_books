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

        // Public registration is only allowed for the very first user.
        // After that, user management is handled from the logged-in Users screen.
        if (!$firstUser) {
            Auth::requireLogin();
            $this->redirect('users');
        }

        $error=null;
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');

            if ($name === '' || $email === '' || $password === '') {
                $error = 'Name, email, and password are required.';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } else {
                $stmt=$this->db->prepare('INSERT INTO users(name,email,password_hash) VALUES(?,?,?)');
                try {
                    $stmt->execute([$name,$email,password_hash($password, PASSWORD_DEFAULT)]);
                    Auth::login($email, $password);
                    $this->redirect('companies');
                } catch(Exception $e){
                    $error='Could not create account. Email may already exist.';
                }
            }
        }

        $this->render('layouts/register', compact('error','firstUser'));
    }

    public function users(): void {
        Auth::requireLogin();
        $stmt = $this->db->query('SELECT id, name, email, created_at FROM users ORDER BY name, email');
        $users = $stmt->fetchAll();
        $currentUserId = (int)(Auth::user()['id'] ?? 0);
        $this->render('users/index', compact('users', 'currentUserId'));
    }

    public function createUser(): void {
        Auth::requireLogin();
        $user = ['id'=>null, 'name'=>'', 'email'=>''];
        $error = null;
        $isEdit = false;
        $this->render('users/form', compact('user', 'error', 'isEdit'));
    }

    public function saveUser(): void {
        Auth::requireLogin();
        check_csrf();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $error = 'Name, email, and password are required.';
            $user = ['id'=>null, 'name'=>$name, 'email'=>$email];
            $isEdit = false;
            $this->render('users/form', compact('user', 'error', 'isEdit'));
            return;
        }
        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
            $user = ['id'=>null, 'name'=>$name, 'email'=>$email];
            $isEdit = false;
            $this->render('users/form', compact('user', 'error', 'isEdit'));
            return;
        }

        try {
            $stmt=$this->db->prepare('INSERT INTO users(name,email,password_hash) VALUES(?,?,?)');
            $stmt->execute([$name,$email,password_hash($password, PASSWORD_DEFAULT)]);
            $this->redirect('users');
        } catch(Exception $e) {
            $error='Could not create account. Email may already exist.';
            $user = ['id'=>null, 'name'=>$name, 'email'=>$email];
            $isEdit = false;
            $this->render('users/form', compact('user', 'error', 'isEdit'));
        }
    }

    public function editUser(): void {
        Auth::requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $this->db->prepare('SELECT id, name, email, created_at FROM users WHERE id=?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) die('User not found');
        $error = null;
        $isEdit = true;
        $this->render('users/form', compact('user', 'error', 'isEdit'));
    }

    public function updateUser(): void {
        Auth::requireLogin();
        check_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $stmt = $this->db->prepare('SELECT id, name, email, created_at FROM users WHERE id=?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) die('User not found');

        if ($name === '' || $email === '') {
            $error = 'Name and email are required.';
            $user = ['id'=>$id, 'name'=>$name, 'email'=>$email];
            $isEdit = true;
            $this->render('users/form', compact('user', 'error', 'isEdit'));
            return;
        }
        if ($password !== '' && strlen($password) < 8) {
            $error = 'New password must be at least 8 characters.';
            $user = ['id'=>$id, 'name'=>$name, 'email'=>$email];
            $isEdit = true;
            $this->render('users/form', compact('user', 'error', 'isEdit'));
            return;
        }

        try {
            if ($password !== '') {
                $stmt = $this->db->prepare('UPDATE users SET name=?, email=?, password_hash=? WHERE id=?');
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $id]);
            } else {
                $stmt = $this->db->prepare('UPDATE users SET name=?, email=? WHERE id=?');
                $stmt->execute([$name, $email, $id]);
            }

            // Keep the session display information current if the user edited themself.
            if ((int)(Auth::user()['id'] ?? 0) === $id) {
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
            }

            $this->redirect('users');
        } catch(Exception $e) {
            $error='Could not update account. Email may already exist.';
            $user = ['id'=>$id, 'name'=>$name, 'email'=>$email];
            $isEdit = true;
            $this->render('users/form', compact('user', 'error', 'isEdit'));
        }
    }

    public function deleteUser(): void {
        Auth::requireLogin();
        check_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $currentUserId = (int)(Auth::user()['id'] ?? 0);

        $count = (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count <= 1) {
            $_SESSION['flash_error'] = 'You cannot delete the only user account.';
            $this->redirect('users');
        }
        if ($id === $currentUserId) {
            $_SESSION['flash_error'] = 'You cannot delete the account you are currently logged in as.';
            $this->redirect('users');
        }

        $stmt = $this->db->prepare('DELETE FROM users WHERE id=?');
        $stmt->execute([$id]);
        $this->redirect('users');
    }

    public function logout(): void { Auth::logout(); $this->redirect('login'); }
}
