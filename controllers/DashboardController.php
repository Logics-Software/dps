<?php
class DashboardController extends Controller {
    public function index() {
        Auth::requireAuth();
        
        $this->view('dashboard/index');
    }
}

