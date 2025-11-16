<?php
class Headerpenerimaan {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($options = []) {
        $page = max((int)($options['page'] ?? 1), 1);
        $perPage = max((int)($options['per_page'] ?? 10), 1);
        $search = trim($options['search'] ?? '');
        $status = $options['status'] ?? null;
        $kodecustomer = $options['kodecustomer'] ?? null;
        $kodesales = $options['kodesales'] ?? null;
        $startDate = $options['start_date'] ?? null;
        $endDate = $options['end_date'] ?? null;

        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = ["1=1"];

        if ($startDate && $endDate) {
            $where[] = "hp.tanggalpenerimaan BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        if (!empty($search)) {
            $where[] = "(hp.nopenerimaan LIKE ? OR mc.namacustomer LIKE ? OR u.namasales LIKE ? OR hp.noinkaso LIKE ?)";
            $keyword = '%' . $search . '%';
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($status) && in_array($status, ['belumproses', 'proses'], true)) {
            $where[] = "hp.status = ?";
            $params[] = $status;
        }

        if (!empty($kodecustomer)) {
            $where[] = "hp.kodecustomer = ?";
            $params[] = $kodecustomer;
        }

        if (!empty($kodesales)) {
            $where[] = "hp.kodesales = ?";
            $params[] = $kodesales;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT hp.*, mc.namacustomer, u.namasales
                FROM headerpenerimaan hp
                LEFT JOIN mastercustomer mc ON hp.kodecustomer = mc.kodecustomer
                LEFT JOIN mastersales u ON hp.kodesales = u.kodesales
                WHERE {$whereClause}
                ORDER BY hp.tanggalpenerimaan DESC, hp.nopenerimaan DESC
                LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function count($options = []) {
        $search = trim($options['search'] ?? '');
        $status = $options['status'] ?? null;
        $kodecustomer = $options['kodecustomer'] ?? null;
        $kodesales = $options['kodesales'] ?? null;
        $startDate = $options['start_date'] ?? null;
        $endDate = $options['end_date'] ?? null;

        $params = [];
        $where = ["1=1"];

        if ($startDate && $endDate) {
            $where[] = "hp.tanggalpenerimaan BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        if (!empty($search)) {
            $where[] = "(hp.nopenerimaan LIKE ? OR mc.namacustomer LIKE ? OR u.namasales LIKE ? OR hp.noinkaso LIKE ?)";
            $keyword = '%' . $search . '%';
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($status) && in_array($status, ['belumproses', 'proses'], true)) {
            $where[] = "hp.status = ?";
            $params[] = $status;
        }

        if (!empty($kodecustomer)) {
            $where[] = "hp.kodecustomer = ?";
            $params[] = $kodecustomer;
        }

        if (!empty($kodesales)) {
            $where[] = "hp.kodesales = ?";
            $params[] = $kodesales;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) AS total
                FROM headerpenerimaan hp
                LEFT JOIN mastercustomer mc ON hp.kodecustomer = mc.kodecustomer
                LEFT JOIN mastersales u ON hp.kodesales = u.kodesales
                WHERE {$whereClause}";

        $result = $this->db->fetchOne($sql, $params);
        return (int)($result['total'] ?? 0);
    }

    public function findByNopenerimaan($nopenerimaan) {
        $sql = "SELECT hp.*, mc.namacustomer, mc.alamatcustomer, mc.kotacustomer, u.namasales
                FROM headerpenerimaan hp
                LEFT JOIN mastercustomer mc ON hp.kodecustomer = mc.kodecustomer
                LEFT JOIN mastersales u ON hp.kodesales = u.kodesales
                WHERE hp.nopenerimaan = ?";
        return $this->db->fetchOne($sql, [$nopenerimaan]);
    }

    public function create($headerData, $details) {
        $conn = $this->db->getConnection();

        $conn->beginTransaction();
        try {
            $headerSql = "INSERT INTO headerpenerimaan (nopenerimaan, tanggalpenerimaan, statuspkp, jenispenerimaan, kodesales, kodecustomer, totalpiutang, totalpotongan, totallainlain, totalnetto, status, noinkaso, userid)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($headerSql, [
                $headerData['nopenerimaan'],
                $headerData['tanggalpenerimaan'],
                $headerData['statuspkp'] ?? null,
                $headerData['jenispenerimaan'],
                $headerData['kodesales'] ?? null,
                $headerData['kodecustomer'] ?? null,
                $headerData['totalpiutang'] ?? 0,
                $headerData['totalpotongan'] ?? 0,
                $headerData['totallainlain'] ?? 0,
                $headerData['totalnetto'] ?? 0,
                $headerData['status'] ?? 'belumproses',
                $headerData['noinkaso'] ?? null,
                $headerData['userid'] ?? null
            ]);

            $detailSql = "INSERT INTO detailpenerimaan (nopenerimaan, nopenjualan, nogiro, tanggalcair, piutang, potongan, lainlain, netto, nourut)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $seq = 1;
            foreach ($details as $detail) {
                $this->db->query($detailSql, [
                    $headerData['nopenerimaan'],
                    $detail['nopenjualan'],
                    $detail['nogiro'] ?? null,
                    $detail['tanggalcair'] ?? null,
                    $detail['piutang'] ?? 0,
                    $detail['potongan'] ?? 0,
                    $detail['lainlain'] ?? 0,
                    $detail['netto'] ?? 0,
                    isset($detail['nourut']) ? (int)$detail['nourut'] : $seq++
                ]);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function update($nopenerimaan, $headerData, $details = null) {
        $conn = $this->db->getConnection();

        $conn->beginTransaction();
        try {
            if (!empty($headerData)) {
                $fields = [];
                $params = [];

                foreach ($headerData as $key => $value) {
                    $fields[] = "{$key} = ?";
                    $params[] = $value;
                }
                $params[] = $nopenerimaan;

                $sql = "UPDATE headerpenerimaan SET " . implode(', ', $fields) . " WHERE nopenerimaan = ?";
                $this->db->query($sql, $params);
            }

            if (is_array($details)) {
                $this->db->query("DELETE FROM detailpenerimaan WHERE nopenerimaan = ?", [$nopenerimaan]);

                $detailSql = "INSERT INTO detailpenerimaan (nopenerimaan, nopenjualan, nogiro, tanggalcair, piutang, potongan, lainlain, netto, nourut)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $seq = 1;
                foreach ($details as $detail) {
                    $this->db->query($detailSql, [
                        $nopenerimaan,
                        $detail['nopenjualan'],
                        $detail['nogiro'] ?? null,
                        $detail['tanggalcair'] ?? null,
                        $detail['piutang'] ?? 0,
                        $detail['potongan'] ?? 0,
                        $detail['lainlain'] ?? 0,
                        $detail['netto'] ?? 0,
                        isset($detail['nourut']) ? (int)$detail['nourut'] : $seq++
                    ]);
                }
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function patch($nopenerimaan, $data) {
        if (empty($data)) {
            return;
        }

        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $nopenerimaan;

        $sql = "UPDATE headerpenerimaan SET " . implode(', ', $fields) . " WHERE nopenerimaan = ?";
        $this->db->query($sql, $params);
    }

    public function delete($nopenerimaan) {
        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            $this->db->query("DELETE FROM detailpenerimaan WHERE nopenerimaan = ?", [$nopenerimaan]);
            $this->db->query("DELETE FROM headerpenerimaan WHERE nopenerimaan = ?", [$nopenerimaan]);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function updateStatusAndNoinkaso($nopenerimaan, $status, $noinkaso = null) {
        $fields = ["status = ?"];
        $params = [$status];
        
        if ($noinkaso !== null) {
            $fields[] = "noinkaso = ?";
            $params[] = $noinkaso;
        }
        
        $params[] = $nopenerimaan;
        $sql = "UPDATE headerpenerimaan SET " . implode(', ', $fields) . " WHERE nopenerimaan = ?";
        $this->db->query($sql, $params);
    }

    public function canEditOrDelete($nopenerimaan) {
        $sql = "SELECT status FROM headerpenerimaan WHERE nopenerimaan = ?";
        $result = $this->db->fetchOne($sql, [$nopenerimaan]);
        return $result && $result['status'] === 'belumproses';
    }

    public function getLastNopenerimaanWithPrefix($prefix) {
        $sql = "SELECT nopenerimaan FROM headerpenerimaan 
                WHERE nopenerimaan LIKE ? 
                ORDER BY nopenerimaan DESC 
                LIMIT 1";
        return $this->db->fetchOne($sql, [$prefix . '%']);
    }
}

