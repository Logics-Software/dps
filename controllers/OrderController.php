<?php
class OrderController extends Controller {
	private $headerModel;
	private $detailModel;
	private $customerModel;
	private $barangModel;
	private $orderFileModel;

	public function __construct() {
		parent::__construct();
		$this->headerModel = new Headerorder();
		$this->detailModel = new Detailorder();
		$this->customerModel = new Mastercustomer();
		$this->barangModel = new Masterbarang();
		$this->orderFileModel = new OrderFile();
	}

	public function index() {
		Auth::requireRole(['admin', 'manajemen', 'operator', 'sales']);

		$user = Auth::user();
		$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
		$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
		$perPage = in_array($perPage, [10, 25, 50, 100, 200, 500, 1000]) ? $perPage : 10;
		$search = trim($_GET['search'] ?? '');
		$status = trim($_GET['status'] ?? '');
		$dateFilter = $_GET['periode'] ?? ($_GET['date_filter'] ?? 'today');
		$startDate = $_GET['start_date'] ?? '';
		$endDate = $_GET['end_date'] ?? '';

		[$computedStartDate, $computedEndDate] = $this->computeDateRange($dateFilter, $startDate, $endDate);

		$sortBy = $_GET['sort_by'] ?? 'tanggalorder';
		$sortOrder = strtoupper($_GET['sort_order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
		
		$options = [
			'page' => $page,
			'per_page' => $perPage,
			'search' => $search,
			'status' => $status,
			'start_date' => $computedStartDate,
			'end_date' => $computedEndDate,
			'sort_by' => $sortBy,
			'sort_order' => $sortOrder
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
			'rawEndDate' => $endDate,
			'sortBy' => $sortBy,
			'sortOrder' => $sortOrder
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
				$message = 'Order berhasil dibuat';
				if (isset($result['warning'])) {
					Session::flash('warning', $result['warning']);
				} else {
					Session::flash('success', $message);
				}
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
			'customersByStatusJson' => json_encode($customersByStatus),
			'backUrl' => $_GET['back'] ?? '/orders' // Custom back URL from query parameter or default
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
		$orderFiles = $this->orderFileModel->listByOrder($noorder);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$result = $this->processFormData($noorder, $user, false, $order);
			if ($result['success']) {
				$message = 'Order berhasil diperbarui';
				if (isset($result['warning'])) {
					Session::flash('warning', $result['warning']);
				} else {
					Session::flash('success', $message);
				}
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
			'orderFiles' => $orderFiles,
			'statuspkp' => $_POST['statuspkp'] ?? ($order['statuspkp'] ?? 'pkp'),
			'barangsJson' => json_encode($barangs),
			'customersByStatusJson' => json_encode($customersByStatus),
			'backUrl' => $_GET['back'] ?? '/orders' // Custom back URL from query parameter or default
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
		$orderFiles = $this->orderFileModel->listByOrder($noorder);

		$data = [
			'order' => $order,
			'details' => $details,
			'orderFiles' => $orderFiles,
			'backUrl' => $_GET['back'] ?? '/orders' // Custom back URL from query parameter or default
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
			// Delete associated files first
			$this->orderFileModel->deleteByOrder($noorder);
			// Then delete the order
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
			case 'year':
				$startDate = date('Y-01-01');
				$endDate = date('Y-12-31');
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

			// Handle file uploads
			if (isset($_FILES['order_files']) && !empty($_FILES['order_files']['name'])) {
				$uploadErrors = $this->handleFileUploads($noorder, $_FILES['order_files'], $user);
				if (!empty($uploadErrors)) {
					// File upload errors, but order is already saved
					// Return success with warning message
					return ['success' => true, 'warning' => 'Order berhasil disimpan, namun beberapa file gagal diupload: ' . implode(', ', $uploadErrors)];
				}
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

	private function handleFileUploads($noorder, $files, $user) {
		$appConfig = require __DIR__ . '/../config/app.php';
		$uploadPath = $appConfig['upload_path'] . 'orders/';
		$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
		$maxFileSize = 5242880; // 5MB
		$maxFiles = 5;

		// Ensure upload directory exists and is writable
		if (!is_dir($uploadPath)) {
			if (!mkdir($uploadPath, 0755, true)) {
				return ['Gagal membuat folder upload. Pastikan folder uploads/orders/ dapat ditulis.'];
			}
		}

		if (!is_writable($uploadPath)) {
			return ['Folder upload tidak dapat ditulis. Pastikan folder uploads/orders/ memiliki permission yang benar.'];
		}

		$errors = [];
		$fileCount = 0;

		// Count non-empty files
		foreach ($files['name'] as $name) {
			if (!empty($name)) {
				$fileCount++;
			}
		}

		// Check max files limit
		if ($fileCount > $maxFiles) {
			return ['Maksimal ' . $maxFiles . ' file yang dapat diupload'];
		}

		// Get existing files count
		$existingFiles = $this->orderFileModel->listByOrder($noorder);
		$existingCount = count($existingFiles);
		if (($existingCount + $fileCount) > $maxFiles) {
			return ['Total file tidak boleh melebihi ' . $maxFiles . ' file (sudah ada ' . $existingCount . ' file)'];
		}

		$uploadedCount = 0;
		$totalFiles = count($files['name']);

		for ($i = 0; $i < $totalFiles; $i++) {
			// Skip empty file names
			if (empty($files['name'][$i])) {
				continue;
			}

			if ($files['error'][$i] !== UPLOAD_ERR_OK) {
				$errorMsg = 'Error upload';
				switch ($files['error'][$i]) {
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						$errorMsg = 'File terlalu besar';
						break;
					case UPLOAD_ERR_PARTIAL:
						$errorMsg = 'File hanya terupload sebagian';
						break;
					case UPLOAD_ERR_NO_FILE:
						$errorMsg = 'Tidak ada file yang diupload';
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						$errorMsg = 'Folder temporary tidak ditemukan';
						break;
					case UPLOAD_ERR_CANT_WRITE:
						$errorMsg = 'Gagal menulis file ke disk';
						break;
					case UPLOAD_ERR_EXTENSION:
						$errorMsg = 'Upload dihentikan oleh extension PHP';
						break;
				}
				$errors[] = $files['name'][$i] . ' - ' . $errorMsg;
				continue;
			}

			$originalName = $files['name'][$i];
			$tmpName = $files['tmp_name'][$i];
			$fileSize = $files['size'][$i];
			$fileType = $files['type'][$i];
			$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

			// Validate file type
			if (!in_array($extension, $allowedTypes)) {
				$errors[] = $originalName . ' - Format file tidak diizinkan (hanya: ' . implode(', ', $allowedTypes) . ')';
				continue;
			}

			// Validate file size
			if ($fileSize > $maxFileSize) {
				$errors[] = $originalName . ' - Ukuran file terlalu besar (maksimal ' . ($maxFileSize / 1024 / 1024) . 'MB)';
				continue;
			}

			// Generate unique filename
			$filename = uniqid() . '_' . time() . '.' . $extension;
			$targetPath = $uploadPath . $filename;
			$relativePath = 'uploads/orders/' . $filename;

			// Move uploaded file
			if (move_uploaded_file($tmpName, $targetPath)) {
				// Save file info to database
				try {
					$this->orderFileModel->create([
						'noorder' => $noorder,
						'filename' => $filename,
						'original_filename' => $originalName,
						'file_path' => $relativePath,
						'file_type' => $fileType,
						'file_size' => $fileSize,
						'uploaded_by' => $user['id'] ?? null
					]);
					$uploadedCount++;
				} catch (Exception $e) {
					// If database save fails, delete the uploaded file
					if (file_exists($targetPath)) {
						unlink($targetPath);
					}
					$errors[] = $originalName . ' - Gagal menyimpan ke database: ' . $e->getMessage();
				}
			} else {
				$errors[] = $originalName . ' - Gagal menyimpan file ke server';
			}
		}

		return $errors;
	}
}


