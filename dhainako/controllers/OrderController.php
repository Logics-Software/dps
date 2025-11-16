<?php
class OrderController extends Controller {
    private $headerModel;
    private $detailModel;
    private $customerModel;
    private $barangModel;

    public function __construct() {
        parent::__construct();
        $this->headerModel = new Headerorder();
        $this->detailModel = new Detailorder();
        $this->customerModel = new Mastercustomer();
        $this->barangModel = new Masterbarang();
    }

    public function index() {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $user = Auth::user();
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $perPage = in_array($perPage, [10, 20, 40, 60, 100]) ? $perPage : 10;
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $dateFilter = $_GET['date_filter'] ?? 'today';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';

        [$computedStartDate, $computedEndDate] = $this->computeDateRange($dateFilter, $startDate, $endDate);

        $options = [
            'page' => $page,
            'per_page' => $perPage,
            'search' => $search,
            'status' => $status,
            'start_date' => $computedStartDate,
            'end_date' => $computedEndDate,
            'sort_by' => 'tanggalorder',
            'sort_order' => 'DESC'
        ];

        if (($user['role'] ?? '') === 'sales') {
            $options['kodesales'] = $user['kodesales'] ?? null;
        }

        $orders = $this->headerModel->getAll($options);
        $total = $this->headerModel->count($options);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        $data = [
            'orders' => $orders,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'search' => $search,
            'status' => $status,
            'dateFilter' => $dateFilter,
            'startDate' => $computedStartDate,
            'endDate' => $computedEndDate,
            'rawStartDate' => $startDate,
            'rawEndDate' => $endDate
        ];

        $this->view('orders/index', $data);
    }

    public function create() {
        Auth::requireRole(['sales']);

        $user = Auth::user();
        if (empty($user['kodesales'])) {
            Session::flash('error', 'Sales tidak memiliki kode sales. Silakan hubungi administrator.');
            $this->redirect('/orders');
        }

        $customers = $this->customerModel->getAllForSelection();
        $customersByStatus = [
            'pkp' => array_values(array_filter($customers, static fn($c) => strtolower($c['statuspkp'] ?? 'pkp') === 'pkp')),
            'nonpkp' => array_values(array_filter($customers, static fn($c) => strtolower($c['statuspkp'] ?? 'pkp') === 'nonpkp')),
        ];
        $barangs = $this->barangModel->getAllForSelection();
        $noorder = $this->generateNoorder();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->processFormData($noorder, $user, true);
            if ($result['success']) {
                Session::flash('success', 'Order berhasil dibuat');
                $this->redirect('/orders');
            } else {
                Session::flash('error', $result['message']);
            }
        }

        $data = [
            'noorder' => $noorder,
            'customers' => $customers,
            'customersByStatus' => $customersByStatus,
            'barangs' => $barangs,
            'selectedCustomer' => $_POST['kodecustomer'] ?? '',
            'statuspkp' => $_POST['statuspkp'] ?? 'pkp',
            'tanggalorder' => date('Y-m-d'),
            'keterangan' => $_POST['keterangan'] ?? '',
            'status' => 'order',
            'detailItems' => $this->getPostedDetails(),
            'barangsJson' => json_encode($barangs),
            'customersByStatusJson' => json_encode($customersByStatus)
        ];

