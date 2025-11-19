<?php
class OmsetController extends Controller {
    private $omsetModel;

    public function __construct() {
        parent::__construct();
        $this->omsetModel = new Omset();
    }

    public function index() {
        Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

        $tahun = $_GET['tahun'] ?? date('Y');
        $bulan = $_GET['bulan'] ?? date('m');
        $export = $_GET['export'] ?? '';

        // Get kodesales filter for sales role
        $kodesales = null;
        $user = Auth::user();
        if (($user['role'] ?? '') === 'sales' && !empty($user['kodesales'])) {
            $kodesales = $user['kodesales'];
        }

        // Get all data for export, or paginated for display
        if (!empty($export)) {
            $omset = $this->omsetModel->getAll($tahun, $bulan, 1, 10000, $kodesales);
            
            if ($export === 'excel') {
                $this->exportExcel($omset, $tahun, $bulan);
            } elseif ($export === 'pdf') {
                $this->exportPDF($omset, $tahun, $bulan);
            }
            exit;
        }

        // For display, use pagination
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPageOptions = [10, 25, 50, 100, 200, 500, 1000];
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 100;
        $perPage = in_array($perPage, $perPageOptions, true) ? $perPage : 100;

        $omset = $this->omsetModel->getAll($tahun, $bulan, $page, $perPage, $kodesales);
        $total = $this->omsetModel->count($tahun, $bulan, $kodesales);
        $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

        // Calculate totals for current page
        $totals = [
            'jumlahfaktur' => 0,
            'penjualan' => 0,
            'returpenjualan' => 0,
            'penjualanbersih' => 0,
            'targetpenjualan' => 0,
            'penerimaantunai' => 0,
            'cnpenjualan' => 0,
            'pencairangiro' => 0,
            'penerimaanbersih' => 0,
            'targetpenerimaan' => 0
        ];

        foreach ($omset as $row) {
            $totals['jumlahfaktur'] += (float)($row['jumlahfaktur'] ?? 0);
            $totals['penjualan'] += (float)($row['penjualan'] ?? 0);
            $totals['returpenjualan'] += (float)($row['returpenjualan'] ?? 0);
            $totals['penjualanbersih'] += (float)($row['penjualanbersih'] ?? 0);
            $totals['targetpenjualan'] += (float)($row['targetpenjualan'] ?? 0);
            $totals['penerimaantunai'] += (float)($row['penerimaantunai'] ?? 0);
            $totals['cnpenjualan'] += (float)($row['cnpenjualan'] ?? 0);
            $totals['pencairangiro'] += (float)($row['pencairangiro'] ?? 0);
            $totals['penerimaanbersih'] += (float)($row['penerimaanbersih'] ?? 0);
            $totals['targetpenerimaan'] += (float)($row['targetpenerimaan'] ?? 0);
        }

        // Calculate percentage totals
        $totals['prosenpenjualan'] = $totals['targetpenjualan'] > 0 
            ? ($totals['penjualanbersih'] / $totals['targetpenjualan']) * 100 
            : 0;
        $totals['prosenpenerimaan'] = $totals['targetpenerimaan'] > 0 
            ? ($totals['penerimaanbersih'] / $totals['targetpenerimaan']) * 100 
            : 0;

        // Get distinct years for dropdown
        $years = $this->omsetModel->getDistinctYears();
        if (empty($years)) {
            $years = [date('Y')];
        }

        // Get user role for view selection
        $user = Auth::user();
        $userRole = $user['role'] ?? '';
        
        $data = [
            'omset' => $omset,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'years' => $years,
            'totals' => $totals,
            'userRole' => $userRole,
            'omsetData' => !empty($omset) && $userRole === 'sales' ? $omset[0] : null // Single record for sales
        ];

        $this->view('laporan/omset', $data);
    }

