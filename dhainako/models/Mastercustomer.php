<?php
class Mastercustomer {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findById($id) {
        $sql = "SELECT * FROM mastercustomer WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function findByKodecustomer($kodecustomer) {
        $sql = "SELECT * FROM mastercustomer WHERE kodecustomer = ?";
        return $this->db->fetchOne($sql, [$kodecustomer]);
    }

    public function getAll($page = 1, $perPage = 100, $search = '', $sortBy = 'id', $sortOrder = 'ASC', $status = '') {
        $offset = ($page - 1) * $perPage;

        $where = "1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (namacustomer LIKE ? OR alamatcustomer LIKE ? OR kotacustomer LIKE ? OR namawp LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_fill(0, 4, $searchParam);
        }

        if (!empty($status)) {
            $normalizedStatus = $this->normalizeStatusValue($status, null);
            if ($normalizedStatus !== null) {
                $where .= " AND LOWER(status) = ?";
                $params[] = $normalizedStatus;
            }
        }

        $validSortColumns = [
            'id',
            'kodecustomer',
            'namacustomer',
            'namabadanusaha',
            'alamatcustomer',
            'kotacustomer',
            'notelepon',
            'statuspkp',
            'status',
            'created_at',
            'updated_at'
        ];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'id';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM mastercustomer WHERE {$where} ORDER BY {$sortBy} {$sortOrder} LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function count($search = '', $status = '') {
        $where = "1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (namacustomer LIKE ? OR alamatcustomer LIKE ? OR kotacustomer LIKE ? OR namawp LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_fill(0, 4, $searchParam);
        }

        if (!empty($status)) {
            $normalizedStatus = $this->normalizeStatusValue($status, null);
            if ($normalizedStatus !== null) {
                $where .= " AND LOWER(status) = ?";
                $params[] = $normalizedStatus;
            }
        }

        $sql = "SELECT COUNT(*) as total FROM mastercustomer WHERE {$where}";
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    public function findNearest($latitude, $longitude, $limit = 10, $search = '') {
        $limit = max(1, min((int)$limit, 100));
        $hasCoordinates = $latitude !== null && $longitude !== null;

        $searchClause = '';
        $searchParams = [];
        if (!empty($search)) {
            $searchClause = " AND (kodecustomer LIKE ? OR namacustomer LIKE ? OR notelepon LIKE ? OR alamatcustomer LIKE ? OR kotacustomer LIKE ?)";
            $searchParam = "%{$search}%";
            $searchParams = array_fill(0, 5, $searchParam);
        }

        $withCoordBase = 'latitude IS NOT NULL AND latitude <> 0 AND longitude IS NOT NULL AND longitude <> 0';
        $withoutCoordBase = '(latitude IS NULL OR latitude = 0 OR longitude IS NULL OR longitude = 0)';

        if ($hasCoordinates) {
            $paramsWithCoord = array_merge([$latitude, $longitude, $latitude], $searchParams);
            $paramsWithoutCoord = $searchParams;

            $sql = "(
                    SELECT *,
                        (6371 * ACOS(
                            COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) +
                            SIN(RADIANS(?)) * SIN(RADIANS(latitude))
                        )) AS distance_km,
                        0 AS sort_group
                    FROM mastercustomer
                    WHERE {$withCoordBase}
                    {$searchClause}
                )
                UNION ALL
                (
                    SELECT *,
                        NULL AS distance_km,
                        1 AS sort_group
                    FROM mastercustomer
                    WHERE {$withoutCoordBase}
                    {$searchClause}
                )
                ORDER BY sort_group ASC, distance_km ASC, namacustomer ASC
                LIMIT {$limit}";

