<?php
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function route($r, array $params=[]): string {
    $query = array_merge(['r' => $r], $params);
    return 'index.php?' . http_build_query($query);
}
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function check_csrf(): void { if(($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) die('Invalid CSRF token'); }