    private function exportExcel($omset, $tahun, $bulan) {
        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanText = isset($bulanNama[(int)$bulan]) ? $bulanNama[(int)$bulan] : $bulan;
        
        $filename = 'Laporan_Omset_' . $tahun . '_' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '_' . date('YmdHis') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        fputcsv($output, ['LAPORAN OMSET - ' . $bulanText . ' ' . $tahun], ';');
        fputcsv($output, []);

        fputcsv($output, [
            'Tahun', 'Bulan', 'Kode Sales', 'Nama Sales', 'Jumlah Faktur', 'Penjualan',
            'Retur Penjualan', 'Penjualan Bersih', 'Target Penjualan', 'Prosen Penjualan',
            'Penerimaan Tunai', 'CN Penjualan', 'Pencairan Giro', 'Penerimaan Bersih',
            'Target Penerimaan', 'Prosen Penerimaan'
        ], ';');

        foreach ($omset as $row) {
            fputcsv($output, [
                $row['tahun'] ?? '',
                $row['bulan'] ?? '',
                $row['kodesales'] ?? '',
                $row['namasales'] ?? '',
                $row['jumlahfaktur'] ?? 0,
                $row['penjualan'] ?? 0,
                $row['returpenjualan'] ?? 0,
                $row['penjualanbersih'] ?? 0,
                $row['targetpenjualan'] ?? 0,
                $row['prosenpenjualan'] ?? 0,
                $row['penerimaantunai'] ?? 0,
                $row['cnpenjualan'] ?? 0,
                $row['pencairangiro'] ?? 0,
                $row['penerimaanbersih'] ?? 0,
                $row['targetpenerimaan'] ?? 0,
                $row['prosenpenerimaan'] ?? 0
            ], ';');
        }

        fclose($output);
    }

    private function exportPDF($omset, $tahun, $bulan) {
        $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanText = isset($bulanNama[(int)$bulan]) ? $bulanNama[(int)$bulan] : $bulan;

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Omset</title>
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
            font-size: 8pt;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 16pt;
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
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9pt;
            padding-top: 10px;
            border-top: 1px solid #333;
        }
    </style>
</head>
<body>
    <h1>LAPORAN OMSET</h1>
    <div class="header-info">
        <p><strong>Periode:</strong> ' . $bulanText . ' ' . $tahun . '</p>
        <p><strong>Tanggal Cetak:</strong> ' . date('d/m/Y H:i:s') . '</p>
        <p><strong>Total Data:</strong> ' . number_format(count($omset)) . ' record</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 4%;">Tahun</th>
                <th style="width: 4%;">Bulan</th>
                <th style="width: 6%;">Kode Sales</th>
                <th style="width: 10%;">Nama Sales</th>
                <th style="width: 5%;">Jml Faktur</th>
                <th style="width: 7%;">Penjualan</th>
                <th style="width: 6%;">Retur</th>
                <th style="width: 7%;">Penj. Bersih</th>
                <th style="width: 7%;">Target</th>
                <th style="width: 5%;">%</th>
                <th style="width: 7%;">Tunai</th>
                <th style="width: 6%;">CN</th>
                <th style="width: 7%;">Giro</th>
                <th style="width: 7%;">Terima Bersih</th>
                <th style="width: 7%;">Target</th>
                <th style="width: 5%;">%</th>
            </tr>
        </thead>
        <tbody>';

        $no = 1;
        foreach ($omset as $row) {
            $html .= '<tr>
                <td style="text-align: center;">' . $no++ . '</td>
                <td style="text-align: center;">' . htmlspecialchars($row['tahun'] ?? '-') . '</td>
                <td style="text-align: center;">' . htmlspecialchars($row['bulan'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['kodesales'] ?? '-') . '</td>
                <td>' . htmlspecialchars($row['namasales'] ?? '-') . '</td>
                <td class="text-right">' . number_format((float)($row['jumlahfaktur'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['penjualan'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['returpenjualan'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['penjualanbersih'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['targetpenjualan'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['prosenpenjualan'] ?? 0), 2, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['penerimaantunai'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['cnpenjualan'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['pencairangiro'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['penerimaanbersih'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['targetpenerimaan'] ?? 0), 0, ',', '.') . '</td>
                <td class="text-right">' . number_format((float)($row['prosenpenerimaan'] ?? 0), 2, ',', '.') . '</td>
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

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }
}