            $params = array_merge($paramsWithCoord, $paramsWithoutCoord);
        } else {
            $paramsWithCoord = $searchParams;
            $paramsWithoutCoord = $searchParams;

            $sql = "(
                    SELECT *,
                        NULL AS distance_km,
                        0 AS sort_group
                    FROM mastercustomer
                    WHERE {$withCoordBase}
                    {$searchClause}
                )
                UNION ALL
                (
                    SELECT *,
                        NULL AS distance_km,
                        1 AS sort_group
                    FROM mastercustomer
                    WHERE {$withoutCoordBase}
                    {$searchClause}
                )
                ORDER BY sort_group ASC, namacustomer ASC
                LIMIT {$limit}";

            $params = array_merge($paramsWithCoord, $paramsWithoutCoord);
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function updateCoordinates($id, $latitude, $longitude) {
        $sql = "UPDATE mastercustomer SET latitude = ?, longitude = ?, updated_at = NOW() WHERE id = ?";
        $this->db->query($sql, [$latitude, $longitude, $id]);
    }

    public function create($data) {
        $sql = "INSERT INTO mastercustomer (
            kodecustomer,
            namacustomer,
            namabadanusaha,
            alamatcustomer,
            kotacustomer,
            notelepon,
            kontakperson,
            statuspkp,
            npwp,
            namawp,
            alamatwp,
            namaapoteker,
            nosipa,
            tanggaledsipa,
            noijinusaha,
            tanggaledijinusaha,
            nocdob,
            tanggaledcdob,
            latitude,
            longitude,
            userid,
            status
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $params = [
            $data['kodecustomer'],
            $data['namacustomer'],
            $data['namabadanusaha'] ?? null,
            $data['alamatcustomer'] ?? null,
            $data['kotacustomer'] ?? null,
            $data['notelepon'] ?? null,
            $data['kontakperson'] ?? null,
            $this->normalizeStatusPkp($data['statuspkp'] ?? 'nonpkp'),
            $data['npwp'] ?? null,
            $data['namawp'] ?? null,
            $data['alamatwp'] ?? null,
            $data['namaapoteker'] ?? null,
            $data['nosipa'] ?? null,
            $data['tanggaledsipa'] ?? null,
            $data['noijinusaha'] ?? null,
            $data['tanggaledijinusaha'] ?? null,
            $data['nocdob'] ?? null,
            $data['tanggaledcdob'] ?? null,
            isset($data['latitude']) ? $data['latitude'] : null,
            isset($data['longitude']) ? $data['longitude'] : null,
            $data['userid'] ?? null,
            $this->normalizeStatusValue($data['status'] ?? 'baru', 'baru')
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];

        $allowedFields = [
            'kodecustomer',
            'namacustomer',
            'namabadanusaha',
            'alamatcustomer',
            'kotacustomer',
            'notelepon',
            'kontakperson',
            'statuspkp',
            'npwp',
            'namawp',
            'alamatwp',
            'namaapoteker',
            'nosipa',
            'tanggaledsipa',
            'noijinusaha',
            'tanggaledijinusaha',
            'nocdob',
            'tanggaledcdob',
            'latitude',
            'longitude',
            'userid',
            'status'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                if ($field === 'status') {
                    $value = $this->normalizeStatusValue($data[$field], 'updated');
                } elseif ($field === 'statuspkp') {
                    $value = $this->normalizeStatusPkp($data[$field], null);
                } else {
                    $value = $data[$field];
                }
                $fields[] = "{$field} = ?";
                $params[] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE mastercustomer SET " . implode(', ', $fields) . " WHERE id = ?";

        return $this->db->query($sql, $params);
    }

    public function updateStatusByKodecustomer($kodecustomer, $status) {
        if (empty($kodecustomer)) {
            return false;
        }

        $normalizedStatus = $this->normalizeStatusValue($status, null);
        if ($normalizedStatus === null) {
            return false;
        }

        $sql = "UPDATE mastercustomer SET status = ?, updated_at = NOW() WHERE kodecustomer = ?";
        return $this->db->query($sql, [$normalizedStatus, $kodecustomer]);
    }

    public function delete($id) {
        $sql = "DELETE FROM mastercustomer WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function getAllForSelection() {
        $sql = "SELECT kodecustomer, namacustomer, alamatcustomer, statuspkp
                FROM mastercustomer
                ORDER BY namacustomer ASC";
        return $this->db->fetchAll($sql);
    }

    private function normalizeStatusValue($status, $default = 'baru') {
        if ($status === null || $status === '') {
            return $default;
        }

        if (!is_string($status)) {
            return $default;
        }

        $value = strtolower(trim($status));
        $allowed = ['baru', 'updated', 'aktif', 'nonaktif'];

        if (in_array($value, $allowed, true)) {
            return $value;
        }

        return $default;
    }

    private function normalizeStatusPkp($value, $default = 'nonpkp') {
        if ($value === null || $value === '') {
            return $default;
        }

        $normalized = strtolower(trim((string)$value));
        $allowed = ['pkp', 'nonpkp'];

        if (in_array($normalized, $allowed, true)) {
            return $normalized;
        }

        return $default;
    }
}