        $this->view('orders/create', $data);
    }

    public function edit($noorder) {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $order = $this->headerModel->findByNoorder($noorder);
        if (!$order) {
            Session::flash('error', 'Order tidak ditemukan');
            $this->redirect('/orders');
        }

        $user = Auth::user();
        if (($user['role'] ?? '') === 'sales' && ($user['kodesales'] ?? '') !== $order['kodesales']) {
            Session::flash('error', 'Anda tidak memiliki akses ke order ini');
            $this->redirect('/orders');
        }

        if ($order['status'] !== 'order') {
            Session::flash('error', 'Order sudah menjadi Faktur dan tidak dapat diubah');
            $this->redirect('/orders');
        }

        $customers = $this->customerModel->getAllForSelection();
        $customersByStatus = [
            'pkp' => array_values(array_filter($customers, static fn($c) => strtolower($c['statuspkp'] ?? 'pkp') === 'pkp')),
            'nonpkp' => array_values(array_filter($customers, static fn($c) => strtolower($c['statuspkp'] ?? 'pkp') === 'nonpkp')),
        ];
        $barangs = $this->barangModel->getAllForSelection();
        $detailItems = $this->detailModel->getByNoorder($noorder);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->processFormData($noorder, $user, false, $order);
            if ($result['success']) {
                Session::flash('success', 'Order berhasil diperbarui');
                $this->redirect('/orders');
            } else {
                Session::flash('error', $result['message']);
            }

            $detailItems = $this->getPostedDetails();
            $order = array_merge($order, [
                'kodecustomer' => $_POST['kodecustomer'] ?? $order['kodecustomer'],
                'keterangan' => $_POST['keterangan'] ?? $order['keterangan'],
                'status' => 'order'
            ]);
        }

        $data = [
            'order' => $order,
            'detailItems' => $detailItems,
            'customers' => $customers,
            'customersByStatus' => $customersByStatus,
            'barangs' => $barangs,
            'statuspkp' => $_POST['statuspkp'] ?? ($order['statuspkp'] ?? 'pkp'),
            'barangsJson' => json_encode($barangs),
            'customersByStatusJson' => json_encode($customersByStatus)
        ];

        $this->view('orders/edit', $data);
    }

    public function show($noorder) {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $order = $this->headerModel->findByNoorder($noorder);
        if (!$order) {
            Session::flash('error', 'Order tidak ditemukan');
            $this->redirect('/orders');
        }

        $user = Auth::user();
        if (($user['role'] ?? '') === 'sales' && ($user['kodesales'] ?? '') !== $order['kodesales']) {
            Session::flash('error', 'Anda tidak memiliki akses ke order ini');
            $this->redirect('/orders');
        }

        $details = $this->detailModel->getByNoorder($noorder);

        $data = [
            'order' => $order,
            'details' => $details
        ];

        $this->view('orders/show', $data);
    }

    public function delete($noorder) {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $order = $this->headerModel->findByNoorder($noorder);
        if (!$order) {
            Session::flash('error', 'Order tidak ditemukan');
            $this->redirect('/orders');
        }

        $user = Auth::user();
        if (($user['role'] ?? '') === 'sales' && ($user['kodesales'] ?? '') !== $order['kodesales']) {
            Session::flash('error', 'Anda tidak memiliki akses ke order ini');
            $this->redirect('/orders');
        }

        if (($order['status'] ?? '') !== 'order') {
            Session::flash('error', 'Order dengan status Faktur tidak dapat dihapus');
            $this->redirect('/orders');
        }

        try {
            $this->headerModel->delete($noorder);
            Session::flash('success', 'Order berhasil dihapus');
        } catch (Exception $e) {
            Session::flash('error', 'Gagal menghapus order: ' . $e->getMessage());
        }

        $this->redirect('/orders');
    }

    private function computeDateRange($filter, $start, $end) {
        switch ($filter) {
            case 'week':
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
                break;
            case 'custom':
                $startDate = !empty($start) ? $start : date('Y-m-d');
                $endDate = !empty($end) ? $end : $startDate;
                break;
            case 'today':
            default:
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');
                break;
        }

        return [$startDate, $endDate];
    }

    private function processFormData($noorder, $user, $isCreate = true, $existingOrder = null) {
        $tanggalorder = date('Y-m-d');
        $kodecustomer = trim($_POST['kodecustomer'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $status = 'order';
        $nopenjualan = $_POST['nopenjualan'] ?? null;
        $statusPkpInput = $_POST['statuspkp'] ?? ($existingOrder['statuspkp'] ?? 'pkp');
        $statusPkpNormalized = strtolower(trim($statusPkpInput)) === 'nonpkp' ? 'nonpkp' : 'pkp';

        if (empty($kodecustomer)) {
            return ['success' => false, 'message' => 'Customer harus dipilih'];
        }

        $customerInfo = $this->customerModel->findByKodecustomer($kodecustomer);
        if (!$customerInfo) {
            return ['success' => false, 'message' => 'Customer tidak ditemukan'];
        }

        $customerStatusPkp = strtolower($customerInfo['statuspkp'] ?? 'pkp');
        if ($customerStatusPkp !== $statusPkpNormalized) {
            return ['success' => false, 'message' => 'Customer yang dipilih tidak sesuai dengan status PKP order'];
        }

        $detailData = $this->sanitizeDetailInput();
        if (empty($detailData)) {
            return ['success' => false, 'message' => 'Minimal satu detail order harus diisi'];
        }

        $nilaiOrder = array_sum(array_column($detailData, 'totalharga'));

        $headerData = [
            'noorder' => $noorder,
            'tanggalorder' => $tanggalorder,
            'kodesales' => $user['kodesales'] ?? $existingOrder['kodesales'] ?? null,
            'statuspkp' => $statusPkpNormalized,
            'kodecustomer' => $kodecustomer,
            'keterangan' => $keterangan,
            'nilaiorder' => $nilaiOrder,
            'nopenjualan' => $nopenjualan,
            'status' => $status
        ];

        if (empty($headerData['kodesales'])) {
            return ['success' => false, 'message' => 'Kode sales tidak tersedia'];
        }

        try {
            if ($isCreate) {
                if ($this->headerModel->findByNoorder($noorder)) {
                    return ['success' => false, 'message' => 'Nomor order sudah digunakan'];
                }
                $this->headerModel->create($headerData, $detailData);
            } else {
                $this->headerModel->update($noorder, $headerData, $detailData);
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }

        return ['success' => true];
    }

    private function sanitizeDetailInput() {
        $kodebarang = $_POST['detail_kodebarang'] ?? [];
        $jumlah = $_POST['detail_jumlah'] ?? [];
        $harga = $_POST['detail_harga'] ?? [];
        $discount = $_POST['detail_discount'] ?? [];
        $satuan = $_POST['detail_satuan'] ?? [];

        $details = [];
        $count = count($kodebarang);

        for ($i = 0; $i < $count; $i++) {
            $kb = trim($kodebarang[$i] ?? '');
            $qty = isset($jumlah[$i]) ? (int)$jumlah[$i] : 0;
            $price = isset($harga[$i]) ? (float)str_replace(',', '', $harga[$i]) : 0;
            $disc = isset($discount[$i]) ? (float)str_replace(',', '', $discount[$i]) : 0;

            if ($kb === '' || $qty <= 0) {
                continue;
            }

            $lineTotal = max(($qty * $price) - $disc, 0);

            $details[] = [
                'kodebarang' => $kb,
                'jumlah' => $qty,
                'hargajual' => $price,
                'discount' => $disc,
                'totalharga' => $lineTotal,
                'satuan' => trim($satuan[$i] ?? '')
            ];
        }

        return $details;
    }

    private function getPostedDetails() {
        $kodebarang = $_POST['detail_kodebarang'] ?? [];
        $jumlah = $_POST['detail_jumlah'] ?? [];
        $harga = $_POST['detail_harga'] ?? [];
        $discount = $_POST['detail_discount'] ?? [];
        $satuan = $_POST['detail_satuan'] ?? [];

        $rows = [];
        $count = max(count($kodebarang), count($jumlah));

        for ($i = 0; $i < $count; $i++) {
            $kb = $kodebarang[$i] ?? '';
            $qty = $jumlah[$i] ?? '';
            $price = $harga[$i] ?? '';
            $disc = $discount[$i] ?? '';
            $total = '';

            if ($kb !== '' && $qty !== '' && $price !== '') {
                $calcTotal = max(((float)$qty * (float)$price) - (float)$disc, 0);
                $total = number_format($calcTotal, 2, '.', '');
            }

            $rows[] = [
                'kodebarang' => $kb,
                'jumlah' => $qty,
                'hargajual' => $price,
                'discount' => $disc,
                'totalharga' => $total,
                'satuan' => $satuan[$i] ?? ''
            ];
        }

        if (empty($rows)) {
            $rows[] = [
                'kodebarang' => '',
                'jumlah' => '',
                'hargajual' => '',
                'discount' => '',
                'totalharga' => '',
                'satuan' => ''
            ];
        }

        return $rows;
    }

    private function generateNoorder() {
        $prefix = 'OJ' . date('ym');
        $last = $this->headerModel->getLastNoorderWithPrefix($prefix);

        if ($last && isset($last['noorder'])) {
            $lastNumber = (int)substr($last['noorder'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s%05d', $prefix, $nextNumber);
    }
}
