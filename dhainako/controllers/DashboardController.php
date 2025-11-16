<?php
class DashboardController extends Controller {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        Auth::requireAuth();
        
        $user = Auth::user();
        $role = $user['role'];
        
        // Load different statistics based on role
        switch ($role) {
            case 'admin':
                $data = $this->getAdminDashboardData($user);
                break;
            case 'manajemen':
                $data = $this->getManajemenDashboardData($user);
                break;
            case 'operator':
                $data = $this->getOperatorDashboardData($user);
                break;
            case 'sales':
                $data = $this->getSalesDashboardData($user);
                break;
            default:
                $data = $this->getDefaultDashboardData($user);
        }
        
        $this->view('dashboard/index', $data);
    }
    
    private function getAdminDashboardData($user) {
        $userModel = new User();
        $penjualanModel = new Headerpenjualan();
        $penerimaanModel = new Headerpenerimaan();
        $orderModel = new Headerorder();
        $customerModel = new Mastercustomer();
        $barangModel = new Masterbarang();
        $salesModel = new Mastersales();
        
        // Get today's date range
        $today = date('Y-m-d');
        
        // Total statistics
        $totalUsers = $userModel->count();
        $totalSales = $salesModel->count();
        $totalCustomers = $customerModel->count();
        $totalBarang = $barangModel->count();
        
        // Today's statistics
        $todayPenjualan = $penjualanModel->count(['start_date' => $today, 'end_date' => $today]);
        $todayPenerimaan = $penerimaanModel->count(['start_date' => $today, 'end_date' => $today]);
        $todayOrder = $orderModel->count(['start_date' => $today, 'end_date' => $today]);
        
        // This month's statistics
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $monthPenjualan = $penjualanModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd]);
        $monthPenerimaan = $penerimaanModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd]);
        
        // Get total nilai penjualan this month
        $sql = "SELECT SUM(nilaipenjualan) as total FROM headerpenjualan 
                WHERE tanggalpenjualan BETWEEN ? AND ?";
        $omsetBulan = $this->db->fetchOne($sql, [$monthStart, $monthEnd]);
        $totalOmset = $omsetBulan['total'] ?? 0;
        
        return [
            'user' => $user,
            'role' => 'admin',
            'totalUsers' => $totalUsers,
            'totalSales' => $totalSales,
            'totalCustomers' => $totalCustomers,
            'totalBarang' => $totalBarang,
            'todayPenjualan' => $todayPenjualan,
            'todayPenerimaan' => $todayPenerimaan,
            'todayOrder' => $todayOrder,
            'monthPenjualan' => $monthPenjualan,
            'monthPenerimaan' => $monthPenerimaan,
            'totalOmset' => $totalOmset
        ];
    }
    
    private function getManajemenDashboardData($user) {
        $penjualanModel = new Headerpenjualan();
        $penerimaanModel = new Headerpenerimaan();
        $orderModel = new Headerorder();
        $customerModel = new Mastercustomer();
        
        // Get today's date range
        $today = date('Y-m-d');
        
        // This month's statistics
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        
        // Today's statistics
        $todayPenjualan = $penjualanModel->count(['start_date' => $today, 'end_date' => $today]);
        $todayPenerimaan = $penerimaanModel->count(['start_date' => $today, 'end_date' => $today]);
        $todayOrder = $orderModel->count(['start_date' => $today, 'end_date' => $today]);
        
        // This month's statistics
        $monthPenjualan = $penjualanModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd]);
        $monthPenerimaan = $penerimaanModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd]);
        $monthOrder = $orderModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd]);
        
        // Get total nilai penjualan this month
        $sql = "SELECT SUM(nilaipenjualan) as total FROM headerpenjualan 
                WHERE tanggalpenjualan BETWEEN ? AND ?";
        $omsetBulan = $this->db->fetchOne($sql, [$monthStart, $monthEnd]);
        $totalOmset = $omsetBulan['total'] ?? 0;
        
        // Get total piutang
        $sql = "SELECT SUM(saldopenjualan) as total FROM headerpenjualan WHERE saldopenjualan > 0";
        $totalPiutang = $this->db->fetchOne($sql, []);
        $totalPiutang = $totalPiutang['total'] ?? 0;
        
        // Get total penerimaan this month
        $sql = "SELECT SUM(totalnetto) as total FROM headerpenerimaan 
                WHERE tanggalpenerimaan BETWEEN ? AND ?";
        $totalPenerimaan = $this->db->fetchOne($sql, [$monthStart, $monthEnd]);
        $totalPenerimaan = $totalPenerimaan['total'] ?? 0;
        
        $totalCustomers = $customerModel->count();
        
        return [
            'user' => $user,
            'role' => 'manajemen',
            'todayPenjualan' => $todayPenjualan,
            'todayPenerimaan' => $todayPenerimaan,
            'todayOrder' => $todayOrder,
            'monthPenjualan' => $monthPenjualan,
            'monthPenerimaan' => $monthPenerimaan,
            'monthOrder' => $monthOrder,
            'totalOmset' => $totalOmset,
            'totalPiutang' => $totalPiutang,
            'totalPenerimaan' => $totalPenerimaan,
            'totalCustomers' => $totalCustomers
        ];
    }
    
    private function getOperatorDashboardData($user) {
        $orderModel = new Headerorder();
        $penjualanModel = new Headerpenjualan();
        $barangModel = new Masterbarang();
        
        // Get today's date range
        $today = date('Y-m-d');
        
        // This month's statistics
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        
        // Today's statistics
        $todayOrder = $orderModel->count(['start_date' => $today, 'end_date' => $today]);
        $todayPenjualan = $penjualanModel->count(['start_date' => $today, 'end_date' => $today]);
        
        // This month's statistics
        $monthOrder = $orderModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd]);
        $monthPenjualan = $penjualanModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd]);
        
        // Get order status counts
        $sql = "SELECT status, COUNT(*) as total FROM headerorder GROUP BY status";
        $orderStatus = $this->db->fetchAll($sql, []);
        $orderStatusCounts = [];
        foreach ($orderStatus as $row) {
            $orderStatusCounts[$row['status']] = $row['total'];
        }
        
        // Get barang with low stock (stokakhir <= 10)
        $sql = "SELECT COUNT(*) as total FROM masterbarang WHERE stokakhir <= 10 AND stokakhir >= 0";
        $lowStock = $this->db->fetchOne($sql, []);
        $lowStockCount = $lowStock['total'] ?? 0;
        
        // Get total barang
        $totalBarang = $barangModel->count();
        
        return [
            'user' => $user,
            'role' => 'operator',
            'todayOrder' => $todayOrder,
            'todayPenjualan' => $todayPenjualan,
            'monthOrder' => $monthOrder,
            'monthPenjualan' => $monthPenjualan,
            'orderStatusCounts' => $orderStatusCounts,
            'lowStockCount' => $lowStockCount,
            'totalBarang' => $totalBarang
        ];
    }
    
    private function getSalesDashboardData($user) {
        $penerimaanModel = new Headerpenerimaan();
        $orderModel = new Headerorder();
        $visitModel = new Visit();
        
        $kodesales = $user['kodesales'] ?? null;
        if (!$kodesales) {
            return [
                'user' => $user,
                'role' => 'sales',
                'error' => 'Kode Sales tidak ditemukan'
            ];
        }
        
        // Get date ranges
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $yearStart = date('Y-01-01');
        $yearEnd = date('Y-12-31');
        
        // Kunjungan statistics
        $sql = "SELECT COUNT(*) as total FROM visits 
                WHERE user_id = ? AND DATE(check_in_time) = ?";
        $todayVisits = $this->db->fetchOne($sql, [$user['id'], $today]);
        $todayVisitsCount = $todayVisits['total'] ?? 0;
        
        $sql = "SELECT COUNT(*) as total FROM visits 
                WHERE user_id = ? AND DATE(check_in_time) BETWEEN ? AND ?";
        $monthVisits = $this->db->fetchOne($sql, [$user['id'], $monthStart, $monthEnd]);
        $monthVisitsCount = $monthVisits['total'] ?? 0;
        
        $yearVisits = $this->db->fetchOne($sql, [$user['id'], $yearStart, $yearEnd]);
        $yearVisitsCount = $yearVisits['total'] ?? 0;
        
        // Order statistics
        $todayOrder = $orderModel->count(['start_date' => $today, 'end_date' => $today, 'kodesales' => $kodesales]);
        $monthOrder = $orderModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd, 'kodesales' => $kodesales]);
        $yearOrder = $orderModel->count(['start_date' => $yearStart, 'end_date' => $yearEnd, 'kodesales' => $kodesales]);
        
        // Get order values
        $sql = "SELECT COUNT(*) as jumlah, SUM(nilaiorder) as nilai FROM headerorder 
                WHERE tanggalorder = ? AND kodesales = ?";
        $todayOrderData = $this->db->fetchOne($sql, [$today, $kodesales]);
        $todayOrderJumlah = $todayOrderData['jumlah'] ?? 0;
        $todayOrderNilai = $todayOrderData['nilai'] ?? 0;
        
        $sql = "SELECT COUNT(*) as jumlah, SUM(nilaiorder) as nilai FROM headerorder 
                WHERE tanggalorder BETWEEN ? AND ? AND kodesales = ?";
        $monthOrderData = $this->db->fetchOne($sql, [$monthStart, $monthEnd, $kodesales]);
        $monthOrderJumlah = $monthOrderData['jumlah'] ?? 0;
        $monthOrderNilai = $monthOrderData['nilai'] ?? 0;
        
        $yearOrderData = $this->db->fetchOne($sql, [$yearStart, $yearEnd, $kodesales]);
        $yearOrderJumlah = $yearOrderData['jumlah'] ?? 0;
        $yearOrderNilai = $yearOrderData['nilai'] ?? 0;
        
        // Inkaso/Penerimaan statistics
        $todayPenerimaan = $penerimaanModel->count(['start_date' => $today, 'end_date' => $today, 'kodesales' => $kodesales]);
        $monthPenerimaan = $penerimaanModel->count(['start_date' => $monthStart, 'end_date' => $monthEnd, 'kodesales' => $kodesales]);
        $yearPenerimaan = $penerimaanModel->count(['start_date' => $yearStart, 'end_date' => $yearEnd, 'kodesales' => $kodesales]);
        
        // Get penerimaan values
        $sql = "SELECT COUNT(*) as jumlah, SUM(totalnetto) as nilai FROM headerpenerimaan 
                WHERE tanggalpenerimaan = ? AND kodesales = ?";
        $todayPenerimaanData = $this->db->fetchOne($sql, [$today, $kodesales]);
        $todayPenerimaanJumlah = $todayPenerimaanData['jumlah'] ?? 0;
        $todayPenerimaanNilai = $todayPenerimaanData['nilai'] ?? 0;
        
        $sql = "SELECT COUNT(*) as jumlah, SUM(totalnetto) as nilai FROM headerpenerimaan 
                WHERE tanggalpenerimaan BETWEEN ? AND ? AND kodesales = ?";
        $monthPenerimaanData = $this->db->fetchOne($sql, [$monthStart, $monthEnd, $kodesales]);
        $monthPenerimaanJumlah = $monthPenerimaanData['jumlah'] ?? 0;
        $monthPenerimaanNilai = $monthPenerimaanData['nilai'] ?? 0;
        
        $yearPenerimaanData = $this->db->fetchOne($sql, [$yearStart, $yearEnd, $kodesales]);
        $yearPenerimaanJumlah = $yearPenerimaanData['jumlah'] ?? 0;
        $yearPenerimaanNilai = $yearPenerimaanData['nilai'] ?? 0;
        
        // Get active visit
        $activeVisit = $visitModel->findActiveByUser($user['id']);
        
        return [
            'user' => $user,
            'role' => 'sales',
            'kodesales' => $kodesales,
            'activeVisit' => $activeVisit,
            // Kunjungan
            'todayVisits' => $todayVisitsCount,
            'monthVisits' => $monthVisitsCount,
            'yearVisits' => $yearVisitsCount,
            // Order
            'todayOrderJumlah' => $todayOrderJumlah,
            'todayOrderNilai' => $todayOrderNilai,
            'monthOrderJumlah' => $monthOrderJumlah,
            'monthOrderNilai' => $monthOrderNilai,
            'yearOrderJumlah' => $yearOrderJumlah,
            'yearOrderNilai' => $yearOrderNilai,
            // Inkaso/Penerimaan
            'todayPenerimaanJumlah' => $todayPenerimaanJumlah,
            'todayPenerimaanNilai' => $todayPenerimaanNilai,
            'monthPenerimaanJumlah' => $monthPenerimaanJumlah,
            'monthPenerimaanNilai' => $monthPenerimaanNilai,
            'yearPenerimaanJumlah' => $yearPenerimaanJumlah,
            'yearPenerimaanNilai' => $yearPenerimaanNilai
        ];
    }
    
    private function getDefaultDashboardData($user) {
        return [
            'user' => $user,
            'role' => $user['role']
        ];
    }
}

