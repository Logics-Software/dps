<?php
$title = 'Tulis Pesan';
require __DIR__ . '/../layouts/header.php';

// Prepare simple config data
$config = require __DIR__ . '/../../config/app.php';
$baseUrl = defined('BASE_URL') ? BASE_URL : '/';
$baseUrl = str_replace(["\r", "\n", "\t"], '', trim($baseUrl));
if (empty($baseUrl)) {
	$baseUrl = '/';
}

// Get IDs from URL - no complex data needed
$replyId = isset($_GET['reply']) ? (int)$_GET['reply'] : 0;
$forwardId = isset($_GET['forward']) ? (int)$_GET['forward'] : 0;

// Get subject for input field (simple string, no complex data)
$subjectValue = '';
if ($replyId && isset($reply_data) && $reply_data && isset($reply_data['subject'])) {
	$subjectValue = 'Reply: ' . htmlspecialchars($reply_data['subject'], ENT_QUOTES, 'UTF-8');
} elseif ($forwardId && isset($forward_data) && $forward_data && isset($forward_data['subject'])) {
	$subjectValue = 'Forward: ' . htmlspecialchars($forward_data['subject'], ENT_QUOTES, 'UTF-8');
}
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
									value="<?= $subjectValue ?>" 
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

<!-- Quill JS Editor - Using CDN -->
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" crossorigin="anonymous">

<!-- Store only IDs and simple config in data attributes -->
<?php
$replyId = isset($_GET['reply']) ? (int)$_GET['reply'] : 0;
$forwardId = isset($_GET['forward']) ? (int)$_GET['forward'] : 0;

// Ensure baseUrl and uploadUrl are safe
$safeBaseUrl = htmlspecialchars(str_replace(["\r", "\n", "\t", "\"", "'"], '', $baseUrl), ENT_QUOTES, 'UTF-8');
$uploadUrlValue = str_replace(["\r", "\n", "\t", "\"", "'"], '', $config['upload_url'] ?? '/uploads/');
$safeUploadUrl = htmlspecialchars($uploadUrlValue, ENT_QUOTES, 'UTF-8');
?>
<div id="app-config" 
	data-base-url="<?= $safeBaseUrl ?>"
	data-upload-url="<?= $safeUploadUrl ?>"
	data-reply-id="<?= $replyId ?>"
	data-forward-id="<?= $forwardId ?>"
	style="display:none;"></div>

<script>
// Load simple configuration from data attributes
(function() {
	var configEl = document.getElementById('app-config');
	if (!configEl) {
		console.error('Config element not found');
		return;
	}
	
	window.CONFIG = {
		baseUrl: configEl.getAttribute('data-base-url') || '/',
		uploadUrl: configEl.getAttribute('data-upload-url') || '/uploads/',
		replyId: parseInt(configEl.getAttribute('data-reply-id') || '0') || 0,
		forwardId: parseInt(configEl.getAttribute('data-forward-id') || '0') || 0,
		forwardData: null,
		replyData: null
	};
})();
</script>
<script>

