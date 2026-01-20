<?php
class LaporanController extends Controller {
    private $barangModel;
    private $pabrikModel;
    private $golonganModel;

    public function __construct() {
        parent::__construct();
        require_once __DIR__ . '/../core/LaporanPDF.php';
        $this->barangModel = new Masterbarang();
        $this->pabrikModel = new Tabelpabrik();
        $this->golonganModel = new Tabelgolongan();
    }

    public function daftarBarang() {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $search = trim($_GET['search'] ?? '');
        $kodepabrik = trim($_GET['kodepabrik'] ?? '');
        $kodegolongan = trim($_GET['kodegolongan'] ?? '');
        $kondisiStok = $_GET['kondisi_stok'] ?? 'semua'; // 'semua', 'ada', 'kosong'
        $sortBy = $_GET['sort_by'] ?? 'namabarang';
        $sortOrder = $_GET['sort_order'] ?? 'ASC';
        $export = $_GET['export'] ?? ''; // 'excel' or 'pdf'

        // Get all data for export, or paginated for display
        if (!empty($export)) {
            // For export, get all data
            $barangs = $this->getAllBarangsForReport($search, $kodepabrik, $kodegolongan, $kondisiStok, $sortBy, $sortOrder);
            
            if ($export === 'excel') {
                $this->exportExcel($barangs);
            } elseif ($export === 'pdf') {
                $this->exportPDF($barangs);
            }
            exit;
        }

        // For display, use pagination
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPageOptions = [10, 25, 50, 100, 200, 500, 1000];
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $perPage = in_array($perPage, $perPageOptions, true) ? $perPage : 10;

        $barangs = $this->getBarangsForReport($search, $kodepabrik, $kodegolongan, $kondisiStok, $sortBy, $sortOrder, $page, $perPage);
        $total = $this->countBarangsForReport($search, $kodepabrik, $kodegolongan, $kondisiStok);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        // Get pabrik and golongan for dropdown
        $pabriks = $this->pabrikModel->getAllActive();
        $golongans = $this->golonganModel->getAllActive();

        $data = [
            'barangs' => $barangs,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'search' => $search,
            'kodepabrik' => $kodepabrik,
            'kodegolongan' => $kodegolongan,
            'kondisiStok' => $kondisiStok,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'pabriks' => $pabriks,
            'golongans' => $golongans,
        ];

        $this->view('laporan/daftar-barang', $data);
    }

    private function getBarangsForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        // Validate sort column
        $validSortColumns = ['kodebarang', 'namabarang', 'golongan', 'pabrik'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'kodebarang' => 'mb.kodebarang',
            'namabarang' => 'mb.namabarang',
            'golongan' => 'tg.namagolongan',
            'pabrik' => 'tp.namapabrik'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.kodebarang,
                    mb.namabarang,
                    mb.satuan,
                    tp.namapabrik AS pabrik,
                    tg.namagolongan AS golongan,
                    mb.kandungan
                FROM masterbarang mb
                LEFT JOIN tabelpabrik tp ON mb.kodepabrik = tp.kodepabrik
                LEFT JOIN tabelgolongan tg ON mb.kodegolongan = tg.kodegolongan
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    private function getAllBarangsForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC') {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        // Validate sort column
        $validSortColumns = ['kodebarang', 'namabarang', 'golongan', 'pabrik'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'kodebarang' => 'mb.kodebarang',
            'namabarang' => 'mb.namabarang',
            'golongan' => 'tg.namagolongan',
            'pabrik' => 'tp.namapabrik'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.kodebarang,
                    mb.namabarang,
                    mb.satuan,
                    tp.namapabrik AS pabrik,
                    tg.namagolongan AS golongan,
                    mb.kandungan
                FROM masterbarang mb
                LEFT JOIN tabelpabrik tp ON mb.kodepabrik = tp.kodepabrik
                LEFT JOIN tabelgolongan tg ON mb.kodegolongan = tg.kodegolongan
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}";

        return $this->db->fetchAll($sql, $params);
    }

    private function countBarangsForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua') {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total 
                FROM masterbarang mb 
                WHERE {$whereClause}";

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    private function exportExcel($barangs) {
        $filename = 'Laporan_Daftar_Barang_' . date('YmdHis') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add BOM for UTF-8 to ensure Excel displays correctly
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['Kode Barang', 'Nama Barang', 'Satuan', 'Pabrik', 'Golongan', 'Kandungan'], ';');

