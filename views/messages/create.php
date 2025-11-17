<?php
$title = 'Tulis Pesan';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
	<div class="breadcrumb-item">
		<div class="col-12">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
					<li class="breadcrumb-item"><a href="/messages">Pesan Masuk</a></li>
					<li class="breadcrumb-item active">Tulis Pesan</li>
				</ol>
			</nav>
		</div>
	</div>

	<div class="card">
		<div class="card-header">
			<div class="d-flex align-items-center">
				<h4 class="mb-0 me-auto">Pesan Baru</h4>
			 </div>
		</div>

		<div class="card-body">
			<form id="messageForm" method="POST" action="/messages/store">
				<div class="row">
					<div class="col-12">
						<div class="mb-3">
							<label for="subject" class="form-label">Subjek <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="subject" name="subject" 
									placeholder="Subjek" 
									value="<?php 
										if (isset($reply_data) && $reply_data) {
											echo 'Reply: ' . htmlspecialchars($reply_data['subject']);
										} elseif (isset($forward_data) && $forward_data) {
											echo 'Forward: ' . htmlspecialchars($forward_data['subject']);
										}
									?>" 
									required>
						</div>
						
						<div class="mb-3">
							<label class="form-label">Penerima <span class="text-danger">*</span></label>
							
							<!-- Search and Filter Controls -->
							<div class="row g-2 mb-3">
								<div class="col-6 col-md-8">
									<div class="input-group">
										<input type="text" class="form-control" id="userSearch" placeholder="Cari berdasarkan nama, username, atau email...">
										<button type="button" class="btn btn-secondary" id="userSearchToggleBtn" title="Search">
											<span id="userSearchIcon"><?= icon('magnifying-glass', 'me-0 mb-1', 16) ?></span>
										</button>
									</div>
								</div>
								<div class="col-3 col-md-3">
									<select class="form-select" id="roleFilter">
										<option value="">Semua</option>
										<option value="admin">Admin</option>
										<option value="manajemen">Manajemen</option>
										<option value="operator">Operator</option>
										<option value="sales">Sales</option>
									</select>
								</div>
								<div class="col-2 col-md-1">
									<div class="btn-group w-100" role="group">
										<button type="button" class="btn btn-primary" id="selectAllBtn" title="Pilih Semua">
											<?= icon('list-check', 'mb-0', 16) ?>
										</button>
										<button type="button" class="btn btn-danger" id="clearAllBtn" title="Hapus Semua">
											<?= icon('cancel', 'mb-0', 16) ?>
										</button>
									</div>
								</div>
							</div>
							
							<!-- Users List -->
							<div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
								<div id="usersList">
									<div class="p-3 text-center">
										<div class="spinner-border spinner-border-sm" role="status">
											<span class="visually-hidden">Loading...</span>
										</div>
										<span class="ms-2">Memuat daftar pengguna...</span>
									</div>
								</div>
							</div>
							
							<!-- Selected Recipients -->
							<div class="mt-2">
								<small class="text-muted">Penerima terpilih:</small>
								<div id="selectedRecipientsList" class="mt-1">
									<span class="text-muted">Belum ada penerima yang dipilih</span>
								</div>
							</div>
							
							<!-- Hidden input for form submission -->
							<input type="hidden" id="selectedRecipients" name="recipients[]" value="">
						</div>
						
						<div class="mb-3">
							<label for="content" class="form-label">Isi Pesan <span class="text-danger">*</span></label>
							<div id="quill-editor" style="height: 300px;"></div>
							<textarea id="content" name="content" class="d-none" required></textarea>
						</div>
						
						<div class="mb-3">
							<label for="attachments" class="form-label">Lampiran</label>
							<input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
							<div class="form-text">Maksimal 5MB per file. Format yang didukung: PDF, DOC, DOCX, TXT, JPG, PNG, GIF</div>
						</div>
						
						<?php if (isset($forward_data) && $forward_data && !empty($forward_data['attachments'])): ?>
						<div class="mb-3">
							<label class="form-label">Lampiran dari Pesan Asli</label>
							<div class="card">
								<div class="card-body">
									<small class="text-muted mb-2 d-block">Lampiran berikut akan ikut diteruskan:</small>
									<?php foreach ($forward_data['attachments'] as $attachment): ?>
									<div class="d-flex align-items-center mb-2">
										<?= icon('file-pdf', 'me-2 text-primary', 20) ?>
										<div class="flex-grow-1">
											<div class="fw-bold"><?= htmlspecialchars($attachment['original_name']) ?></div>
											<small class="text-muted">
												<?= number_format($attachment['file_size'] / 1024, 1) ?> KB
											</small>
										</div>
									</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</form>
		</div>

		<div class="card-footer d-flex justify-content-between align-items-center">
			<a href="/messages" class="btn btn-secondary">
				<?= icon('cancel', 'me-1 mb-1', 18) ?> Batal
			</a>
			<button type="submit" form="messageForm" class="btn btn-primary">
				<?= icon('paper-plane', 'me-1 mb-1', 18) ?> Kirim Pesan
			</button>
		</div>
	</div>
