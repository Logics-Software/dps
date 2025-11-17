<?php
$title = 'Pesan Terkirim';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
	<div class="breadcrumb-item">
		<div class="col-12">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
					<li class="breadcrumb-item"><a href="/messages">Pesan Masuk</a></li>
					<li class="breadcrumb-item active">Pesan Terkirim</li>
				</ol>
			</nav>
		</div>
	</div>

	<div class="card">
		<div class="card-header d-flex justify-content-between align-items-center">
			<h4 class="mb-0 me-auto">Pesan Terkirim</h4>
			<div class="d-flex gap-2">
				<a href="/messages/create" class="btn btn-primary btn-sm"><?= icon('square-plus', 'me-1 mb-1', 18) ?> Tulis Pesan</a>
				<a href="/messages" class="btn btn-secondary btn-sm"><?= icon('table-list', 'me-1 mb-1', 18) ?> Masuk</a>
			</div>
		</div>

		<div class="card-body">
			<!-- Search Form with Action Buttons -->
			<div class="row mb-3">
				<div class="col-md-6 mb-2">
					<form method="GET" action="/messages/sent" class="d-flex" id="searchForm">
						<div class="input-group">
							<input type="text" name="search" class="form-control" placeholder="Cari pesan terkirim..." value="<?= htmlspecialchars($search ?? '') ?>" id="searchInput">
							<button type="button" class="btn btn-secondary" id="searchToggleBtn" title="Search">
								<span id="searchIcon">🔍</span>
							</button>
						</div>
					</form>
				</div>
				<div class="col-md-2 mb-2">
					<select class="form-select" id="per_page" name="per_page" onchange="window.location.href='/messages/sent?' + new URLSearchParams({...new URLSearchParams(window.location.search), per_page: this.value}).toString()">
						<?php foreach ([10, 20, 30, 50, 100] as $pp): ?>
						<option value="<?= $pp ?>" <?= ($pagination['per_page'] ?? 20) == $pp ? 'selected' : '' ?>><?= $pp ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<?php if (empty($messages)): ?>
				<div class="text-center py-5">
					<?= icon('paper-plane', 'mb-3', 48) ?>
					<?php if (!empty($search)): ?>
						<h5 class="text-muted">Tidak ada hasil pencarian</h5>
						<p class="text-muted">Tidak ada pesan terkirim yang sesuai dengan pencarian "<strong><?= htmlspecialchars($search) ?></strong>"</p>
						<a href="/messages/sent" class="btn btn-secondary">
							<?= icon('list-check', 'me-1 mb-1', 18) ?> Lihat Semua Pesan Terkirim
						</a>
					<?php else: ?>
						<h5 class="text-muted">Belum ada pesan terkirim</h5>
						<p class="text-muted">Anda belum mengirim pesan apapun.</p>
						<a href="/messages/create" class="btn btn-primary">
							<?= icon('square-plus', 'me-1 mb-1', 18) ?> Tulis Pesan Pertama
						</a>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-striped align-middle">
						<thead>
							<tr>
								<th width="5%"></th>
								<th width="40%">Subjek</th>
								<th width="30%">Penerima</th>
								<th width="15%">Tanggal</th>
								<th width="10%">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($messages as $message): ?>
								<tr>
									<td>
										<?= icon('paper-plane', 'text-primary', 16) ?>
									</td>
									<td>
										<div class="fw-bold"><?= htmlspecialchars($message['subject'] ?? '') ?></div>
										<small class="text-muted">
											<?= htmlspecialchars(substr(strip_tags($message['content'] ?? ''), 0, 100)) ?>
											<?php if (strlen(strip_tags($message['content'] ?? '')) > 100): ?>...<?php endif; ?>
										</small>
									</td>
									<td>
										<small class="text-muted">
											<?php 
											$recipients = $message['recipient_names'] ?? '';
											$recipientCount = $message['recipient_count'] ?? 0;
											
											if (empty($recipients) || $recipientCount == 0) {
												echo '<span class="text-muted">Tidak ada penerima</span>';
											} else {
												$displayRecipients = $recipients;
												if (strlen($recipients) > 50) {
													$displayRecipients = substr($recipients, 0, 50) . '...';
												}
												echo htmlspecialchars($displayRecipients);
												if ($recipientCount > 1) {
													echo ' <span class="badge bg-success">' . $recipientCount . '</span>';
												}
											}
											?>
										</small>
									</td>
									<td>
										<small class="text-muted">
											<?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
										</small>
									</td>
									<td>
										<div class="d-flex gap-1">
											<a href="/messages/show/<?= $message['id'] ?>" class="btn btn-info btn-sm" 
											data-bs-toggle="tooltip" data-bs-title="Lihat Pesan">
												<?= icon('eye', 'mb-0', 16) ?>
											</a>
											<button type="button" class="btn btn-danger btn-sm" onclick="deleteMessage(<?= $message['id'] ?>)" 
											data-bs-toggle="tooltip" data-bs-title="Hapus Pesan">
												<?= icon('trash', 'mb-0', 16) ?>
											</button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
				<div class="row mt-3">
					<div class="col-12">
						<nav aria-label="Sent Messages pagination">
							<ul class="pagination justify-content-center">
								<?php
								$queryParams = [];
								if (!empty($search)) $queryParams['search'] = $search;
								if (!empty($pagination['per_page'])) $queryParams['per_page'] = $pagination['per_page'];
								$queryString = http_build_query($queryParams);
								?>
								
								<?php if ($pagination['has_prev']): ?>
									<li class="page-item">
										<a class="page-link" href="/messages/sent?page=<?= $pagination['current_page'] - 1 ?><?= !empty($queryString) ? '&' . $queryString : '' ?>">Previous</a>
									</li>
								<?php endif; ?>

								<?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
									<?php $activeClass = $i == $pagination['current_page'] ? ' active' : ''; ?>
									<li class="page-item<?= $activeClass ?>">
										<a class="page-link" href="/messages/sent?page=<?= $i ?><?= !empty($queryString) ? '&' . $queryString : '' ?>"><?= $i ?></a>
									</li>
								<?php endfor; ?>

								<?php if ($pagination['has_next']): ?>
									<li class="page-item">
										<a class="page-link" href="/messages/sent?page=<?= $pagination['current_page'] + 1 ?><?= !empty($queryString) ? '&' . $queryString : '' ?>">Next</a>
									</li>
								<?php endif; ?>
							</ul>
						</nav>

						<div class="text-center text-muted mt-2">
							Menampilkan <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> sampai 
							<?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']) ?> 
							dari <?= $pagination['total_items'] ?> pesan terkirim
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteMessageModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Konfirmasi Hapus</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				Apakah Anda yakin ingin menghapus pesan ini? Tindakan ini tidak dapat dibatalkan.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-danger" id="confirmDeleteMessage">Hapus</button>
			</div>
		</div>
	</div>
