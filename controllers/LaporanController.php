<?php
class LaporanController extends Controller {
    private $barangModel;
    private $pabrikModel;
    private $golonganModel;

    public function __construct() {
        parent::__construct();
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
        $html = '';
        $filename = '';

        if ($reportType === 'daftar-barang') {
            $filename = 'Daftar_Barang_' . date('Y-m-d_H-i-s') . '.pdf';
            
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 15px;
        }
        h1 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18pt;
            color: #333;
        }
        .header-info {
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .header-info p {
            margin: 5px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        td {
            background-color: #fff;
        }
        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>📋 Laporan Daftar Barang</h1>
    <div class="header-info">
        <p><strong>Tanggal Laporan:</strong> ' . date('d F Y, H:i:s') . '</p>
        <p><strong>Total Barang:</strong> ' . count($data) . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Kode Barang</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 15%;">Pabrik</th>
                <th style="width: 15%;">Golongan</th>
                <th style="width: 20%;">Kandungan</th>
            </tr>
        </thead>
        <tbody>';

            $no = 1;
            foreach ($data as $barang) {
                $html .= '<tr>
                    <td style="text-align: center;">' . $no++ . '</td>
                    <td>' . htmlspecialchars($barang['kodebarang'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($barang['namabarang'] ?? '-') . '</td>
                    <td style="text-align: center;">' . htmlspecialchars($barang['satuan'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($barang['pabrik'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($barang['golongan'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($barang['kandungan'] ?? '-') . '</td>
                </tr>';
            }

            $html .= '</tbody>
        </table>
        <div class="footer">
            <p><strong>Dicetak oleh:</strong> ' . htmlspecialchars(Auth::user()['namalengkap'] ?? 'System') . '</p>
            <p><strong>Tanggal:</strong> ' . date('d F Y, H:i:s') . '</p>
        </div>
    </body>
</html>';
        }

        // Fallback: Create downloadable HTML that can be printed as PDF in browser
        $this->downloadAsHTML($html, $filename);
    }

