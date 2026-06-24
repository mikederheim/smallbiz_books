<?php
class Controller {
    protected PDO $db;
    protected array $config;
    public function __construct() { $this->db = Database::get(); $this->config = require __DIR__ . '/../../config/config.php'; }
    protected function render(string $view, array $data=[]): void {
        extract($data);
        $config = $this->config;
        $company = $this->currentCompany();
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }
    protected function redirect(string $route): void { header('Location: index.php?r=' . $route); exit; }
    protected function currentCompany(): ?array {
        if (empty($_SESSION['company_id'])) return null;
        $cid = (int)$_SESSION['company_id'];
        if (!Auth::canAccessCompany($cid)) { unset($_SESSION['company_id']); return null; }
        $stmt = $this->db->prepare('SELECT * FROM companies WHERE id=?');
        $stmt->execute([$cid]);
        return $stmt->fetch() ?: null;
    }
    protected function companyId(): int {
        if (empty($_SESSION['company_id'])) $this->redirect('companies');
        $cid = (int)$_SESSION['company_id'];
        if (!Auth::canAccessCompany($cid)) { unset($_SESSION['company_id']); $this->redirect('companies'); }
        return $cid;
    }
    protected function money($n): string { return '$' . number_format((float)$n, 2); }
}
