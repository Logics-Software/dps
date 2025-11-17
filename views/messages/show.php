<?php
$title = 'Detail Pesan';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
	<div class="breadcrumb-item">
		<div class="col-12">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
					<li class="breadcrumb-item"><a href="/messages">Pesan Masuk</a></li>
					<li class="breadcrumb-item active">Detail Pesan</li>
				</ol>
			</nav>
		</div>
	</div>

	<div class="card">
		<div class="card-header d-flex justify-content-between align-items-center">
			<div>
				<h4 class="mb-0"><?= htmlspecialchars($message['subject'] ?? '(No Subject)') ?></h4>
				<small class="text-muted">
					Dari: <?= htmlspecialchars($message['sender_name'] ?? 'Unknown') ?> 
					(<?= htmlspecialchars($message['sender_email'] ?? '-') ?>)
				</small>
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-8">
					<!-- Message Content -->
					<div class="mb-4">
						<div class="d-flex align-items-center mb-3">
							<?php if (!empty($message['sender_picture'])): ?>
								<img src="<?= BASE_URL . htmlspecialchars($message['sender_picture']) ?>" 
										alt="<?= htmlspecialchars($message['sender_name']) ?>" 
										class="rounded-circle me-3"
										style="width: 48px; height: 48px; object-fit: cover;">
							<?php else: ?>
								<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
									<?= strtoupper(substr($message['sender_name'] ?? 'U', 0, 1)) ?>
								</div>
							<?php endif; ?>
							<div>
								<h6 class="mb-1"><?= htmlspecialchars($message['sender_name'] ?? 'Unknown') ?></h6>
								<small class="text-muted">
									<?= date('d F Y, H:i', strtotime($message['created_at'] ?? 'now')) ?>
									<?php if (($message['status'] ?? '') === 'read'): ?>
										<span class="badge bg-success ms-2">Sudah dibaca</span>
									<?php endif; ?>
								</small>
							</div>
						</div>
					</div>
					
					<div class="message-body border rounded p-3 mb-3" style="min-height: 200px;">
						<?= $message['content'] ?? '' ?>
					</div>
					
					<?php if (!empty($message['attachments'])): ?>
					<div class="mt-4">
						<h6 class="mb-3">
							<?= icon('file-pdf', 'me-1 mb-1', 18) ?> Lampiran
						</h6>
						<div class="row">
							<?php foreach ($message['attachments'] as $attachment): ?>
							<div class="col-md-6 mb-2">
								<div class="card">
									<div class="card-body p-2">
										<div class="d-flex align-items-center">
											<?= icon('file-pdf', 'me-2 text-primary', 20) ?>
											<div class="flex-grow-1">
												<div class="fw-bold"><?= htmlspecialchars($attachment['original_name']) ?></div>
												<small class="text-muted">
													<?= number_format($attachment['file_size'] / 1024, 1) ?> KB
												</small>
											</div>
											<a href="<?= BASE_URL . $attachment['file_path'] ?>" 
												class="btn btn-sm btn-outline-primary" 
												target="_blank" 
												download="<?= htmlspecialchars($attachment['original_name']) ?>">
												<?= icon('arrow-down', 'mb-0', 16) ?>
											</a>
										</div>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
				
				<div class="col-md-4">
					<!-- Recipients - Only show for sent messages -->
					<?php if (!empty($message['recipients']) && !$is_recipient): ?>
						<div class="card mt-3">
							<div class="card-header">
								<h6 class="mb-0">
									<?= icon('users', 'me-1 mb-1', 18) ?> Penerima
								</h6>
							</div>
							<div class="card-body">
								<?php foreach ($message['recipients'] as $recipient): ?>
									<div class="d-flex align-items-center mb-2">
										<?php if (!empty($recipient['recipient_picture'])): ?>
											<img src="<?= BASE_URL . htmlspecialchars($recipient['recipient_picture']) ?>" 
													alt="<?= htmlspecialchars($recipient['recipient_name']) ?>" 
													class="rounded-circle me-2"
													style="width: 32px; height: 32px; object-fit: cover;">
										<?php else: ?>
											<div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
												<?= strtoupper(substr($recipient['recipient_name'] ?? 'U', 0, 1)) ?>
											</div>
										<?php endif; ?>
										<div class="flex-grow-1">
											<div class="fw-bold"><?= htmlspecialchars($recipient['recipient_name']) ?></div>
											<small class="text-muted"><?= htmlspecialchars($recipient['recipient_email']) ?></small>
										</div>
										<div>
											<?php if ($recipient['is_read']): ?>
												<span class="badge bg-success">Dibaca</span>
											<?php else: ?>
												<span class="badge bg-warning">Belum</span>
											<?php endif; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="card-footer d-flex justify-content-between align-items-center">
			<a href="/messages" class="btn btn-secondary">
				<?= icon('arrow-left', 'me-1 mb-1', 18) ?> Kembali
			</a>
			<div class="d-flex gap-2">
				<a href="/messages/create?reply=<?= $message['id'] ?>" class="btn btn-primary">
					<?= icon('arrow-right', 'me-1 mb-1', 18) ?> Balas
				</a>
				<a href="/messages/create?forward=<?= $message['id'] ?>" class="btn btn-primary">
					<?= icon('share-from-square', 'me-1 mb-1', 18) ?> Teruskan
				</a>
				<button type="button" class="btn btn-primary" onclick="window.print()">
					<?= icon('display', 'me-1 mb-1', 18) ?> Cetak
				</button>
				<button type="button" class="btn btn-danger" onclick="deleteMessage(<?= $message['id'] ?>)">
					<?= icon('trash', 'me-1 mb-1', 18) ?> Hapus
				</button>
			</div>
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
						window.location.href = '/messages';
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
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