</div>

<!-- Quill JS Editor -->
<link href="<?= BASE_URL ?>assets/css/quill.snow.css" rel="stylesheet">
<script src="<?= BASE_URL ?>assets/js/quill.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
	// Initialize Quill Editor
	const quill = new Quill('#quill-editor', {
		theme: 'snow',
		modules: {
			toolbar: [
				[{ 'header': [1, 2, 3, false] }],
				['bold', 'italic', 'underline', 'strike'],
				[{ 'color': [] }, { 'background': [] }],
				[{ 'list': 'ordered'}, { 'list': 'bullet' }],
				[{ 'indent': '-1'}, { 'indent': '+1' }],
				[{ 'align': [] }],
				['link', 'image'],
				['clean']
			]
		},
		placeholder: 'Tulis pesan Anda di sini...'
	});
	
	// Make quill globally available
	window.quill = quill;
	
	// Update hidden textarea when content changes
	quill.on('text-change', function() {
		document.getElementById('content').value = quill.root.innerHTML;
	});
	
	// Auto-fill content for forward
	<?php if (isset($forward_data) && $forward_data): ?>
	const forwardSenderName = <?= json_encode($forward_data['forward_sender']['name'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
	const forwardSenderEmail = <?= json_encode($forward_data['forward_sender']['email'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
	const forwardContent = <?= json_encode($forward_data['content'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
	const forwardSubject = <?= json_encode($forward_data['subject'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
	const forwardDate = <?= json_encode(date('d F Y, H:i', strtotime($forward_data['created_at'] ?? 'now')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
	
	setTimeout(() => {
		const forwardMessage = `
			<div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background-color: #f9f9f9;">
				<div style="margin-bottom: 10px;">
					<strong>Diteruskan dari:</strong> ${forwardSenderName} (${forwardSenderEmail})<br>
					<strong>Tanggal:</strong> ${forwardDate}<br>
					<strong>Subjek:</strong> ${forwardSubject}
				</div>
				<div style="border-top: 1px solid #ddd; padding-top: 10px;">
					${forwardContent}
				</div>
			</div>
		`;
		
		if (window.quill) {
			try {
				const delta = window.quill.clipboard.convert(forwardMessage);
				window.quill.setContents(delta);
				document.getElementById('content').value = forwardMessage;
			} catch (error) {
				try {
					window.quill.clipboard.dangerouslyPasteHTML(forwardMessage);
					document.getElementById('content').value = forwardMessage;
				} catch (error2) {
					window.quill.root.innerHTML = forwardMessage;
					document.getElementById('content').value = forwardMessage;
				}
			}
		}
	}, 500);
	<?php endif; ?>
	
	const userSearch = document.getElementById('userSearch');
	const roleFilter = document.getElementById('roleFilter');
	const usersList = document.getElementById('usersList');
	const selectedRecipientsList = document.getElementById('selectedRecipientsList');
	const selectedRecipientsInput = document.getElementById('selectedRecipients');
	
	let selectedUsers = [];
	let allUsers = [];
	
	// Load users on page load
	loadUsers();
	
	// Search functionality
	userSearch.addEventListener('input', function() {
		debounceSearch();
		setTimeout(() => {
			updateBulkSelectButtons();
		}, 400);
	});
	
	// Filter functionality
	roleFilter.addEventListener('change', function() {
		debounceSearch();
		setTimeout(() => {
			updateBulkSelectButtons();
		}, 400);
	});
	
	// Bulk select functionality
	const selectAllBtn = document.getElementById('selectAllBtn');
	const clearAllBtn = document.getElementById('clearAllBtn');
	
	selectAllBtn.addEventListener('click', function() {
		const displayedUsers = getCurrentDisplayedUsers();
		displayedUsers.forEach(user => {
			if (!selectedUsers.some(selected => selected.id == user.id)) {
				selectedUsers.push(user);
			}
		});
		updateSelectedRecipients();
		displayUsers(allUsers);
	});
	
	clearAllBtn.addEventListener('click', function() {
		selectedUsers = [];
		updateSelectedRecipients();
		displayUsers(allUsers);
	});
	
	// Debounce search
	let searchTimeout;
	function debounceSearch() {
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(() => {
			loadUsers();
		}, 300);
	}
	
	// Load users from API
	function loadUsers() {
		const search = userSearch.value;
		const role = roleFilter.value;
		
		const params = new URLSearchParams();
		if (search) params.append('search', search);
		if (role) params.append('role', role);
		
		const url = `/messages/searchUsers?${params.toString()}`;
		
		fetch(url)
			.then(response => {
				if (!response.ok) {
					throw new Error('HTTP error! status: ' + response.status);
				}
				return response.json();
			})
			.then(data => {
				if (data.success) {
					allUsers = data.users;
					displayUsers(data.users);
				} else {
					usersList.innerHTML = '<div class="p-3 text-center text-danger">Error: ' + data.message + '</div>';
				}
			})
			.catch(error => {
				usersList.innerHTML = '<div class="p-3 text-center text-danger">Error loading users: ' + error.message + '</div>';
			});
	}
	
	// Get currently displayed users
	function getCurrentDisplayedUsers() {
		let filteredUsers = allUsers;
		const search = userSearch.value.toLowerCase();
		const role = roleFilter.value;
		
		if (search) {
			filteredUsers = filteredUsers.filter(user => 
				user.namalengkap.toLowerCase().includes(search) ||
				user.username.toLowerCase().includes(search) ||
				user.email.toLowerCase().includes(search)
			);
		}
		
		if (role) {
			filteredUsers = filteredUsers.filter(user => user.role === role);
		}
		
		return filteredUsers;
	}
	
	// Update bulk select button states
	function updateBulkSelectButtons() {
		const displayedUsers = getCurrentDisplayedUsers();
		const selectedCount = displayedUsers.filter(user => 
			selectedUsers.some(selected => selected.id == user.id)
		).length;
		
		if (selectedCount === displayedUsers.length && displayedUsers.length > 0) {
			selectAllBtn.disabled = true;
		} else {
			selectAllBtn.disabled = false;
		}
		
		if (selectedUsers.length === 0) {
			clearAllBtn.disabled = true;
		} else {
			clearAllBtn.disabled = false;
		}
	}
	
	// Display users in the list
	function displayUsers(users) {
		if (users.length === 0) {
			usersList.innerHTML = '<div class="p-3 text-center text-muted">Tidak ada pengguna yang ditemukan</div>';
			return;
		}
		
		const usersHtml = users.map(user => {
			const isSelected = selectedUsers.some(selected => selected.id == user.id);
			const config = <?= json_encode(require __DIR__ . '/../../config/app.php', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
			const uploadUrl = config.upload_url || '/uploads/';
			const baseUrl = <?= json_encode(BASE_URL, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
			const avatarInitial = user.namalengkap.charAt(0).toUpperCase();
			let userPicture = '';
			if (user.picture) {
				userPicture = `<img src="${baseUrl}${uploadUrl}${user.picture}" alt="${user.namalengkap}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">`;
			} else {
				userPicture = `<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">${avatarInitial}</div>`;
			}
			
			return `
				<div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
					<div class="card user-selection-item position-relative ${isSelected ? 'border-primary' : ''}" data-user-id="${user.id}" style="cursor: pointer;">
						<div class="position-absolute" style="top: 0; left: 0.25rem; z-index: 10;">
							<div class="form-check">
								<input type="checkbox" class="form-check-input" style="border-radius: 0;" ${isSelected ? 'checked' : ''} onchange="toggleUser(${user.id})">
							</div>
						</div>
						<div class="card-body d-flex align-items-center" style="padding: 0.75rem; min-height: 60px;">
							${userPicture}
							<div class="flex-grow-1 ms-2" style="min-width: 0; overflow: hidden;">
								<div class="fw-bold text-truncate" style="font-size: 0.875rem;" title="${user.namalengkap}">${user.namalengkap}</div>
								<div class="text-muted text-truncate" style="font-size: 0.75rem;" title="${user.username}">${user.username}</div>
								<div class="text-muted text-truncate" style="font-size: 0.7rem;" title="${user.email}">${user.email}</div>
								<span class="badge bg-secondary" style="font-size: 0.65rem;">${user.role}</span>
							</div>
						</div>
					</div>
				</div>
			`;
		}).join('');
		
		usersList.innerHTML = `<div class="row g-2">${usersHtml}</div>`;
		
		setTimeout(() => {
			addCardClickHandlers();
		}, 100);
		
		updateBulkSelectButtons();
	}
	
	// Toggle user selection
	window.toggleUser = function(userId) {
		const user = allUsers.find(u => u.id == userId);
		if (!user) return;
		
		const existingIndex = selectedUsers.findIndex(u => u.id == user.id);
		if (existingIndex >= 0) {
			selectedUsers.splice(existingIndex, 1);
		} else {
			selectedUsers.push(user);
		}
		
		updateSelectedRecipients();
		displayUsers(allUsers);
		updateBulkSelectButtons();
	};
	
	// Add click functionality to user cards
	function addCardClickHandlers() {
		const userCards = document.querySelectorAll('.user-selection-item');
		userCards.forEach(card => {
			card.removeEventListener('click', handleCardClick);
			card.addEventListener('click', handleCardClick);
		});
	}
	
	function handleCardClick(e) {
		if (e.target.type === 'checkbox') {
			return;
		}
		const userId = parseInt(this.dataset.userId);
		const checkbox = this.querySelector('input[type="checkbox"]');
		if (checkbox) {
			checkbox.checked = !checkbox.checked;
			toggleUser(userId);
		}
	}
	
	// Update selected recipients display
	function updateSelectedRecipients() {
		if (selectedUsers.length === 0) {
			selectedRecipientsList.innerHTML = '<span class="text-muted">Belum ada penerima yang dipilih</span>';
			selectedRecipientsInput.value = '';
		} else {
			const recipientsHtml = selectedUsers.map(user => 
				`<span class="badge bg-primary me-1 mb-1">${user.namalengkap} <span onclick="removeUser(${user.id})" style="cursor: pointer;">×</span></span>`
			).join('');
			selectedRecipientsList.innerHTML = recipientsHtml;
			selectedRecipientsInput.value = selectedUsers.map(u => u.id).join(',');
		}
	}
	
	// Remove user from selection
	window.removeUser = function(userId) {
		selectedUsers = selectedUsers.filter(u => u.id != userId);
		updateSelectedRecipients();
		displayUsers(allUsers);
		updateBulkSelectButtons();
	};
	
	// Auto-select recipient for reply
	<?php if (isset($reply_data) && $reply_data): ?>
	const replySenderId = <?= $reply_data['reply_sender']['id'] ?>;
	setTimeout(() => {
		if (replySenderId) {
			const userCard = document.querySelector(`[data-user-id="${replySenderId}"]`);
			if (userCard) {
				const checkbox = userCard.querySelector('input[type="checkbox"]');
				if (checkbox && !checkbox.checked) {
					checkbox.checked = true;
					toggleUser(replySenderId);
				}
			}
		}
	}, 1000);
	<?php endif; ?>
	
	// Form submission
	document.getElementById('messageForm').addEventListener('submit', function(e) {
		e.preventDefault();
		
		if (selectedUsers.length === 0) {
			alert('Pilih minimal satu penerima');
			return;
		}
		
		const formData = new FormData(this);
		
		// Update content with Quill HTML
		let quillContent = '';
		try {
			if (window.quill && window.quill.root) {
				quillContent = window.quill.root.innerHTML;
			} else {
				quillContent = document.getElementById('content').value;
			}
		} catch (error) {
			quillContent = document.getElementById('content').value;
		}
		formData.set('content', quillContent);
		
		// Add recipients to form data
		formData.delete('recipients[]');
		selectedUsers.forEach(user => {
			formData.append('recipients[]', user.id);
		});
		
		const submitBtn = this.querySelector('button[type="submit"]');
		if (submitBtn) {
			const originalText = submitBtn.innerHTML;
			submitBtn.innerHTML = 'Mengirim...';
			submitBtn.disabled = true;
		}
		
		fetch(this.action, {
			method: 'POST',
			body: formData
		})
		.then(response => {
			if (response.redirected) {
				window.location.href = response.url;
			} else {
				return response.text();
			}
		})
		.catch(error => {
			alert('Terjadi kesalahan saat mengirim pesan');
		})
		.finally(() => {
			if (submitBtn) {
				submitBtn.innerHTML = originalText;
				submitBtn.disabled = false;
			}
		});
	});
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

