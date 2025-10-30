<?php if ($page != 'login') : ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-<?= $tema ?> shadow">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php?dashboard">🗂️ <?= $nama_app ?></a>
      <div class="ms-auto">
        <a href="?profil">
          <span class="text-white me-3 d-none d-lg-inline">
            Halo, <?= htmlspecialchars($user['nama']) ?> (<?= $user['role'] ?>)
          </span>
          <span class="d-lg-none text-white">
            <i class="bi bi-person"></i>
          </span>
        </a>
        <a href="logout.php" class="btn btn-outline-light btn-sm d-none d-lg-inline">Logout</a>
        <a href="logout.php" class="d-lg-none">
          <i class="bi bi-box-arrow-right text-white"></i>
        </a>

      </div>
    </div>
  </nav>
<?php endif; ?>