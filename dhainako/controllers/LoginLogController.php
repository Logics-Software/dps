<?php
class LoginLogController extends Controller {
    public function index() {
        Auth::requireRole(['admin', 'manajemen']);

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $sortBy = $_GET['sort_by'] ?? 'login_at';
        $sortOrder = $_GET['sort_order'] ?? 'DESC';

        $validPerPage = [10, 20, 40, 50, 100, 200, 500];
        if (!in_array($perPage, $validPerPage)) {
            $perPage = 20;
        }

        $loginLogModel = new LoginLog();
        $result = $loginLogModel->getAll($page, $perPage, $search, $status, $dateFrom, $dateTo, $sortBy, $sortOrder);

        $totalPages = $perPage > 0 ? (int)ceil($result['total'] / $perPage) : 1;

        $data = [
            'logs' => $result['data'],
            'page' => $page,
            'perPage' => $perPage,
            'total' => $result['total'],
            'totalPages' => $totalPages,
            'search' => $search,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortBy' => $sortBy,
            'sortOrder' => strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC',
            'validPerPage' => $validPerPage
        ];

        $this->view('loginlog/index', $data);
    }
}

