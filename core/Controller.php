<?php
class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            // Extract data to make variables available in view
            extract($data, EXTR_SKIP);
            require $viewFile;
        } else {
            die("View not found: {$view}");
        }
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Set success message and redirect
     */
    protected function redirectWithSuccess($url, $message) {
        Message::success($message);
        $this->redirect($url);
    }
    
    /**
     * Set error message and redirect
     */
    protected function redirectWithError($url, $message) {
        Message::error($message);
        $this->redirect($url);
    }
}