</div>

<script>
let deleteMessageId = null;

function deleteMessage(messageId) {
	deleteMessageId = messageId;
	const modal = new bootstrap.Modal(document.getElementById("deleteMessageModal"));
	modal.show();
}

document.addEventListener("DOMContentLoaded", function() {
	const confirmBtn = document.getElementById("confirmDeleteMessage");
	if (confirmBtn) {
		confirmBtn.addEventListener("click", function() {
			if (deleteMessageId) {
				fetch(`/messages/delete/${deleteMessageId}`, {
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						location.reload();
					} else {
						const modal = bootstrap.Modal.getInstance(document.getElementById("deleteMessageModal"));
						modal.hide();
						alert(data.message || 'Gagal menghapus pesan');
					}
				})
				.catch(error => {
					const modal = bootstrap.Modal.getInstance(document.getElementById("deleteMessageModal"));
					modal.hide();
					alert('Terjadi kesalahan saat menghapus pesan');
				});
			}
		});
	}

	// Search/Reset Toggle
	const searchForm = document.getElementById('searchForm');
	const searchInput = document.getElementById('searchInput');
	const searchToggleBtn = document.getElementById('searchToggleBtn');
	
	if (searchForm && searchInput && searchToggleBtn) {
		let isSearchMode = true;
		
		if (searchInput.value.trim() !== '') {
			isSearchMode = false;
			updateButtonState();
		}
		
		function updateButtonState() {
			const searchIcon = document.getElementById('searchIcon');
			if (isSearchMode) {
				searchToggleBtn.title = 'Search';
				if (searchIcon) searchIcon.textContent = '🔍';
				searchToggleBtn.onclick = function() {
					searchForm.submit();
				};
			} else {
				searchToggleBtn.title = 'Reset';
				if (searchIcon) searchIcon.textContent = '✕';
				searchToggleBtn.onclick = function() {
					searchInput.value = '';
					searchForm.submit();
				};
			}
		}
		
		searchInput.addEventListener('input', function() {
			const hasValue = this.value.trim() !== '';
			if (hasValue && isSearchMode) {
				isSearchMode = false;
				updateButtonState();
			} else if (!hasValue && !isSearchMode) {
				isSearchMode = true;
				updateButtonState();
			}
		});
		
		updateButtonState();
	}
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

