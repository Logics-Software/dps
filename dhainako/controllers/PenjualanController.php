<?php
class PenjualanController extends Controller {
    private $headerModel;
    private $detailModel;

    public function __construct() {
        $this->headerModel = new Headerpenjualan();
        $this->detailModel = new Detailpenjualan();
    }

    public function index() {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $currentUser = Auth::user();
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPageOptions = [10, 20, 50, 75, 100, 200, 500];
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $search = trim($_GET['search'] ?? '');
        $periode = $_GET['periode'] ?? 'today';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $statuspkp = $_GET['statuspkp'] ?? null;

        $options = [
            'page' => $page,
            'per_page' => $perPage,
            'search' => $search,
            'periode' => $periode,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'statuspkp' => $statuspkp,
        ];

        // Jika role adalah sales, filter hanya data penjualan dari sales tersebut
        if (Auth::isSales() && !empty($currentUser['kodesales'])) {
            $options['kodesales'] = $currentUser['kodesales'];
        }

        $penjualan = $this->headerModel->getAll($options);
        $total = $this->headerModel->count($options);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        $data = [
            'penjualan' => $penjualan,
            'page' => $page,
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
            'totalPages' => $totalPages,
            'total' => $total,
            'search' => $search,
            'periode' => $periode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statuspkp' => $statuspkp,
        ];

        $this->view('penjualan/index', $data);
    }

    public function show($nopenjualan) {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $currentUser = Auth::user();
        $header = $this->headerModel->findByNopenjualan($nopenjualan);
        if (!$header) {
            Session::flash('error', 'Data penjualan tidak ditemukan');
            $this->redirect('/penjualan');
        }

        // Jika role adalah sales, pastikan hanya bisa melihat penjualan mereka sendiri
        if (Auth::isSales() && !empty($currentUser['kodesales'])) {
            if ($header['kodesales'] !== $currentUser['kodesales']) {
                Session::flash('error', 'Anda tidak memiliki akses untuk melihat data penjualan ini');
                $this->redirect('/penjualan');
            }
        }

        $details = $this->detailModel->getByNopenjualan($nopenjualan);

        $data = [
            'penjualan' => $header,
            'details' => $details
        ];

        $this->view('penjualan/view', $data);
    }
}


