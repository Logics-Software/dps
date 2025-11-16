<?php
class ApiMasterbarangController extends Controller {
    public function index() {
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        switch ($method) {
            case 'GET':
                $this->getMasterbarang();
                break;
            case 'POST':
                $this->createMasterbarang();
                break;
            case 'PUT':
            case 'PATCH':
                $this->updateMasterbarang();
                break;
            case 'DELETE':
                $this->deleteMasterbarang();
                break;
            default:
                $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }
    }

    private function getMasterbarang() {
        $id = $_GET['id'] ?? null;
        $kodebarang = $_GET['kodebarang'] ?? null;

        $masterbarangModel = new Masterbarang();

        if ($id) {
            $item = $masterbarangModel->findById($id);
            if ($item) {
                $this->json(['success' => true, 'data' => $item]);
            } else {
                $this->json(['success' => false, 'message' => 'Masterbarang not found'], 404);
            }
            return;
        }

        if ($kodebarang) {
            $item = $masterbarangModel->findByKodebarang($kodebarang);
            if ($item) {
                $this->json(['success' => true, 'data' => $item]);
            } else {
                $this->json(['success' => false, 'message' => 'Masterbarang not found'], 404);
            }
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 100;
        $search = $_GET['search'] ?? '';
        $sortBy = $_GET['sort_by'] ?? 'id';
        $sortOrder = $_GET['sort_order'] ?? 'ASC';
        $kodepabrik = $_GET['kodepabrik'] ?? '';
        $kodegolongan = $_GET['kodegolongan'] ?? '';
        $kodesupplier = $_GET['kodesupplier'] ?? '';
        $status = $_GET['status'] ?? '';

        $items = $masterbarangModel->getAll(
            $page,
            $perPage,
            $search,
            $sortBy,
            $sortOrder,
            $kodepabrik,
            $kodegolongan,
            $kodesupplier,
            $status
        );
        $total = $masterbarangModel->count($search, $kodepabrik, $kodegolongan, $kodesupplier, $status);

        $this->json([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $perPage > 0 ? (int)ceil($total / $perPage) : 1
            ]
        ]);
    }

    private function createMasterbarang() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        $required = ['kodebarang', 'namabarang'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                $this->json(['success' => false, 'message' => "Field {$field} is required"], 400);
                return;
            }
        }

        $masterbarangModel = new Masterbarang();

        if ($masterbarangModel->findByKodebarang($input['kodebarang'])) {
            $this->json(['success' => false, 'message' => 'Kode barang already exists'], 400);
            return;
        }

        $data = [
            'kodebarang' => $input['kodebarang'],
            'namabarang' => $input['namabarang'],
            'satuan' => $input['satuan'] ?? null,
            'kodepabrik' => $input['kodepabrik'] ?? null,
            'kodegolongan' => $input['kodegolongan'] ?? null,
            'kodesupplier' => $input['kodesupplier'] ?? null,
            'kandungan' => $input['kandungan'] ?? null,
            'oot' => $input['oot'] ?? 'tidak',
            'prekursor' => $input['prekursor'] ?? 'tidak',
            'nie' => $input['nie'] ?? null,
            'hpp' => $input['hpp'] ?? null,
            'hargabeli' => $input['hargabeli'] ?? null,
            'discountbeli' => $input['discountbeli'] ?? null,
            'hargajual' => $input['hargajual'] ?? null,
            'discountjual' => $input['discountjual'] ?? null,
            'stokakhir' => $input['stokakhir'] ?? null,
            'status' => $input['status'] ?? 'aktif'
        ];

        $id = $masterbarangModel->create($data);
        $item = $masterbarangModel->findById($id);

        $this->json(['success' => true, 'message' => 'Masterbarang created', 'data' => $item], 201);
    }

    private function updateMasterbarang() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            parse_str(file_get_contents('php://input'), $parsedData);
            $input = $parsedData ?: $_POST;
        }

        $id = $input['id'] ?? $_GET['id'] ?? null;
        $kodebarang = $input['kodebarang'] ?? $_GET['kodebarang'] ?? null;

        if (!$id && !$kodebarang) {
            $this->json(['success' => false, 'message' => 'ID or kodebarang is required'], 400);
            return;
        }

        $masterbarangModel = new Masterbarang();

        if ($id) {
            $item = $masterbarangModel->findById($id);
        } else {
            $item = $masterbarangModel->findByKodebarang($kodebarang);
            if ($item) {
                $id = $item['id'];
            }
        }

        if (!$item) {
            $this->json(['success' => false, 'message' => 'Masterbarang not found'], 404);
            return;
        }

        $data = [];
        $allowedFields = [
            'kodebarang',
            'namabarang',
            'satuan',
            'kodepabrik',
            'kodegolongan',
            'kodesupplier',
            'kandungan',
            'oot',
            'prekursor',
            'nie',
            'hpp',
            'hargabeli',
            'discountbeli',
            'hargajual',
            'discountjual',
            'stokakhir',
            'status'
        ];

        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        if (isset($data['kodebarang']) && $data['kodebarang'] !== $item['kodebarang']) {
            $existing = $masterbarangModel->findByKodebarang($data['kodebarang']);
            if ($existing && $existing['id'] != $id) {
                $this->json(['success' => false, 'message' => 'Kode barang already exists'], 400);
                return;
            }
        }

        if (empty($data)) {
            $this->json(['success' => false, 'message' => 'No data to update'], 400);
            return;
        }

        $masterbarangModel->update($id, $data);
        $updated = $masterbarangModel->findById($id);

        $this->json(['success' => true, 'message' => 'Masterbarang updated', 'data' => $updated]);
    }

    private function deleteMasterbarang() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            parse_str(file_get_contents('php://input'), $parsedData);
            $input = $parsedData ?: $_GET;
        }

        $id = $input['id'] ?? $_GET['id'] ?? null;
        $kodebarang = $input['kodebarang'] ?? $_GET['kodebarang'] ?? null;

        if (!$id && !$kodebarang) {
            $this->json(['success' => false, 'message' => 'ID or kodebarang is required'], 400);
            return;
        }

        $masterbarangModel = new Masterbarang();

        if ($id) {
            $item = $masterbarangModel->findById($id);
        } else {
            $item = $masterbarangModel->findByKodebarang($kodebarang);
            if ($item) {
                $id = $item['id'];
            }
        }

        if (!$item) {
            $this->json(['success' => false, 'message' => 'Masterbarang not found'], 404);
            return;
        }

        $masterbarangModel->delete($id);
        $this->json(['success' => true, 'message' => 'Masterbarang deleted']);
    }
}


