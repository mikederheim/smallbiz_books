<?php
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function route($r): string { return 'index.php?r=' . urlencode($r); }
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function check_csrf(): void { if(($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) die('Invalid CSRF token'); }
