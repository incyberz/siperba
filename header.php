<?php if ($page != 'login') : ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-<?= $tema ?> shadow">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php?dashboard">🗂️ <?= $nama_app ?></a>
      <div class="ms-auto">
        <a href="?profil">
          <span class="text-white me-3">
            Halo, <?= htmlspecialchars($user['nama']) ?> (<?= $user['role'] ?>)
          </span>
        </a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>
<?php endif; ?>