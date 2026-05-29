<?php
class AuthController extends Controller {
    public function login(): void {
        $error=null;
        if ($_SERVER['REQUEST_METHOD']==='POST') { check_csrf(); if (Auth::login($_POST['email'], $_POST['password'])) $this->redirect('dashboard'); $error='Invalid email or password'; }
        $this->render('layouts/login', compact('error'));
    }
    public function register(): void {
        $error=null;
        if ($_SERVER['REQUEST_METHOD']==='POST') { check_csrf();
            $stmt=$this->db->prepare('INSERT INTO users(name,email,password_hash) VALUES(?,?,?)');
            try { $stmt->execute([$_POST['name'],$_POST['email'],password_hash($_POST['password'], PASSWORD_DEFAULT)]); Auth::login($_POST['email'], $_POST['password']); $this->redirect('companies'); }
            catch(Exception $e){ $error='Could not create account. Email may already exist.'; }
        }
        $this->render('layouts/register', compact('error'));
    }
    public function logout(): void { Auth::logout(); $this->redirect('login'); }
}
