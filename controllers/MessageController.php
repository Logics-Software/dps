<?php
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/User.php';

class MessageController extends Controller {
	private $messageModel;
	private $userModel;

	public function __construct() {
		parent::__construct();
		$this->messageModel = new Message();
		$this->userModel = new User();
	}

	/**
	 * Display inbox messages
	 */
	public function index() {
		Auth::requireAuth();

		$userId = Auth::user()['id'];
		$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
		$search = trim($_GET['search'] ?? '');
		$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
		$perPage = in_array($perPage, [10, 20, 30, 50, 100]) ? $perPage : 20;

		// Get paginated messages
		$result = $this->messageModel->getPaginatedInboxMessages($userId, $page, $perPage, $search);
		$unreadCount = $this->messageModel->getUnreadCount($userId);

		$this->view('messages/index', [
			'title' => 'Pesan Masuk',
			'messages' => $result['data'],
			'unread_count' => $unreadCount,
			'search' => $search,
			'pagination' => [
				'current_page' => $result['page'],
				'total_pages' => $result['total_pages'],
				'total_items' => $result['total'],
				'per_page' => $result['per_page'],
				'has_next' => $result['has_next'],
				'has_prev' => $result['has_prev']
			]
		]);
	}

	/**
	 * Display sent messages
	 */
	public function sent() {
		Auth::requireAuth();

		$userId = Auth::user()['id'];
		$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
		$search = trim($_GET['search'] ?? '');
		$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
		$perPage = in_array($perPage, [10, 20, 30, 50, 100]) ? $perPage : 20;

		// Get paginated sent messages
		$result = $this->messageModel->getPaginatedSentMessages($userId, $page, $perPage, $search);

		$this->view('messages/sent', [
			'title' => 'Pesan Terkirim',
			'messages' => $result['data'],
			'search' => $search,
			'pagination' => [
				'current_page' => $result['page'],
				'total_pages' => $result['total_pages'],
				'total_items' => $result['total'],
				'per_page' => $result['per_page'],
				'has_next' => $result['has_next'],
				'has_prev' => $result['has_prev']
			]
		]);
	}

	/**
	 * Show compose message form
	 */
	public function create() {
		Auth::requireAuth();

		$userId = Auth::user()['id'];

		// Get all users for recipient selection
		$users = $this->messageModel->getAllUsers($userId);
		
		// Initialize reply and forward data
		$replyData = null;
		$forwardData = null;
		
		// Check if this is a reply to a message
		$replyId = $_GET['reply'] ?? null;
		if ($replyId) {
			$replyData = $this->messageModel->getMessageForReply($replyId, $userId);
		}
		
		// Check if this is a forward of a message
		$forwardId = $_GET['forward'] ?? null;
		if ($forwardId) {
			$forwardData = $this->messageModel->getMessageForForward($forwardId, $userId);
		}

		$this->view('messages/create', [
			'title' => 'Tulis Pesan',
			'users' => $users,
			'reply_data' => $replyData,
			'forward_data' => $forwardData
		]);
	}

