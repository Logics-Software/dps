<?php
class ApiMasterbarangController extends Controller {
    public function index() {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Handle method override (Router already handles this, but keep for compatibility)
        if ($method === 'POST' && isset($_POST['_method'])) {
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

    /**
     * Get kodebarang from query string, handling plus sign correctly
     * PHP converts + to space in query strings, so we need to parse manually
     * Extract directly from raw query string and decode properly
     */
    private function getKodebarangFromQuery() {
        if (!isset($_SERVER['QUERY_STRING'])) {
            return null;
        }
        
        $queryString = $_SERVER['QUERY_STRING'];
        
        // Extract kodebarang value directly from raw query string
        // This preserves + signs that haven't been converted to spaces yet
        if (preg_match('/[&?]kodebarang=([^&]*)/', $queryString, $matches)) {
            $value = $matches[1];
            // Decode URL encoding (%XX) but preserve + signs
            // rawurldecode only decodes %XX, doesn't convert + to space
            $value = rawurldecode($value);
            return $value;
        }
        
        // Fallback to $_GET (but this will have + converted to space)
        return $_GET['kodebarang'] ?? null;
    }

    /**
     * Parse form-urlencoded data correctly, handling plus sign
     * PHP's parse_str() converts + to space, so we need to parse manually
     */
    private function parseFormUrlencoded($rawInput) {
        if (empty($rawInput)) {
            return [];
        }
        
        $result = [];
        
        // Split by & to get key-value pairs
        $pairs = explode('&', $rawInput);
        
        foreach ($pairs as $pair) {
            if (empty($pair)) {
                continue;
            }
            
            // Split key and value
            $parts = explode('=', $pair, 2);
            if (count($parts) !== 2) {
                continue;
            }
            
            $key = rawurldecode($parts[0]);
            $value = rawurldecode($parts[1]);
            
            // Handle array notation (e.g., field[]=value)
            if (preg_match('/^(.+)\[\]$/', $key, $matches)) {
                $arrayKey = $matches[1];
                if (!isset($result[$arrayKey])) {
                    $result[$arrayKey] = [];
                }
                $result[$arrayKey][] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

    private function getMasterbarang() {
        $id = $_GET['id'] ?? null;
        // Get kodebarang from query string - handle plus sign correctly
        // Try manual parsing first, then fallback to $_GET
        $kodebarang = $this->getKodebarangFromQuery();
        if ($kodebarang === null) {
            $kodebarang = $_GET['kodebarang'] ?? null;
        }

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
        // Use stored raw input if available (from Router), otherwise read from php://input
        $rawInput = $GLOBALS['_RAW_INPUT'] ?? file_get_contents('php://input');
        $input = json_decode($rawInput, true);

        if (!$input) {
            // Check if it's form-urlencoded (VB6 might send this way)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                // Parse form-urlencoded manually to handle + signs correctly
                $input = $this->parseFormUrlencoded($rawInput);
            } else {
                $input = $_POST;
            }
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
            'kondisi' => $input['kondisi'] ?? null,
            'ed' => $input['ed'] ?? null,
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
        // Use stored raw input if available (from Router), otherwise read from php://input
        $rawInput = $GLOBALS['_RAW_INPUT'] ?? file_get_contents('php://input');
        $input = json_decode($rawInput, true);

        if (!$input) {
            // Check if it's form-urlencoded (VB6 might send this way)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                // Parse form-urlencoded manually to handle + signs correctly
                $input = $this->parseFormUrlencoded($rawInput);
            } else {
                // Try parse_str as fallback
                parse_str($rawInput, $parsedData);
                $input = $parsedData ?: $_POST;
            }
        }

        $id = $input['id'] ?? $_GET['id'] ?? null;
        // Get kodebarang - prefer from input body, then from query string (handle plus sign correctly)
        $kodebarang = $input['kodebarang'] ?? null;
        if ($kodebarang === null) {
            $kodebarang = $this->getKodebarangFromQuery();
            if ($kodebarang === null) {
                $kodebarang = $_GET['kodebarang'] ?? null;
            }
        }

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
            'kondisi',
            'ed',
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
        // Use stored raw input if available (from Router), otherwise read from php://input
        $rawInput = $GLOBALS['_RAW_INPUT'] ?? file_get_contents('php://input');
        $input = json_decode($rawInput, true);

        if (!$input) {
            // Check if it's form-urlencoded (VB6 might send this way)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                // Parse form-urlencoded manually to handle + signs correctly
                $input = $this->parseFormUrlencoded($rawInput);
            } else {
                // Try parse_str as fallback
                parse_str($rawInput, $parsedData);
                $input = $parsedData ?: $_GET;
            }
        }

        $id = $input['id'] ?? $_GET['id'] ?? null;
        // Get kodebarang - prefer from input body, then from query string (handle plus sign correctly)
        $kodebarang = $input['kodebarang'] ?? null;
        if ($kodebarang === null) {
            $kodebarang = $this->getKodebarangFromQuery();
            if ($kodebarang === null) {
                $kodebarang = $_GET['kodebarang'] ?? null;
            }
        }

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