        // Data
        foreach ($barangs as $barang) {
            fputcsv($output, [
                $barang['kodebarang'] ?? '',
                $barang['namabarang'] ?? '',
                $barang['satuan'] ?? '',
                $barang['pabrik'] ?? '',
                $barang['golongan'] ?? '',
                $barang['kandungan'] ?? ''
            ], ';');
        }

        fclose($output);
    }

    private function exportPDF($barangs) {
        // Generate PDF using simple HTML to PDF conversion
        $this->generateAndDownloadPDF('daftar-barang', $barangs);
    }

    private function generateAndDownloadPDF($reportType, $data) {
        $filename = 'Daftar_Barang_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $pdf = new LaporanPDF('P', 'mm', 'A4');
        $pdf->reportTitle = 'Laporan Daftar Barang';
        $pdf->reportSubtitle = "Tanggal Laporan: " . date('d F Y') . "\nTotal Barang: " . count($data);
        $pdf->printedBy = Auth::user()['namalengkap'] ?? 'System';
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $header = ['No', 'Kode', 'Nama Barang', 'Satuan', 'Pabrik', 'Golongan', 'Kandungan'];
        $widths = [10, 25, 50, 15, 30, 30, 30];

        $pdf->TableHeader($header, $widths);

        $pdf->SetFont('Helvetica', '', 8);
        $no = 1;

        foreach ($data as $d) {
            $pdf->Cell($widths[0], 6, $no++, 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $d['kodebarang'] ?? '-', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, substr($d['namabarang'] ?? '-', 0, 35), 1, 0, 'L');
            $pdf->Cell($widths[3], 6, $d['satuan'] ?? '-', 1, 0, 'C');
            $pdf->Cell($widths[4], 6, substr($d['pabrik'] ?? '-', 0, 15), 1, 0, 'L');
            $pdf->Cell($widths[5], 6, substr($d['golongan'] ?? '-', 0, 15), 1, 0, 'L');
            $pdf->Cell($widths[6], 6, substr($d['kandungan'] ?? '-', 0, 18), 1, 0, 'L');
            $pdf->Ln();
        }

        $pdf->Output('D', $filename);
    }

    private function downloadAsHTML($html, $filename) {
        // Send as downloadable file
        // Detect if it's a PDF or HTML based on filename
        $isPDF = strpos($filename, '.pdf') !== false;
        
        if ($isPDF) {
            // For PDF files, use application/pdf MIME type
            header('Content-Type: application/pdf; charset=utf-8');
        } else {
            // For HTML files, use text/html MIME type
            header('Content-Type: text/html; charset=utf-8');
        }
        
        // Don't append extension - filename already has it
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        
        echo $html;
    }

    public function daftarStok() {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $search = trim($_GET['search'] ?? '');
        $kodepabrik = trim($_GET['kodepabrik'] ?? '');
        $kodegolongan = trim($_GET['kodegolongan'] ?? '');
        $kondisiStok = $_GET['kondisi_stok'] ?? 'semua'; // 'semua', 'ada', 'kosong'
        $sortBy = $_GET['sort_by'] ?? 'namabarang';
        $sortOrder = $_GET['sort_order'] ?? 'ASC';
        $export = $_GET['export'] ?? ''; // 'excel' or 'pdf'

        // Get all data for export, or paginated for display
        if (!empty($export)) {
            // For export, get all data
            $barangs = $this->getAllStoksForReport($search, $kodepabrik, $kodegolongan, $kondisiStok, $sortBy, $sortOrder);
            
            if ($export === 'excel') {
                $this->exportExcelStok($barangs);
            } elseif ($export === 'pdf') {
                $this->exportPDFStok($barangs);
            }
            exit;
        }

        // For display, use pagination
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPageOptions = [10, 25, 50, 100, 200, 500, 1000];
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $perPage = in_array($perPage, $perPageOptions, true) ? $perPage : 10;

        $barangs = $this->getStoksForReport($search, $kodepabrik, $kodegolongan, $kondisiStok, $sortBy, $sortOrder, $page, $perPage);
        $total = $this->countStoksForReport($search, $kodepabrik, $kodegolongan, $kondisiStok);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        // Get pabrik and golongan for dropdown
        $pabriks = $this->pabrikModel->getAllActive();
        $golongans = $this->golonganModel->getAllActive();

        $data = [
            'barangs' => $barangs,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'search' => $search,
            'kodepabrik' => $kodepabrik,
            'kodegolongan' => $kodegolongan,
            'kondisiStok' => $kondisiStok,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'pabriks' => $pabriks,
            'golongans' => $golongans,
        ];

        $this->view('laporan/daftar-stok', $data);
    }

    private function getStoksForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        // Validate sort column
        $validSortColumns = ['namabarang', 'satuan', 'hargajual', 'discountjual', 'kondisi', 'stok'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'namabarang' => 'mb.namabarang',
            'satuan' => 'mb.satuan',
            'hargajual' => 'mb.hargajual',
            'discountjual' => 'mb.discountjual',
            'kondisi' => 'mb.kondisi',
            'stok' => 'mb.stokakhir'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.namabarang,
                    mb.satuan,
                    mb.hargajual,
                    mb.discountjual,
                    mb.kondisi,
                    mb.stokakhir AS stok
                FROM masterbarang mb
                LEFT JOIN tabelpabrik tp ON mb.kodepabrik = tp.kodepabrik
                LEFT JOIN tabelgolongan tg ON mb.kodegolongan = tg.kodegolongan
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    private function getAllStoksForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC') {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        // Validate sort column
        $validSortColumns = ['namabarang', 'satuan', 'hargajual', 'discountjual', 'kondisi', 'stok'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'namabarang' => 'mb.namabarang',
            'satuan' => 'mb.satuan',
            'hargajual' => 'mb.hargajual',
            'discountjual' => 'mb.discountjual',
            'kondisi' => 'mb.kondisi',
            'stok' => 'mb.stokakhir'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.namabarang,
                    mb.satuan,
                    mb.hargajual,
                    mb.discountjual,
                    mb.kondisi,
                    mb.stokakhir AS stok
                FROM masterbarang mb
                LEFT JOIN tabelpabrik tp ON mb.kodepabrik = tp.kodepabrik
                LEFT JOIN tabelgolongan tg ON mb.kodegolongan = tg.kodegolongan
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}";

        return $this->db->fetchAll($sql, $params);
    }

    private function countStoksForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua') {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total 
                FROM masterbarang mb 
                WHERE {$whereClause}";

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    private function exportExcelStok($barangs) {
        $filename = 'Laporan_Daftar_Stok_' . date('YmdHis') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add BOM for UTF-8 to ensure Excel displays correctly
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['Nama Barang', 'Satuan', 'Harga Jual', 'Discount', 'Kondisi', 'Stok'], ';');

        // Data
        foreach ($barangs as $barang) {
            fputcsv($output, [
                $barang['namabarang'] ?? '',
                $barang['satuan'] ?? '',
                $barang['hargajual'] ?? '0',
                $barang['discountjual'] ?? '0',
                $barang['kondisi'] ?? '-',
                $barang['stok'] ?? '0'
            ], ';');
        }

        fclose($output);
    }

    private function exportPDFStok($barangs) {
        $this->generateAndDownloadPDFStok($barangs);
    }

    private function generateAndDownloadPDFStok($data) {
        $filename = 'Daftar_Stok_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $pdf = new LaporanPDF('P', 'mm', 'A4');
        $pdf->reportTitle = 'Laporan Daftar Stok';
        $pdf->reportSubtitle = "Tanggal Laporan: " . date('d F Y') . "\nTotal Barang: " . count($data);
        $pdf->printedBy = Auth::user()['namalengkap'] ?? 'System';
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $header = ['No', 'Nama Barang', 'Satuan', 'Harga Jual', 'Disc', 'Kondisi', 'Stok'];
        $widths = [10, 75, 20, 20, 15, 25, 15];

        $pdf->TableHeader($header, $widths);

        $pdf->SetFont('Helvetica', '', 8);
        $no = 1;

        foreach ($data as $d) {
            $pdf->Cell($widths[0], 6, $no++, 1, 0, 'C');
            $pdf->Cell($widths[1], 6, substr($d['namabarang'] ?? '-', 0, 40), 1, 0, 'L');
            $pdf->Cell($widths[2], 6, $d['satuan'] ?? '-', 1, 0, 'C');
            $pdf->Cell($widths[3], 6, number_format((float)($d['hargajual'] ?? 0), 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[4], 6, number_format((float)($d['discountjual'] ?? 0), 2, ',', '.') . '%', 1, 0, 'R');
            $pdf->Cell($widths[5], 6, substr($d['kondisi'] ?? '-', 0, 15), 1, 0, 'L');
            $pdf->Cell($widths[6], 6, number_format((float)($d['stok'] ?? 0), 0, ',', '.'), 1, 0, 'R');
            $pdf->Ln();
        }

        $pdf->Output('D', $filename);
    }

    public function daftarHarga() {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $search = trim($_GET['search'] ?? '');
        $kodepabrik = trim($_GET['kodepabrik'] ?? '');
        $kodegolongan = trim($_GET['kodegolongan'] ?? '');
        $kondisiStok = $_GET['kondisi_stok'] ?? 'semua'; // 'semua', 'ada', 'kosong'
        $sortBy = $_GET['sort_by'] ?? 'namabarang';
        $sortOrder = $_GET['sort_order'] ?? 'ASC';
        $export = $_GET['export'] ?? ''; // 'excel' or 'pdf'

        // Get all data for export, or paginated for display
        if (!empty($export)) {
            // For export, get all data
            $barangs = $this->getAllHargasForReport($search, $kodepabrik, $kodegolongan, $kondisiStok, $sortBy, $sortOrder);
            
            if ($export === 'excel') {
                $this->exportExcelHarga($barangs);
            } elseif ($export === 'pdf') {
                $this->exportPDFHarga($barangs);
            }
            exit;
        }

        // For display, use pagination
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPageOptions = [10, 25, 50, 100, 200, 500, 1000];
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $perPage = in_array($perPage, $perPageOptions, true) ? $perPage : 10;

        $barangs = $this->getHargasForReport($search, $kodepabrik, $kodegolongan, $kondisiStok, $sortBy, $sortOrder, $page, $perPage);
        $total = $this->countHargasForReport($search, $kodepabrik, $kodegolongan, $kondisiStok);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        // Get pabrik and golongan for dropdown
        $pabriks = $this->pabrikModel->getAllActive();
        $golongans = $this->golonganModel->getAllActive();

        $data = [
            'barangs' => $barangs,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'search' => $search,
            'kodepabrik' => $kodepabrik,
            'kodegolongan' => $kodegolongan,
            'kondisiStok' => $kondisiStok,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'pabriks' => $pabriks,
            'golongans' => $golongans,
        ];

        $this->view('laporan/daftar-harga', $data);
    }

    private function getHargasForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        // Validate sort column
        $validSortColumns = ['namabarang', 'satuan', 'pabrik', 'hargajual', 'discountjual'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'namabarang' => 'mb.namabarang',
            'satuan' => 'mb.satuan',
            'pabrik' => 'tp.namapabrik',
            'hargajual' => 'mb.hargajual',
            'discountjual' => 'mb.discountjual'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.namabarang,
                    mb.satuan,
                    tp.namapabrik AS pabrik,
                    mb.kondisi,
                    mb.ed,
                    mb.hargajual,
                    mb.discountjual
                FROM masterbarang mb
                LEFT JOIN tabelpabrik tp ON mb.kodepabrik = tp.kodepabrik
                LEFT JOIN tabelgolongan tg ON mb.kodegolongan = tg.kodegolongan
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    private function getAllHargasForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC') {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        // Validate sort column
        $validSortColumns = ['namabarang', 'satuan', 'pabrik', 'hargajual', 'discountjual'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'namabarang' => 'mb.namabarang',
            'satuan' => 'mb.satuan',
            'pabrik' => 'tp.namapabrik',
            'hargajual' => 'mb.hargajual',
            'discountjual' => 'mb.discountjual'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.namabarang,
                    mb.satuan,
                    tp.namapabrik AS pabrik,
                    mb.kondisi,
                    mb.ed,
                    mb.hargajual,
                    mb.discountjual
                FROM masterbarang mb
                LEFT JOIN tabelpabrik tp ON mb.kodepabrik = tp.kodepabrik
                LEFT JOIN tabelgolongan tg ON mb.kodegolongan = tg.kodegolongan
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}";

        return $this->db->fetchAll($sql, $params);
    }

    private function countHargasForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua') {
        $where = ["1=1"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mb.namabarang LIKE ? OR mb.kandungan LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodepabrik)) {
            $where[] = "mb.kodepabrik = ?";
            $params[] = $kodepabrik;
        }

        if (!empty($kodegolongan)) {
            $where[] = "mb.kodegolongan = ?";
            $params[] = $kodegolongan;
        }

        if ($kondisiStok === 'ada') {
            $where[] = "mb.stokakhir > 0";
        } elseif ($kondisiStok === 'kosong') {
            $where[] = "(mb.stokakhir = 0 OR mb.stokakhir IS NULL)";
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total 
                FROM masterbarang mb 
                WHERE {$whereClause}";

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    private function exportExcelHarga($barangs) {
        $filename = 'Laporan_Daftar_Harga_' . date('YmdHis') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add BOM for UTF-8 to ensure Excel displays correctly
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['Nama Barang', 'Satuan', 'Pabrik', 'Kondisi', 'ED', 'Harga Jual', 'Discount Jual'], ';');

        // Data
        foreach ($barangs as $barang) {
            fputcsv($output, [
                $barang['namabarang'] ?? '',
                $barang['satuan'] ?? '',
                $barang['pabrik'] ?? '',
                $barang['kondisi'] ?? '',
                $barang['ed'] ?? '',
                $barang['hargajual'] ?? '0',
                number_format((float)($barang['discountjual'] ?? 0), 2, ',', '.')
            ], ';');
        }

        fclose($output);
    }

    private function exportPDFHarga($barangs) {
        $this->generateAndDownloadPDFHarga($barangs);
    }

    private function generateAndDownloadPDFHarga($data) {
        $filename = 'Daftar_Harga_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $pdf = new LaporanPDF('P', 'mm', 'A4');
        $pdf->reportTitle = 'Laporan Daftar Harga';
        $pdf->reportSubtitle = "Tanggal Laporan: " . date('d F Y') . "\nTotal Barang: " . count($data);
        $pdf->printedBy = Auth::user()['namalengkap'] ?? 'System';
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $header = ['No', 'Nama Barang', 'Satuan', 'Pabrik', 'Kondisi', 'ED', 'Harga Jual', 'Disc'];
        $widths = [10, 50, 15, 25, 20, 20, 25, 15];

        $pdf->TableHeader($header, $widths);

        $pdf->SetFont('Helvetica', '', 8);
        $no = 1;

        foreach ($data as $d) {
            $pdf->Cell($widths[0], 6, $no++, 1, 0, 'C');
            $pdf->Cell($widths[1], 6, substr($d['namabarang'] ?? '-', 0, 35), 1, 0, 'L');
            $pdf->Cell($widths[2], 6, $d['satuan'] ?? '-', 1, 0, 'C');
            $pdf->Cell($widths[3], 6, substr($d['pabrik'] ?? '-', 0, 15), 1, 0, 'L');
            $pdf->Cell($widths[4], 6, substr($d['kondisi'] ?? '-', 0, 12), 1, 0, 'L');
            $pdf->Cell($widths[5], 6, $d['ed'] ?? '-', 1, 0, 'C');
            $pdf->Cell($widths[6], 6, number_format((float)($d['hargajual'] ?? 0), 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[7], 6, number_format((float)($d['discountjual'] ?? 0), 2, ',', '.') . '%', 1, 0, 'R');
            $pdf->Ln();
        }

        $pdf->Output('D', $filename);
    }

    public function daftarTagihan() {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $search = trim($_GET['search'] ?? '');
        $kodecustomer = trim($_GET['kodecustomer'] ?? '');
        $statusJatuhTempo = $_GET['status_jatuh_tempo'] ?? 'semua'; // 'semua', 'sudah', 'belum'
        $sortBy = $_GET['sort_by'] ?? 'umur';
        $sortOrder = $_GET['sort_order'] ?? 'DESC';
        $export = $_GET['export'] ?? ''; // 'excel' or 'pdf'

        // Get all data for export, or paginated for display
        if (!empty($export)) {
            // For export, get all data
            $tagihans = $this->getAllTagihansForReport($search, $kodecustomer, $statusJatuhTempo, $sortBy, $sortOrder);
            
            if ($export === 'excel') {
                $this->exportExcelTagihan($tagihans);
            } elseif ($export === 'pdf') {
                $this->exportPDFTagihan($tagihans);
            }
            exit;
        }

        // For display, use pagination
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPageOptions = [10, 25, 50, 100, 200, 500, 1000];
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $perPage = in_array($perPage, $perPageOptions, true) ? $perPage : 10;

        $tagihans = $this->getTagihansForReport($search, $kodecustomer, $statusJatuhTempo, $sortBy, $sortOrder, $page, $perPage);
        $total = $this->countTagihansForReport($search, $kodecustomer, $statusJatuhTempo);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        // Calculate totals for current page
        $totals = [
            'nilaipenjualan' => 0,
            'saldopenjualan' => 0
        ];
        foreach ($tagihans as $tagihan) {
            $totals['nilaipenjualan'] += (float)($tagihan['nilaipenjualan'] ?? 0);
            $totals['saldopenjualan'] += (float)($tagihan['saldopenjualan'] ?? 0);
        }

        // Calculate grand total from all data (not paginated)
        $grandTotals = $this->getGrandTotalsForReport($search, $kodecustomer, $statusJatuhTempo);

        // Get customers for dropdown
        $customerModel = new Mastercustomer();
        $customers = $customerModel->getAllForSelection();

        $data = [
            'tagihans' => $tagihans,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'search' => $search,
            'kodecustomer' => $kodecustomer,
            'statusJatuhTempo' => $statusJatuhTempo,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'customers' => $customers,
            'totals' => $totals,
            'grandTotals' => $grandTotals,
        ];

        $this->view('laporan/daftar-tagihan', $data);
    }

    private function getTagihansForReport($search = '', $kodecustomer = '', $statusJatuhTempo = 'semua', $sortBy = 'tanggalpenjualan', $sortOrder = 'DESC', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $tanggalSistem = date('Y-m-d');
        
        $where = ["hp.saldopenjualan > 0"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mc.namacustomer LIKE ? OR mc.namabadanusaha LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodecustomer)) {
            $where[] = "hp.kodecustomer = ?";
            $params[] = $kodecustomer;
        }

        if ($statusJatuhTempo === 'sudah') {
            $where[] = "hp.tanggaljatuhtempo < ?";
            $params[] = $tanggalSistem;
        } elseif ($statusJatuhTempo === 'belum') {
            $where[] = "(hp.tanggaljatuhtempo >= ? OR hp.tanggaljatuhtempo IS NULL)";
            $params[] = $tanggalSistem;
        }

        // Validate sort column
        $validSortColumns = ['nopenjualan', 'tanggalpenjualan', 'tanggaljatuhtempo', 'namacustomer', 'nilaipenjualan', 'saldopenjualan', 'umur'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'umur';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $whereClause = implode(' AND ', $where);
        
        // Determine order by column
        if ($sortBy === 'namacustomer') {
            $orderByColumn = 'mc.namacustomer';
        } elseif ($sortBy === 'umur') {
            $orderByColumn = 'DATEDIFF(CURDATE(), hp.tanggalpenjualan)';
        } else {
            $orderByColumn = "hp.{$sortBy}";
        }

        $sql = "SELECT 
                    hp.nopenjualan,
                    hp.tanggalpenjualan,
                    hp.tanggaljatuhtempo,
                    hp.nilaipenjualan,
                    hp.saldopenjualan,
                    mc.namacustomer,
                    mc.namabadanusaha,
                    mc.alamatcustomer,
                    DATEDIFF(CURDATE(), hp.tanggalpenjualan) AS umur
                FROM headerpenjualan hp
                LEFT JOIN mastercustomer mc ON hp.kodecustomer = mc.kodecustomer
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }

    private function countTagihansForReport($search = '', $kodecustomer = '', $statusJatuhTempo = 'semua') {
        $tanggalSistem = date('Y-m-d');
        
        $where = ["hp.saldopenjualan > 0"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mc.namacustomer LIKE ? OR mc.namabadanusaha LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodecustomer)) {
            $where[] = "hp.kodecustomer = ?";
            $params[] = $kodecustomer;
        }

        if ($statusJatuhTempo === 'sudah') {
            $where[] = "hp.tanggaljatuhtempo < ?";
            $params[] = $tanggalSistem;
        } elseif ($statusJatuhTempo === 'belum') {
            $where[] = "(hp.tanggaljatuhtempo >= ? OR hp.tanggaljatuhtempo IS NULL)";
            $params[] = $tanggalSistem;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) as total
                FROM headerpenjualan hp
                LEFT JOIN mastercustomer mc ON hp.kodecustomer = mc.kodecustomer
                WHERE {$whereClause}";
        
        $result = $this->db->fetchOne($sql, $params);
        return $result ? (int)$result['total'] : 0;
    }

    private function getGrandTotalsForReport($search = '', $kodecustomer = '', $statusJatuhTempo = 'semua') {
        $tanggalSistem = date('Y-m-d');
        
        $where = ["hp.saldopenjualan > 0"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mc.namacustomer LIKE ? OR mc.namabadanusaha LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodecustomer)) {
            $where[] = "hp.kodecustomer = ?";
            $params[] = $kodecustomer;
        }

        if ($statusJatuhTempo === 'sudah') {
            $where[] = "hp.tanggaljatuhtempo < ?";
            $params[] = $tanggalSistem;
        } elseif ($statusJatuhTempo === 'belum') {
            $where[] = "(hp.tanggaljatuhtempo >= ? OR hp.tanggaljatuhtempo IS NULL)";
            $params[] = $tanggalSistem;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    SUM(hp.nilaipenjualan) as total_nilaipenjualan,
                    SUM(hp.saldopenjualan) as total_saldopenjualan
                FROM headerpenjualan hp
                LEFT JOIN mastercustomer mc ON hp.kodecustomer = mc.kodecustomer
                WHERE {$whereClause}";
        
        $result = $this->db->fetchOne($sql, $params);
        return [
            'nilaipenjualan' => (float)($result['total_nilaipenjualan'] ?? 0),
            'saldopenjualan' => (float)($result['total_saldopenjualan'] ?? 0)
        ];
    }

    private function getAllTagihansForReport($search = '', $kodecustomer = '', $statusJatuhTempo = 'semua', $sortBy = 'umur', $sortOrder = 'DESC') {
        $tanggalSistem = date('Y-m-d');
        
        $where = ["hp.saldopenjualan > 0"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(mc.namacustomer LIKE ? OR mc.namabadanusaha LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($kodecustomer)) {
            $where[] = "hp.kodecustomer = ?";
            $params[] = $kodecustomer;
        }

        if ($statusJatuhTempo === 'sudah') {
            $where[] = "hp.tanggaljatuhtempo < ?";
            $params[] = $tanggalSistem;
        } elseif ($statusJatuhTempo === 'belum') {
            $where[] = "(hp.tanggaljatuhtempo >= ? OR hp.tanggaljatuhtempo IS NULL)";
            $params[] = $tanggalSistem;
        }

        // Validate sort column
        $validSortColumns = ['nopenjualan', 'tanggalpenjualan', 'tanggaljatuhtempo', 'namacustomer', 'nilaipenjualan', 'saldopenjualan', 'umur'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'umur';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        $whereClause = implode(' AND ', $where);
        
        // Determine order by column
        if ($sortBy === 'namacustomer') {
            $orderByColumn = 'mc.namacustomer';
        } elseif ($sortBy === 'umur') {
            $orderByColumn = 'DATEDIFF(CURDATE(), hp.tanggalpenjualan)';
        } else {
            $orderByColumn = "hp.{$sortBy}";
        }

        $sql = "SELECT 
                    hp.nopenjualan,
                    hp.tanggalpenjualan,
                    hp.tanggaljatuhtempo,
                    hp.nilaipenjualan,
                    hp.saldopenjualan,
                    mc.namacustomer,
                    mc.namabadanusaha,
                    mc.alamatcustomer,
                    DATEDIFF(CURDATE(), hp.tanggalpenjualan) AS umur
                FROM headerpenjualan hp
                LEFT JOIN mastercustomer mc ON hp.kodecustomer = mc.kodecustomer
                WHERE {$whereClause}
                ORDER BY {$orderByColumn} {$sortOrder}";
        
        return $this->db->fetchAll($sql, $params);
    }

    private function exportExcelTagihan($tagihans) {
        $filename = 'Laporan_Daftar_Tagihan_' . date('YmdHis') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add BOM for UTF-8 to ensure Excel displays correctly
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['No.Faktur', 'Tanggal Penjualan', 'Umur', 'Jatuh Tempo', 'Customer', 'Alamat Customer', 'Nilai Penjualan', 'Saldo Tagihan'], ';');

        // Data
        $tanggalSistem = new DateTime();
        foreach ($tagihans as $tagihan) {
            // Hitung umur
            $umur = '-';
            if (!empty($tagihan['tanggalpenjualan'])) {
                try {
                    $tanggalPenjualan = new DateTime($tagihan['tanggalpenjualan']);
                    $diff = $tanggalSistem->diff($tanggalPenjualan);
                    $umur = $diff->days;
                } catch (Exception $e) {
                    $umur = '-';
                }
            }

            // Format customer dengan namabadanusaha
            $customerDisplay = $tagihan['namacustomer'] ?? '';
            if ($customerDisplay && !empty($tagihan['namabadanusaha'])) {
                $customerDisplay .= ', ' . $tagihan['namabadanusaha'];
            }

            fputcsv($output, [
                $tagihan['nopenjualan'] ?? '',
                $tagihan['tanggalpenjualan'] ? date('d/m/Y', strtotime($tagihan['tanggalpenjualan'])) : '',
                $umur !== '-' ? $umur . ' hari' : '-',
                $tagihan['tanggaljatuhtempo'] ? date('d/m/Y', strtotime($tagihan['tanggaljatuhtempo'])) : '',
                $customerDisplay ?: '',
                $tagihan['alamatcustomer'] ?? '',
                number_format((float)($tagihan['nilaipenjualan'] ?? 0), 0, ',', '.'),
                number_format((float)($tagihan['saldopenjualan'] ?? 0), 0, ',', '.')
            ], ';');
        }

        fclose($output);
    }

    private function exportPDFTagihan($tagihans) {
        $this->generateAndDownloadPDFTagihan($tagihans);
    }

    private function generateAndDownloadPDFTagihan($data) {
        $filename = 'Daftar_Tagihan_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $pdf = new LaporanPDF('P', 'mm', 'A4');
        $pdf->reportTitle = 'Laporan Daftar Tagihan';
        $pdf->reportSubtitle = "Tanggal Laporan: " . date('d F Y') . "\nTotal Transaksi: " . count($data);
        $pdf->printedBy = Auth::user()['namalengkap'] ?? 'System';
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $header = ['No', 'No.Faktur', 'Tanggal', 'Umur', 'Jatuh Tempo', 'Customer', 'Nilai', 'Saldo'];
        $widths = [8, 25, 20, 10, 20, 57, 25, 25];

        $pdf->TableHeader($header, $widths);

        $pdf->SetFont('Helvetica', '', 7);
        $no = 1;
        $tanggalSistem = new DateTime();
        $totalNilai = 0;
        $totalSaldo = 0;

        foreach ($data as $d) {
            $umur = '-';
            if (!empty($d['tanggalpenjualan'])) {
                try {
                    $tgl = new DateTime($d['tanggalpenjualan']);
                    $diff = $tanggalSistem->diff($tgl);
                    $umur = $diff->days;
                } catch (Exception $e) {}
            }

            $customer = $d['namacustomer'] ?? '';
            if ($customer && !empty($d['namabadanusaha'])) {
                $customer .= ', ' . $d['namabadanusaha'];
            }

            $nilai = (float)($d['nilaipenjualan'] ?? 0);
            $saldo = (float)($d['saldopenjualan'] ?? 0);
            $totalNilai += $nilai;
            $totalSaldo += $saldo;

            $pdf->Cell($widths[0], 6, $no++, 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $d['nopenjualan'] ?? '-', 1, 0, 'L');
            $pdf->Cell($widths[2], 6, $d['tanggalpenjualan'] ? date('d/m/Y', strtotime($d['tanggalpenjualan'])) : '-', 1, 0, 'C');
            $pdf->Cell($widths[3], 6, ($umur !== '-' ? $umur . ' h' : '-'), 1, 0, 'C');
            $pdf->Cell($widths[4], 6, $d['tanggaljatuhtempo'] ? date('d/m/Y', strtotime($d['tanggaljatuhtempo'])) : '-', 1, 0, 'C');
            $pdf->Cell($widths[5], 6, substr($customer, 0, 40), 1, 0, 'L');
            $pdf->Cell($widths[6], 6, number_format($nilai, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[7], 6, number_format($saldo, 0, ',', '.'), 1, 0, 'R');
            $pdf->Ln();
        }

        // Total row
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell($widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4] + $widths[5], 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell($widths[6], 6, number_format($totalNilai, 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell($widths[7], 6, number_format($totalSaldo, 0, ',', '.'), 1, 0, 'R');

        $pdf->Output('D', $filename);
    }
}