	/**
	 * Store new message
	 */
	public function store() {
		Auth::requireAuth();

		$userId = Auth::user()['id'];

		// Validation
		if (empty($_POST['subject']) || empty($_POST['content']) || empty($_POST['recipients'])) {
			Message::error('Subjek, isi pesan, dan penerima wajib diisi');
			$this->redirect('/messages/create');
			return;
		}

		try {
			$this->db->getConnection()->beginTransaction();
			
			// Prepare message data
			$messageData = [
				'sender_id' => $userId,
				'subject' => trim($_POST['subject']),
				'content' => $_POST['content'],
				'message_type' => $_POST['message_type'] ?? 'direct',
				'status' => 'sent'
			];

			// Parse recipients (can be comma-separated or array)
			$recipientIds = [];
			if (is_string($_POST['recipients'])) {
				$recipientIds = array_filter(array_map('trim', explode(',', $_POST['recipients'])));
			} elseif (is_array($_POST['recipients'])) {
				$recipientIds = array_filter($_POST['recipients']);
			}

			if (empty($recipientIds)) {
				$this->db->getConnection()->rollBack();
				Message::error('Pilih minimal satu penerima');
				$this->redirect('/messages/create');
				return;
			}

			// Create message
			$messageId = $this->messageModel->createMessage($messageData, $recipientIds);

			if ($messageId) {
				// Handle attachments
				if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
					$this->handleAttachments($messageId, $_FILES['attachments']);
				}
				
				$this->db->getConnection()->commit();
				Message::success('Pesan berhasil dikirim');
				$this->redirect('/messages?sent=true');
			} else {
				$this->db->getConnection()->rollBack();
				Message::error('Gagal mengirim pesan');
				$this->redirect('/messages/create');
			}
		} catch (Exception $e) {
			$this->db->getConnection()->rollBack();
			Message::error('Terjadi kesalahan: ' . $e->getMessage());
			$this->redirect('/messages/create');
		}
	}

	/**
	 * Show specific message
	 */
	public function show($id = null) {
		Auth::requireAuth();

		$messageId = $id ?? ($_GET['id'] ?? null);
		if (!$messageId) {
			$this->redirect('/messages');
			return;
		}
		$userId = Auth::user()['id'];

		// Get message with recipients
		$message = $this->messageModel->getMessageWithRecipients($messageId, $userId);

		if (!$message) {
			$this->redirect('/messages');
			return;
		}

		// Get attachments
		$message['attachments'] = $this->messageModel->getAttachments($messageId);

		// Check if user is recipient and mark as read if needed
		$isRecipient = false;
		foreach ($message['recipients'] as $recipient) {
			if ($recipient['recipient_id'] == $userId) {
				$isRecipient = true;
				if (!$recipient['is_read']) {
					$this->messageModel->markAsRead($messageId, $userId);
				}
				break;
			}
		}

		$this->view('messages/show', [
			'title' => 'Detail Pesan',
			'message' => $message,
			'is_recipient' => $isRecipient
		]);
	}

	/**
	 * Delete message
	 */
	public function delete($id = null) {
		Auth::requireAuth();

		$messageId = $id ?? ($_GET['id'] ?? null);
		if (!$messageId) {
			$this->json(['success' => false, 'message' => 'ID pesan tidak valid']);
			return;
		}
		$userId = Auth::user()['id'];

		try {
			$result = $this->messageModel->deleteMessage($messageId, $userId);

			if ($result) {
				$this->json(['success' => true, 'message' => 'Pesan berhasil dihapus']);
			} else {
				$this->json(['success' => false, 'message' => 'Gagal menghapus pesan']);
			}
		} catch (Exception $e) {
			$this->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
		}
	}

	/**
	 * Search messages
	 */
	public function search() {
		Auth::requireAuth();

		$userId = Auth::user()['id'];
		$searchTerm = trim($_GET['q'] ?? '');
		$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
		$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
		$perPage = in_array($perPage, [10, 20, 30, 50, 100]) ? $perPage : 20;

		if (empty($searchTerm)) {
			$this->redirect('/messages');
			return;
		}

		// Search messages with pagination
		$result = $this->messageModel->getPaginatedInboxMessages($userId, $page, $perPage, $searchTerm);

		$this->view('messages/search', [
			'title' => 'Hasil Pencarian',
			'messages' => $result['data'],
			'search_term' => $searchTerm,
			'pagination' => [
				'current_page' => $result['page'],
				'total_pages' => $result['total_pages'],
				'total_items' => $result['total'],
				'per_page' => $result['per_page'],
				'has_next' => $result['has_next'],
				'has_prev' => $result['has_prev']
			]
		]);
	}

	/**
	 * Get unread count (AJAX)
	 */
	public function getUnreadCount() {
		Auth::requireAuth();

		$userId = Auth::user()['id'];
		$unreadCount = $this->messageModel->getUnreadCount($userId);

		$this->json(['success' => true, 'unread_count' => $unreadCount]);
	}

	/**
	 * Mark all messages as read (AJAX)
	 */
	public function markAllAsRead() {
		Auth::requireAuth();

		$userId = Auth::user()['id'];

		try {
			$result = $this->messageModel->markAllAsRead($userId);
			
			if ($result) {
				$this->json(['success' => true, 'message' => 'All messages marked as read']);
			} else {
				$this->json(['success' => false, 'message' => 'Failed to mark messages as read']);
			}
		} catch (Exception $e) {
			$this->json(['success' => false, 'message' => 'Failed to mark messages as read']);
		}
	}

	/**
	 * Mark message as read (AJAX)
	 */
	public function markAsRead() {
		Auth::requireAuth();

		$messageId = $_POST['message_id'] ?? $_GET['message_id'] ?? null;
		$userId = Auth::user()['id'];

		if (!$messageId) {
			$this->json(['success' => false, 'message' => 'Message ID required']);
			return;
		}

		try {
			$result = $this->messageModel->markAsRead($messageId, $userId);
			
			if ($result) {
				$this->json(['success' => true, 'message' => 'Message marked as read']);
			} else {
				$this->json(['success' => false, 'message' => 'Failed to mark as read']);
			}
		} catch (Exception $e) {
			$this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	/**
	 * Search users for recipient selection (AJAX)
	 */
	public function searchUsers() {
		Auth::requireAuth();

		try {
			$search = trim($_GET['search'] ?? '');
			$role = trim($_GET['role'] ?? '');
			$currentUserId = Auth::user()['id'];
			
			$users = $this->messageModel->searchUsers($search, $role, $currentUserId);
			
			$this->json(['success' => true, 'users' => $users]);
		} catch (Exception $e) {
			$this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	private function handleAttachments($messageId, $files) {
		$uploadDir = __DIR__ . '/../assets/uploads/attachments/';
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0755, true);
		}

		$allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
		$maxSize = 5 * 1024 * 1024; // 5MB

		for ($i = 0; $i < count($files['name']); $i++) {
			if ($files['error'][$i] === UPLOAD_ERR_OK) {
				$fileName = $files['name'][$i];
				$fileSize = $files['size'][$i];
				$fileTmp = $files['tmp_name'][$i];
				$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

				// Validate file type
				if (!in_array($fileExt, $allowedTypes)) {
					continue; // Skip invalid files
				}

				// Validate file size
				if ($fileSize > $maxSize) {
					continue; // Skip oversized files
				}

				// Generate unique filename
				$newFileName = 'msg_' . $messageId . '_' . time() . '_' . $i . '.' . $fileExt;
				$filePath = $uploadDir . $newFileName;

				if (move_uploaded_file($fileTmp, $filePath)) {
					// Save attachment info to database
					$relativePath = 'assets/uploads/attachments/' . $newFileName;
					$this->messageModel->saveAttachment($messageId, $fileName, $relativePath, $files['type'][$i], $fileSize);
				}
			}
		}
	}
}