// Load Quill from CDN with fallback
(function() {
	var quillScript = document.createElement('script');
	quillScript.src = 'https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js';
	quillScript.crossOrigin = 'anonymous';
	quillScript.async = false;
	
	quillScript.onerror = function() {
		var baseUrl = (window.CONFIG && window.CONFIG.baseUrl) ? window.CONFIG.baseUrl : '/';
		var localScript = document.createElement('script');
		localScript.src = baseUrl + 'assets/js/quill.js';
		localScript.async = false;
		localScript.onerror = function() {
			var errorDiv = document.createElement('div');
			errorDiv.className = 'alert alert-danger';
			var strong = document.createElement('strong');
			strong.textContent = 'Error:';
			errorDiv.appendChild(strong);
			errorDiv.appendChild(document.createTextNode(' Editor tidak dapat dimuat. Silakan refresh halaman.'));
			var editorContainer = document.getElementById('quill-editor');
			if (editorContainer && editorContainer.parentElement) {
				editorContainer.parentElement.insertBefore(errorDiv, editorContainer);
			}
		};
		document.head.appendChild(localScript);
	};
	
	document.head.appendChild(quillScript);
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var quill = null;
	var selectedUsers = [];
	var allUsers = [];
	
	// Initialize Quill
	function initQuill() {
		if (typeof Quill === 'undefined') {
			setTimeout(initQuill, 100);
			return;
		}
		
		quill = new Quill('#quill-editor', {
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
		
		window.quill = quill;
		
		quill.on('text-change', function() {
			document.getElementById('content').value = quill.root.innerHTML;
		});
		
		// Load forward data via AJAX if needed
		if (window.CONFIG && window.CONFIG.forwardId > 0) {
			fetch('/messages/getForwardData?id=' + window.CONFIG.forwardId)
				.then(function(response) { return response.json(); })
				.then(function(result) {
					if (result.success && result.data) {
						window.CONFIG.forwardData = result.data;
						loadForwardData(result.data);
					}
				})
				.catch(function(error) {
					console.error('Error loading forward data:', error);
				});
		}
		
		// Load reply data via AJAX if needed
		if (window.CONFIG && window.CONFIG.replyId > 0) {
			fetch('/messages/getReplyData?id=' + window.CONFIG.replyId)
				.then(function(response) { return response.json(); })
				.then(function(result) {
					if (result.success && result.data) {
						window.CONFIG.replyData = result.data;
						// Auto-select sender for reply
						setTimeout(function() {
							var senderId = parseInt(result.data.sender_id || 0) || 0;
							if (senderId > 0) {
								var card = document.querySelector('[data-user-id="' + senderId + '"]');
								if (card) {
									var cb = card.querySelector('input[type="checkbox"]');
									if (cb && !cb.checked) {
										cb.checked = true;
										toggleUser(senderId);
									}
								}
							}
						}, 1000);
					}
				})
				.catch(function(error) {
					console.error('Error loading reply data:', error);
				});
		}
		
		// Function to load forward data into editor
		function loadForwardData(fd) {
			if (!fd || !quill) return;
			setTimeout(function() {
			var headerDiv = document.createElement('div');
			headerDiv.style.marginBottom = '10px';
			
			var strong1 = document.createElement('strong');
			strong1.textContent = 'Diteruskan dari:';
			headerDiv.appendChild(strong1);
			headerDiv.appendChild(document.createTextNode(' ' + escapeHtml(fd.sender_name || '') + ' (' + escapeHtml(fd.sender_email || '') + ')'));
			headerDiv.appendChild(document.createElement('br'));
			
			var strong2 = document.createElement('strong');
			strong2.textContent = 'Tanggal:';
			headerDiv.appendChild(strong2);
			headerDiv.appendChild(document.createTextNode(' ' + escapeHtml(fd.date || '')));
			headerDiv.appendChild(document.createElement('br'));
			
			var strong3 = document.createElement('strong');
			strong3.textContent = 'Subjek:';
			headerDiv.appendChild(strong3);
			headerDiv.appendChild(document.createTextNode(' ' + escapeHtml(fd.subject || '')));
				
				var contentDiv = document.createElement('div');
				contentDiv.style.cssText = 'border-top: 1px solid #ddd; padding-top: 10px;';
				// Use textContent for safety, but if content is HTML, we need to parse it
				if (fd.content) {
					var tempDiv = document.createElement('div');
					tempDiv.innerHTML = fd.content;
					while (tempDiv.firstChild) {
						contentDiv.appendChild(tempDiv.firstChild);
					}
				}
				
				var wrapperDiv = document.createElement('div');
				wrapperDiv.style.cssText = 'border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background-color: #f9f9f9;';
				wrapperDiv.appendChild(headerDiv);
				wrapperDiv.appendChild(contentDiv);
				
				try {
					var delta = quill.clipboard.convert(wrapperDiv.outerHTML);
					quill.setContents(delta);
					document.getElementById('content').value = wrapperDiv.outerHTML;
				} catch (e) {
					quill.root.innerHTML = wrapperDiv.outerHTML;
					document.getElementById('content').value = wrapperDiv.outerHTML;
				}
			}, 500);
		}
	}
	
	function escapeHtml(text) {
		if (!text) return '';
		var div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
	
	initQuill();
	
	// User selection elements
	var userSearch = document.getElementById('userSearch');
	var roleFilter = document.getElementById('roleFilter');
	var usersList = document.getElementById('usersList');
	var selectedRecipientsList = document.getElementById('selectedRecipientsList');
	var selectedRecipientsInput = document.getElementById('selectedRecipients');
	var selectAllBtn = document.getElementById('selectAllBtn');
	var clearAllBtn = document.getElementById('clearAllBtn');
	
	// Load users
	function loadUsers() {
		var search = userSearch.value;
		var role = roleFilter.value;
		var params = new URLSearchParams();
		if (search) params.append('search', search);
		if (role) params.append('role', role);
		
		usersList.innerHTML = '';
		var loadingDiv = document.createElement('div');
		loadingDiv.className = 'p-3 text-center';
		var spinner = document.createElement('div');
		spinner.className = 'spinner-border spinner-border-sm';
		loadingDiv.appendChild(spinner);
		var loadingText = document.createElement('span');
		loadingText.className = 'ms-2';
		loadingText.textContent = 'Memuat...';
		loadingDiv.appendChild(loadingText);
		usersList.appendChild(loadingDiv);
		
		fetch('/messages/searchUsers?' + params.toString())
			.then(function(response) {
				return response.json();
			})
		.then(function(data) {
			if (data.success) {
				// Sanitize all user data to remove any line breaks
				var sanitizedUsers = [];
				if (Array.isArray(data.users)) {
					data.users.forEach(function(user) {
						var cleanUser = {};
						for (var key in user) {
							if (user.hasOwnProperty(key)) {
								var val = user[key];
								if (typeof val === 'string') {
									val = val.replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ');
								}
								cleanUser[key] = val;
							}
						}
						sanitizedUsers.push(cleanUser);
					});
				}
				allUsers = sanitizedUsers;
				displayUsers(allUsers);
			} else {
				var errorMsg = (data.message || 'Gagal memuat').replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ');
				usersList.innerHTML = '';
				var errorDiv = document.createElement('div');
				errorDiv.className = 'p-3 text-center text-danger';
				errorDiv.textContent = 'Error: ' + errorMsg;
				usersList.appendChild(errorDiv);
			}
		})
		.catch(function(error) {
			usersList.innerHTML = '';
			var errorDiv = document.createElement('div');
			errorDiv.className = 'p-3 text-center text-danger';
			errorDiv.textContent = 'Error memuat daftar pengguna';
			usersList.appendChild(errorDiv);
		});
	}
	
	// Display users
	function displayUsers(users) {
		usersList.innerHTML = '';
		if (users.length === 0) {
			var emptyDiv = document.createElement('div');
			emptyDiv.className = 'p-3 text-center text-muted';
			emptyDiv.textContent = 'Tidak ada pengguna';
			usersList.appendChild(emptyDiv);
			return;
		}
		
		var row = document.createElement('div');
		row.className = 'row g-2';
		
		users.forEach(function(user) {
			var userId = parseInt(user.id || 0) || 0;
			var isSelected = selectedUsers.some(function(su) { return parseInt(su.id || 0) === userId; });
			var col = document.createElement('div');
			col.className = 'col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2';
			
			var card = document.createElement('div');
			card.className = 'card user-selection-item position-relative' + (isSelected ? ' border-primary' : '');
			card.setAttribute('data-user-id', userId);
			card.style.cursor = 'pointer';
			
			var checkboxDiv = document.createElement('div');
			checkboxDiv.className = 'position-absolute';
			checkboxDiv.style.cssText = 'top: 0; left: 0.25rem; z-index: 10;';
			var formCheckDiv = document.createElement('div');
			formCheckDiv.className = 'form-check';
			var checkbox = document.createElement('input');
			checkbox.type = 'checkbox';
			checkbox.className = 'form-check-input';
			if (isSelected) {
				checkbox.checked = true;
			}
			checkbox.setAttribute('onchange', 'toggleUser(' + userId + ')');
			formCheckDiv.appendChild(checkbox);
			checkboxDiv.appendChild(formCheckDiv);
			
			var cardBody = document.createElement('div');
			cardBody.className = 'card-body d-flex align-items-center';
			cardBody.style.cssText = 'padding: 0.75rem; min-height: 60px;';
			
			// Sanitize user data to ensure no line breaks
			var safePicture = (user.picture || '').replace(/\r\n/g, '').replace(/\n/g, '').replace(/\r/g, '').replace(/\t/g, '');
			var safeNamalengkap = (user.namalengkap || '').replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ').replace(/\t/g, ' ');
			var safeUsername = (user.username || '').replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ').replace(/\t/g, ' ');
			var safeEmail = (user.email || '').replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ').replace(/\t/g, ' ');
			var safeRole = (user.role || '').replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ').replace(/\t/g, ' ');
			
			var avatarEl = null;
			if (safePicture) {
				var baseUrl = (window.CONFIG && window.CONFIG.baseUrl) ? window.CONFIG.baseUrl : '/';
				var uploadUrl = (window.CONFIG && window.CONFIG.uploadUrl) ? window.CONFIG.uploadUrl : '/uploads/';
				var picUrl = baseUrl + uploadUrl + safePicture;
				avatarEl = document.createElement('img');
				avatarEl.src = picUrl;
				avatarEl.alt = safeNamalengkap || '';
				avatarEl.className = 'rounded-circle me-2';
				avatarEl.style.cssText = 'width: 32px; height: 32px; object-fit: cover;';
			} else {
				var initial = (safeNamalengkap && safeNamalengkap.length > 0) ? safeNamalengkap.charAt(0).toUpperCase() : 'U';
				avatarEl = document.createElement('div');
				avatarEl.className = 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2';
				avatarEl.style.cssText = 'width: 32px; height: 32px;';
				avatarEl.textContent = initial;
			}
			
			var infoDiv = document.createElement('div');
			infoDiv.className = 'flex-grow-1 ms-2';
			infoDiv.style.cssText = 'min-width: 0; overflow: hidden;';
			
			var nameDiv = document.createElement('div');
			nameDiv.className = 'fw-bold text-truncate';
			nameDiv.style.fontSize = '0.875rem';
			nameDiv.textContent = safeNamalengkap || 'N/A';
			infoDiv.appendChild(nameDiv);
			
			var usernameDiv = document.createElement('div');
			usernameDiv.className = 'text-muted text-truncate';
			usernameDiv.style.fontSize = '0.75rem';
			usernameDiv.textContent = safeUsername || 'N/A';
			infoDiv.appendChild(usernameDiv);
			
			var emailDiv = document.createElement('div');
			emailDiv.className = 'text-muted text-truncate';
			emailDiv.style.fontSize = '0.7rem';
			emailDiv.textContent = safeEmail || 'N/A';
			infoDiv.appendChild(emailDiv);
			
			var roleBadge = document.createElement('span');
			roleBadge.className = 'badge bg-secondary';
			roleBadge.style.fontSize = '0.65rem';
			roleBadge.textContent = safeRole || 'N/A';
			infoDiv.appendChild(roleBadge);
			
			if (avatarEl) {
				cardBody.appendChild(avatarEl);
			}
			cardBody.appendChild(infoDiv);
			card.appendChild(checkboxDiv);
			card.appendChild(cardBody);
			col.appendChild(card);
			row.appendChild(col);
			
			card.addEventListener('click', function(e) {
				if (e.target.type !== 'checkbox') {
					var cb = card.querySelector('input[type="checkbox"]');
					if (cb) {
						cb.checked = !cb.checked;
						toggleUser(userId);
					}
				}
			});
		});
		
		usersList.appendChild(row);
		updateBulkSelectButtons();
	}
	
	// Toggle user
	window.toggleUser = function(userId) {
		userId = parseInt(userId || 0) || 0;
		var user = allUsers.find(function(u) { return parseInt(u.id || 0) === userId; });
		if (!user) return;
		
		var userIntId = parseInt(user.id || 0) || 0;
		var index = selectedUsers.findIndex(function(u) { return parseInt(u.id || 0) === userIntId; });
		if (index >= 0) {
			selectedUsers.splice(index, 1);
		} else {
			selectedUsers.push(user);
		}
		
		updateSelectedRecipients();
		displayUsers(allUsers);
	};
	
	// Update selected recipients
	function updateSelectedRecipients() {
		selectedRecipientsList.innerHTML = '';
		if (selectedUsers.length === 0) {
			var emptySpan = document.createElement('span');
			emptySpan.className = 'text-muted';
			emptySpan.textContent = 'Belum ada penerima yang dipilih';
			selectedRecipientsList.appendChild(emptySpan);
			selectedRecipientsInput.value = '';
		} else {
			selectedUsers.forEach(function(user) {
				var safeName = (user.namalengkap || user.username || 'User').replace(/\r\n/g, ' ').replace(/\n/g, ' ').replace(/\r/g, ' ').replace(/\t/g, ' ');
				var userId = parseInt(user.id || 0) || 0;
				
				var badge = document.createElement('span');
				badge.className = 'badge bg-primary me-1 mb-1';
				
				var nameText = document.createTextNode(escapeHtml(safeName) + ' ');
				badge.appendChild(nameText);
				
				var removeSpan = document.createElement('span');
				removeSpan.style.cursor = 'pointer';
				removeSpan.textContent = '×';
				removeSpan.onclick = function() { removeUser(userId); };
				badge.appendChild(removeSpan);
				
				selectedRecipientsList.appendChild(badge);
			});
			selectedRecipientsInput.value = selectedUsers.map(function(u) { return u.id; }).join(',');
		}
	}
	
	// Remove user
	window.removeUser = function(userId) {
		userId = parseInt(userId || 0) || 0;
		selectedUsers = selectedUsers.filter(function(u) { return parseInt(u.id || 0) !== userId; });
		updateSelectedRecipients();
		displayUsers(allUsers);
	};
	
	// Update bulk buttons
	function updateBulkSelectButtons() {
		var displayed = getCurrentDisplayedUsers();
		var selectedCount = displayed.filter(function(u) {
			return selectedUsers.some(function(su) { return su.id == u.id; });
		}).length;
		
		selectAllBtn.disabled = (selectedCount === displayed.length && displayed.length > 0);
		clearAllBtn.disabled = (selectedUsers.length === 0);
	}
	
	function getCurrentDisplayedUsers() {
		var filtered = allUsers;
		var search = userSearch.value.toLowerCase();
		var role = roleFilter.value;
		
		if (search) {
			filtered = filtered.filter(function(user) {
				return (user.namalengkap || '').toLowerCase().includes(search) ||
					(user.username || '').toLowerCase().includes(search) ||
					(user.email || '').toLowerCase().includes(search);
			});
		}
		
		if (role) {
			filtered = filtered.filter(function(user) { return user.role === role; });
		}
		
		return filtered;
	}
	
	// Event listeners
	var searchTimeout;
	userSearch.addEventListener('input', function() {
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(loadUsers, 300);
	});
	
	roleFilter.addEventListener('change', loadUsers);
	
		selectAllBtn.addEventListener('click', function() {
			var displayed = getCurrentDisplayedUsers();
			displayed.forEach(function(user) {
				var userId = parseInt(user.id || 0) || 0;
				if (!selectedUsers.some(function(su) { return parseInt(su.id || 0) === userId; })) {
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
	
	// Reply data is now loaded via AJAX above
	
	// Form submission
	document.getElementById('messageForm').addEventListener('submit', function(e) {
		e.preventDefault();
		
		if (selectedUsers.length === 0) {
			alert('Pilih minimal satu penerima');
			return;
		}
		
		var formData = new FormData(this);
		var content = '';
		if (quill && quill.root) {
			content = quill.root.innerHTML;
		} else {
			content = document.getElementById('content').value;
		}
		formData.set('content', content);
		
		formData.delete('recipients[]');
		selectedUsers.forEach(function(user) {
			var userId = parseInt(user.id || 0) || 0;
			if (userId > 0) {
				formData.append('recipients[]', userId);
			}
		});
		
		var submitBtn = this.querySelector('button[type="submit"]');
		var originalText = '';
		if (submitBtn) {
			originalText = submitBtn.textContent || submitBtn.innerText || '';
			submitBtn.textContent = 'Mengirim...';
			submitBtn.disabled = true;
		}
		
		fetch(this.action, {
			method: 'POST',
			body: formData
		})
		.then(function(response) {
			if (response.redirected) {
				window.location.href = response.url;
			}
		})
		.catch(function(error) {
			alert('Terjadi kesalahan saat mengirim pesan');
		})
		.finally(function() {
			if (submitBtn) {
				submitBtn.textContent = originalText;
				submitBtn.disabled = false;
			}
		});
	});
	
	// Initial load
	loadUsers();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
