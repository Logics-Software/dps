<?php
class Headerorder {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByNoorder($noorder) {
        $sql = "SELECT ho.*, mc.namacustomer, mc.alamatcustomer, mc.kotacustomer, u.namalengkap AS namasales
                FROM headerorder ho
                LEFT JOIN mastercustomer mc ON ho.kodecustomer = mc.kodecustomer
                LEFT JOIN users u ON ho.kodesales = u.kodesales
                WHERE ho.noorder = ?";
        return $this->db->fetchOne($sql, [$noorder]);
    }

    public function getAll($options = []) {
        $page = $options['page'] ?? 1;
        $perPage = $options['per_page'] ?? 10;
        $search = $options['search'] ?? '';
        $status = $options['status'] ?? '';
        $kodesales = $options['kodesales'] ?? null;
        $startDate = $options['start_date'] ?? null;
        $endDate = $options['end_date'] ?? null;
        $sortBy = $options['sort_by'] ?? 'tanggalorder';
        $sortOrder = strtoupper($options['sort_order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $perPage;

        $where = ["1=1"];
        $params = [];

        if (!empty($kodesales)) {
            $where[] = "ho.kodesales = ?";
            $params[] = $kodesales;
        }

        if (!empty($status)) {
            $where[] = "ho.status = ?";
            $params[] = $status;
        }

        if (!empty($startDate) && !empty($endDate)) {
            $where[] = "ho.tanggalorder BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        if (!empty($search)) {
            $where[] = "mc.namacustomer LIKE ?";
            $params[] = "%{$search}%";
        }

        $validSortColumns = ['tanggalorder', 'noorder', 'nilaiorder', 'status'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'tanggalorder';

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT ho.*, mc.namacustomer, mc.alamatcustomer, mc.kotacustomer
                FROM headerorder ho
                LEFT JOIN mastercustomer mc ON ho.kodecustomer = mc.kodecustomer
                WHERE {$whereClause}
                ORDER BY ho.{$sortBy} {$sortOrder}
                LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function count($options = []) {
        $search = $options['search'] ?? '';
        $status = $options['status'] ?? '';
        $kodesales = $options['kodesales'] ?? null;
        $startDate = $options['start_date'] ?? null;
        $endDate = $options['end_date'] ?? null;

        $where = ["1=1"];
        $params = [];

        if (!empty($kodesales)) {
            $where[] = "ho.kodesales = ?";
            $params[] = $kodesales;
        }

        if (!empty($status)) {
            $where[] = "ho.status = ?";
            $params[] = $status;
        }

        if (!empty($startDate) && !empty($endDate)) {
            $where[] = "ho.tanggalorder BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        if (!empty($search)) {
            $where[] = "mc.namacustomer LIKE ?";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) AS total
                FROM headerorder ho
                LEFT JOIN mastercustomer mc ON ho.kodecustomer = mc.kodecustomer
                WHERE {$whereClause}";

        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }

    public function create($headerData, $details) {
        $conn = $this->db->getConnection();

        try {
            $conn->beginTransaction();

            $sqlHeader = "INSERT INTO headerorder (noorder, tanggalorder, kodesales, statuspkp, kodecustomer, keterangan, nilaiorder, nopenjualan, status)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($sqlHeader, [
                $headerData['noorder'],
                $headerData['tanggalorder'],
                $headerData['kodesales'],
                $headerData['statuspkp'],
                $headerData['kodecustomer'],
                $headerData['keterangan'],
                $headerData['nilaiorder'],
                $headerData['nopenjualan'],
                $headerData['status']
            ]);

            $sqlDetail = "INSERT INTO detailorder (noorder, kodebarang, jumlah, hargajual, discount, totalharga)
                          VALUES (?, ?, ?, ?, ?, ?)";

            foreach ($details as $detail) {
                $this->db->query($sqlDetail, [
                    $headerData['noorder'],
                    $detail['kodebarang'],
                    $detail['jumlah'],
                    $detail['hargajual'],
                    $detail['discount'],
                    $detail['totalharga']
                ]);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function update($noorder, $headerData, $details = null) {
        $conn = $this->db->getConnection();

        try {
            $conn->beginTransaction();

            $sqlHeader = "UPDATE headerorder
                          SET tanggalorder = ?, kodesales = ?, statuspkp = ?, kodecustomer = ?, keterangan = ?, nilaiorder = ?, nopenjualan = ?, status = ?
                          WHERE noorder = ?";
            $this->db->query($sqlHeader, [
                $headerData['tanggalorder'],
                $headerData['kodesales'],
                $headerData['statuspkp'],
                $headerData['kodecustomer'],
                $headerData['keterangan'],
                $headerData['nilaiorder'],
                $headerData['nopenjualan'],
                $headerData['status'],
                $noorder
            ]);

            if ($details !== null) {
                $this->db->query("DELETE FROM detailorder WHERE noorder = ?", [$noorder]);

                $sqlDetail = "INSERT INTO detailorder (noorder, kodebarang, jumlah, hargajual, discount, totalharga)
                              VALUES (?, ?, ?, ?, ?, ?)";

                foreach ($details as $detail) {
                    $this->db->query($sqlDetail, [
                        $noorder,
                        $detail['kodebarang'],
                        $detail['jumlah'],
                        $detail['hargajual'],
                        $detail['discount'],
                        $detail['totalharga']
                    ]);
                }
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function delete($noorder) {
        $conn = $this->db->getConnection();

        try {
            $conn->beginTransaction();

            $this->db->query("DELETE FROM detailorder WHERE noorder = ?", [$noorder]);
            $this->db->query("DELETE FROM headerorder WHERE noorder = ?", [$noorder]);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function getLastNoorderWithPrefix($prefix) {
        $sql = "SELECT noorder FROM headerorder WHERE noorder LIKE ? ORDER BY noorder DESC LIMIT 1";
        return $this->db->fetchOne($sql, [$prefix . '%']);
    }
}
