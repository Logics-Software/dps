<?php
class UserController extends Controller {
    public function index() {
        Auth::requireRole(['admin', 'manajemen']);
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $search = $_GET['search'] ?? '';
        $sortBy = $_GET['sort_by'] ?? 'id';
        $sortOrder = $_GET['sort_order'] ?? 'ASC';
        
        $validPerPage = [10, 20, 40, 60, 100];
        if (!in_array($perPage, $validPerPage)) {
            $perPage = 10;
        }
        
        $userModel = new User();
        $users = $userModel->getAll($page, $perPage, $search, $sortBy, $sortOrder);
        $total = $userModel->count($search);
        $totalPages = ceil($total / $perPage);
        
        $data = [
            'users' => $users,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ];
        
        $this->view('users/index', $data);
    }
    
    public function create() {
        Auth::requireRole(['admin', 'manajemen']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'] ?? '',
                'namalengkap' => $_POST['namalengkap'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'sales',
                'kodesales' => $_POST['kodesales'] ?? null,
                'status' => $_POST['status'] ?? 'aktif'
            ];
            
            // Validate kodesales for sales role
            if ($data['role'] === 'sales') {
                if (empty($data['kodesales'])) {
                    Session::flash('error', 'Kode Sales wajib diisi untuk role Sales');
                    $this->redirect('/users/create');
                }
            } else {
                // Clear kodesales if role is not sales
                $data['kodesales'] = null;
            }
            
            // Handle picture upload
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $data['picture'] = $this->uploadPicture($_FILES['picture']);
            }
            
            $userModel = new User();
            
            // Check if username exists
            if ($userModel->findByUsername($data['username'])) {
                Session::flash('error', 'Username sudah digunakan');
                $this->redirect('/users/create');
            }
            
            // Check if email exists
            if ($userModel->findByEmail($data['email'])) {
                Session::flash('error', 'Email sudah digunakan');
                $this->redirect('/users/create');
            }
            
            $userModel->create($data);
            Session::flash('success', 'User berhasil ditambahkan');
            $this->redirect('/users');
        }
        
        // Get active mastersales for dropdown
        $mastersalesModel = new Mastersales();
        $mastersales = $mastersalesModel->getAllActive();
        
        $data = ['mastersales' => $mastersales];
        $this->view('users/create', $data);
    }
    
    public function edit($id) {
        Auth::requireRole(['admin', 'manajemen']);
        
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            Session::flash('error', 'User tidak ditemukan');
            $this->redirect('/users');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'] ?? '',
                'namalengkap' => $_POST['namalengkap'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? 'sales',
                'kodesales' => $_POST['kodesales'] ?? null,
                'status' => $_POST['status'] ?? 'aktif'
            ];
            
            // Validate kodesales for sales role
            if ($data['role'] === 'sales') {
                if (empty($data['kodesales'])) {
                    Session::flash('error', 'Kode Sales wajib diisi untuk role Sales');
                    $this->redirect("/users/edit/{$id}");
                }
            } else {
                // Clear kodesales if role is not sales
                $data['kodesales'] = null;
            }
            
            // Check username uniqueness (except current user)
            $existingUser = $userModel->findByUsername($data['username']);
            if ($existingUser && $existingUser['id'] != $id) {
                Session::flash('error', 'Username sudah digunakan');
                $this->redirect("/users/edit/{$id}");
            }
            
            // Check email uniqueness (except current user)
            $existingEmail = $userModel->findByEmail($data['email']);
            if ($existingEmail && $existingEmail['id'] != $id) {
                Session::flash('error', 'Email sudah digunakan');
                $this->redirect("/users/edit/{$id}");
            }
            
            // Handle picture upload
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                // Delete old picture
                if ($user['picture']) {
                    $oldPicturePath = __DIR__ . '/../uploads/' . $user['picture'];
                    if (file_exists($oldPicturePath)) {
                        unlink($oldPicturePath);
                    }
                }
                $data['picture'] = $this->uploadPicture($_FILES['picture']);
            }
            
            $userModel->update($id, $data);
            Session::flash('success', 'User berhasil diupdate');
            $this->redirect('/users');
        }
        
        // Get active mastersales for dropdown
        $mastersalesModel = new Mastersales();
        $mastersales = $mastersalesModel->getAllActive();
        
        $data = ['user' => $user, 'mastersales' => $mastersales];
        $this->view('users/edit', $data);
    }
    
    public function delete($id) {
        Auth::requireRole(['admin', 'manajemen']);
        
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            Session::flash('error', 'User tidak ditemukan');
            $this->redirect('/users');
        }
        
        // Don't allow deleting yourself
        if ($user['id'] == Auth::user()['id']) {
            Session::flash('error', 'Tidak dapat menghapus akun sendiri');
            $this->redirect('/users');
        }
        
        // Delete picture
        if ($user['picture']) {
            $picturePath = __DIR__ . '/../uploads/' . $user['picture'];
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }
        }
        
        $userModel->delete($id);
        Session::flash('success', 'User berhasil dihapus');
        $this->redirect('/users');
    }
    
    private function uploadPicture($file) {
        $config = require __DIR__ . '/../config/app.php';
        $uploadPath = $config['upload_path'];
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $config['allowed_image_types'])) {
            throw new Exception('Format file tidak diizinkan');
        }
        
        if ($file['size'] > $config['max_file_size']) {
            throw new Exception('Ukuran file terlalu besar');
        }
        
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadPath . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $filename;
        }
        
        throw new Exception('Gagal mengupload file');
    }
}