    private function downloadAsHTML($html, $filename) {
        // Send as downloadable file
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.html"');
        header('Pragma: no-cache');
        header('Expires: 0');
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
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Stok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 15px;
        }
        h1 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18pt;
            color: #333;
        }
        .header-info {
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .header-info p {
            margin: 5px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        td {
            background-color: #fff;
        }
        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>📋 Laporan Daftar Stok</h1>
    <div class="header-info">
        <p><strong>Tanggal Laporan:</strong> ' . date('d F Y, H:i:s') . '</p>
        <p><strong>Total Barang:</strong> ' . count($data) . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 30%;">Nama Barang</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 15%;">Harga Jual</th>
                <th style="width: 12%;">Discount</th>
                <th style="width: 12%;">Kondisi</th>
                <th style="width: 12%;">Stok</th>
            </tr>
        </thead>
        <tbody>';

        $no = 1;
        foreach ($data as $barang) {
            $html .= '<tr>
                <td style="text-align: center;">' . $no++ . '</td>
                <td>' . htmlspecialchars($barang['namabarang'] ?? '-') . '</td>
                <td style="text-align: center;">' . htmlspecialchars($barang['satuan'] ?? '-') . '</td>
                <td style="text-align: right;">' . number_format((float)($barang['hargajual'] ?? 0), 0, ',', '.') . '</td>
                <td style="text-align: right;">' . number_format((float)($barang['discountjual'] ?? 0), 2, ',', '.') . '%</td>
                <td style="text-align: left;">' . htmlspecialchars($barang['kondisi'] ?? '-') . '</td>
                <td style="text-align: right;">' . number_format((float)($barang['stok'] ?? 0), 0, ',', '.') . '</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>
    <div class="footer">
        <p><strong>Dicetak oleh:</strong> ' . htmlspecialchars(Auth::user()['namalengkap'] ?? 'System') . '</p>
        <p><strong>Tanggal:</strong> ' . date('d F Y, H:i:s') . '</p>
    </div>
</body>
</html>';

        $this->downloadAsHTML($html, $filename);
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
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Harga</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 15px;
        }
        h1 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18pt;
            color: #333;
        }
        .header-info {
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .header-info p {
            margin: 5px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        td {
            background-color: #fff;
        }
        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>📋 Laporan Daftar Harga</h1>
    <div class="header-info">
        <p><strong>Tanggal Laporan:</strong> ' . date('d F Y, H:i:s') . '</p>
        <p><strong>Total Barang:</strong> ' . count($data) . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 15%;">Pabrik</th>
                <th style="width: 12%;">Kondisi</th>
                <th style="width: 10%;">ED</th>
                <th style="width: 13%;">Harga Jual</th>
                <th style="width: 13%;">Discount</th>
            </tr>
        </thead>
        <tbody>';

        $no = 1;
        foreach ($data as $barang) {
            $html .= '<tr>
                <td style="text-align: center;">' . $no++ . '</td>
                <td>' . htmlspecialchars($barang['namabarang'] ?? '-') . '</td>
                <td style="text-align: center;">' . htmlspecialchars($barang['satuan'] ?? '-') . '</td>
                <td>' . htmlspecialchars($barang['pabrik'] ?? '-') . '</td>
                <td>' . htmlspecialchars($barang['kondisi'] ?? '-') . '</td>
                <td>' . htmlspecialchars($barang['ed'] ?? '-') . '</td>
                <td style="text-align: right;">' . number_format((float)($barang['hargajual'] ?? 0), 0, ',', '.') . '</td>
                <td style="text-align: right;">' . number_format((float)($barang['discountjual'] ?? 0), 2, ',', '.') . '</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>
    <div class="footer">
        <p><strong>Dicetak oleh:</strong> ' . htmlspecialchars(Auth::user()['namalengkap'] ?? 'System') . '</p>
        <p><strong>Tanggal:</strong> ' . date('d F Y, H:i:s') . '</p>
    </div>
</body>
</html>';

        $this->downloadAsHTML($html, $filename);
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
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Tagihan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 15px;
        }
        h1 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 18pt;
            color: #333;
        }
        .header-info {
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .header-info p {
            margin: 5px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 7pt;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        td {
            background-color: #fff;
        }
        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }
        tr.total-row {
            background-color: #fff3cd;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>📋 Laporan Daftar Tagihan</h1>
    <div class="header-info">
        <p><strong>Tanggal Laporan:</strong> ' . date('d F Y, H:i:s') . '</p>
        <p><strong>Total Transaksi:</strong> ' . count($data) . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">No.Faktur</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 6%;">Umur</th>
                <th style="width: 10%;">Jatuh Tempo</th>
                <th style="width: 20%;">Customer</th>
                <th style="width: 18%;">Alamat</th>
                <th style="width: 10%;">Nilai Penjualan</th>
                <th style="width: 10%;">Saldo Tagihan</th>
            </tr>
        </thead>
        <tbody>';

        $no = 1;
        $tanggalSistem = new DateTime();
        $totalNilaiPenjualan = 0;
        $totalSaldoTagihan = 0;

        foreach ($data as $tagihan) {
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

            $customerDisplay = $tagihan['namacustomer'] ?? '';
            if ($customerDisplay && !empty($tagihan['namabadanusaha'])) {
                $customerDisplay .= ', ' . $tagihan['namabadanusaha'];
            }

            $nilaipenjualan = (float)($tagihan['nilaipenjualan'] ?? 0);
            $saldopenjualan = (float)($tagihan['saldopenjualan'] ?? 0);
            $totalNilaiPenjualan += $nilaipenjualan;
            $totalSaldoTagihan += $saldopenjualan;

            $html .= '<tr>
                <td style="text-align: center;">' . $no++ . '</td>
                <td>' . htmlspecialchars($tagihan['nopenjualan'] ?? '-') . '</td>
                <td style="text-align: center;">' . ($tagihan['tanggalpenjualan'] ? date('d/m/Y', strtotime($tagihan['tanggalpenjualan'])) : '-') . '</td>
                <td style="text-align: center;">' . ($umur !== '-' ? $umur . ' h' : '-') . '</td>
                <td style="text-align: center;">' . ($tagihan['tanggaljatuhtempo'] ? date('d/m/Y', strtotime($tagihan['tanggaljatuhtempo'])) : '-') . '</td>
                <td>' . htmlspecialchars($customerDisplay ?: '-') . '</td>
                <td>' . htmlspecialchars($tagihan['alamatcustomer'] ?? '-') . '</td>
                <td style="text-align: right;">' . number_format($nilaipenjualan, 0, ',', '.') . '</td>
                <td style="text-align: right;">' . number_format($saldopenjualan, 0, ',', '.') . '</td>
            </tr>';
        }

        // Grand Total
        $html .= '<tr class="total-row">
            <td colspan="8" style="text-align: center;">GRAND TOTAL</td>
            <td style="text-align: right;">' . number_format($totalNilaiPenjualan, 0, ',', '.') . '</td>
            <td style="text-align: right;">' . number_format($totalSaldoTagihan, 0, ',', '.') . '</td>
        </tr>';

        $html .= '</tbody>
    </table>
    <div class="footer">
        <p><strong>Dicetak oleh:</strong> ' . htmlspecialchars(Auth::user()['namalengkap'] ?? 'System') . '</p>
        <p><strong>Tanggal:</strong> ' . date('d F Y, H:i:s') . '</p>
    </div>
</body>
</html>';

        $this->downloadAsHTML($html, $filename);
    }
}

