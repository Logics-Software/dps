<?php
class DashboardController extends Controller {
    public function index() {
        Auth::requireAuth();
        
        $user = Auth::user();
        $role = $user['role'] ?? '';
        
        $data = [
            'user' => $user,
            'role' => $role
        ];
        
        // Get statistics based on role
        if ($role === 'admin' || $role === 'manajemen') {
            // Admin & Manajemen: Full statistics
            $data['stats'] = $this->getAdminStats();
        } elseif ($role === 'operator') {
            // Operator: Operational statistics
            $data['stats'] = $this->getOperatorStats();
        } elseif ($role === 'sales') {
            // Sales: Personal sales statistics
            $data['stats'] = $this->getSalesStats($user['kodesales'] ?? null);
        } else {
            $data['stats'] = [];
        }
        
        $this->view('dashboard/index', $data);
    }
    
    private function getAdminStats() {
        $headerOrder = new Headerorder();
        $headerPenjualan = new Headerpenjualan();
        $headerPenerimaan = new Headerpenerimaan();
        $userModel = new User();
        $messageModel = new MessageModel();
        
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        
        return [
            'total_orders' => $headerOrder->count(['start_date' => $today, 'end_date' => $today]),
            'total_penjualan' => $headerPenjualan->count(['periode' => 'today', 'start_date' => $today, 'end_date' => $today]),
            'total_penerimaan' => $headerPenerimaan->count(['start_date' => $today, 'end_date' => $today]),
            'total_users' => $userModel->count(),
            'unread_messages' => $messageModel->getUnreadCount(Auth::user()['id'] ?? 0)
        ];
    }
    
    private function getOperatorStats() {
        $headerOrder = new Headerorder();
        $headerPenjualan = new Headerpenjualan();
        $headerPenerimaan = new Headerpenerimaan();
        $messageModel = new MessageModel();
        
        $today = date('Y-m-d');
        
        return [
            'total_orders' => $headerOrder->count(['start_date' => $today, 'end_date' => $today]),
            'total_penjualan' => $headerPenjualan->count(['periode' => 'today', 'start_date' => $today, 'end_date' => $today]),
            'total_penerimaan' => $headerPenerimaan->count(['start_date' => $today, 'end_date' => $today]),
            'unread_messages' => $messageModel->getUnreadCount(Auth::user()['id'] ?? 0)
        ];
    }
    
    private function getSalesStats($kodesales) {
        $headerOrder = new Headerorder();
        $headerPenjualan = new Headerpenjualan();
        $headerPenerimaan = new Headerpenerimaan();
        $messageModel = new MessageModel();
        
        $today = date('Y-m-d');
        $options = [
            'start_date' => $today,
            'end_date' => $today
        ];
        
        if ($kodesales) {
            $options['kodesales'] = $kodesales;
        }
        
        return [
            'my_orders' => $headerOrder->count($options),
            'my_penjualan' => $headerPenjualan->count(array_merge($options, ['periode' => 'today'])),
            'my_penerimaan' => $headerPenerimaan->count($options),
            'unread_messages' => $messageModel->getUnreadCount(Auth::user()['id'] ?? 0)
        ];
    }
}

