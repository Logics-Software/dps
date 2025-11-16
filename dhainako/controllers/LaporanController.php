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
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
        $perPage = in_array($perPage, [25, 50, 100, 200, 500]) ? $perPage : 50;

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

    private function getBarangsForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC', $page = 1, $perPage = 50) {
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
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Barang</title>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            body {
                margin: 0;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 20px;
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
            text-align: right;
            font-size: 9pt;
            padding-top: 10px;
            border-top: 1px solid #333;
        }
        .no-print {
            display: none;
        }
    </style>
</head>
<body>
    <h1>LAPORAN DAFTAR BARANG</h1>
    <div class="header-info">
        <p><strong>Tanggal Cetak:</strong> ' . date('d/m/Y H:i:s') . '</p>
        <p><strong>Total Data:</strong> ' . number_format(count($barangs)) . ' barang</p>
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
        foreach ($barangs as $barang) {
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
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>';

        // Output HTML that can be printed as PDF by browser
        header('Content-Type: text/html; charset=utf-8');
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
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
        $perPage = in_array($perPage, [25, 50, 100, 200, 500]) ? $perPage : 50;

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

    private function getStoksForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC', $page = 1, $perPage = 50) {
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
        $validSortColumns = ['namabarang', 'satuan', 'pabrik', 'stok'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'namabarang' => 'mb.namabarang',
            'satuan' => 'mb.satuan',
            'pabrik' => 'tp.namapabrik',
            'stok' => 'mb.stokakhir'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.namabarang,
                    mb.satuan,
                    tp.namapabrik AS pabrik,
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
        $validSortColumns = ['namabarang', 'satuan', 'pabrik', 'stok'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'namabarang';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        // Map sort column to actual database column
        $sortColumnMap = [
            'namabarang' => 'mb.namabarang',
            'satuan' => 'mb.satuan',
            'pabrik' => 'tp.namapabrik',
            'stok' => 'mb.stokakhir'
        ];
        $orderByColumn = $sortColumnMap[$sortBy] ?? 'mb.namabarang';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    mb.namabarang,
                    mb.satuan,
                    tp.namapabrik AS pabrik,
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
        fputcsv($output, ['Nama Barang', 'Satuan', 'Pabrik', 'Stok'], ';');

        // Data
        foreach ($barangs as $barang) {
            fputcsv($output, [
                $barang['namabarang'] ?? '',
                $barang['satuan'] ?? '',
                $barang['pabrik'] ?? '',
                $barang['stok'] ?? '0'
            ], ';');
        }

        fclose($output);
    }

    private function exportPDFStok($barangs) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Stok</title>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
            body {
                margin: 0;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 20px;
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
            text-align: right;
            font-size: 9pt;
            padding-top: 10px;
            border-top: 1px solid #333;
        }
        .no-print {
            display: none;
        }
    </style>
</head>
<body>
    <h1>LAPORAN DAFTAR STOK</h1>
    <div class="header-info">
        <p><strong>Tanggal Cetak:</strong> ' . date('d/m/Y H:i:s') . '</p>
        <p><strong>Total Data:</strong> ' . number_format(count($barangs)) . ' barang</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Nama Barang</th>
                <th style="width: 15%;">Satuan</th>
                <th style="width: 25%;">Pabrik</th>
                <th style="width: 15%;">Stok</th>
            </tr>
        </thead>
        <tbody>';

        $no = 1;
        foreach ($barangs as $barang) {
            $html .= '<tr>
                <td style="text-align: center;">' . $no++ . '</td>
                <td>' . htmlspecialchars($barang['namabarang'] ?? '-') . '</td>
                <td style="text-align: center;">' . htmlspecialchars($barang['satuan'] ?? '-') . '</td>
                <td>' . htmlspecialchars($barang['pabrik'] ?? '-') . '</td>
                <td style="text-align: right;">' . number_format((float)($barang['stok'] ?? 0), 0, ',', '.') . '</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>
    <div class="footer">
        <p><strong>Dicetak oleh:</strong> ' . htmlspecialchars(Auth::user()['namalengkap'] ?? 'System') . '</p>
        <p><strong>Tanggal:</strong> ' . date('d F Y, H:i:s') . '</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>';

        // Output HTML that can be printed as PDF by browser
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
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
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
        $perPage = in_array($perPage, [25, 50, 100, 200, 500]) ? $perPage : 50;

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

    private function getHargasForReport($search = '', $kodepabrik = '', $kodegolongan = '', $kondisiStok = 'semua', $sortBy = 'namabarang', $sortOrder = 'ASC', $page = 1, $perPage = 50) {
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
        fputcsv($output, ['Nama Barang', 'Satuan', 'Pabrik', 'Harga Jual', 'Discount Jual'], ';');

        // Data
        foreach ($barangs as $barang) {
            fputcsv($output, [
                $barang['namabarang'] ?? '',
                $barang['satuan'] ?? '',
                $barang['pabrik'] ?? '',
                $barang['hargajual'] ?? '0',
                number_format((float)($barang['discountjual'] ?? 0), 2, ',', '.')
            ], ';');
        }

        fclose($output);
    }

    private function exportPDFHarga($barangs) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Harga</title>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
            body {
                margin: 0;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 20px;
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
            text-align: right;
            font-size: 9pt;
            padding-top: 10px;
            border-top: 1px solid #333;
        }
        .no-print {
            display: none;
        }
    </style>
</head>
<body>
    <h1>LAPORAN DAFTAR HARGA</h1>
    <div class="header-info">
        <p><strong>Tanggal Cetak:</strong> ' . date('d/m/Y H:i:s') . '</p>
        <p><strong>Total Data:</strong> ' . number_format(count($barangs)) . ' barang</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Nama Barang</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 20%;">Pabrik</th>
                <th style="width: 17.5%;">Harga Jual</th>
                <th style="width: 17.5%;">Discount</th>
            </tr>
        </thead>
        <tbody>';

        $no = 1;
        foreach ($barangs as $barang) {
            $html .= '<tr>
                <td style="text-align: center;">' . $no++ . '</td>
                <td>' . htmlspecialchars($barang['namabarang'] ?? '-') . '</td>
                <td style="text-align: center;">' . htmlspecialchars($barang['satuan'] ?? '-') . '</td>
                <td>' . htmlspecialchars($barang['pabrik'] ?? '-') . '</td>
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
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>';

        // Output HTML that can be printed as PDF by browser
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }
}

