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

        // Security rule: after the first account exists, only logged-in users may create more users.
        if (!$firstUser) {
            Auth::requireLogin();
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

                    if ($firstUser) {
                        Auth::login($email, $password);
                        $this->redirect('companies');
                    }

                    $this->redirect('dashboard');
                } catch(Exception $e){
                    $error='Could not create account. Email may already exist.';
                }
            }
        }

        $this->render('layouts/register', compact('error','firstUser'));
    }

    public function logout(): void { Auth::logout(); $this->redirect('login'); }
}
