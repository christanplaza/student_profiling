<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark min-vh-100">
    <a href="<?= $rootURL ?>/faculty/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4"></span>
        <button class="btn btn-dark btn-lg text-start d-flex align-items-center" style="background-color: #00a8e8;">
            <?php if (isset($deep) && $deep) : ?>
                <img src="../../assets/logo.png" class="img-fluid rounded-top w-25" alt="" />
            <?php else : ?>
                <img src="../assets/logo.png" class="img-fluid rounded-top w-25" alt="" />
            <?php endif; ?>
            intelli.fied
        </button>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= $rootURL ?>/faculty/account_information.php" class="nav-link text-white" aria-current="page">
                Account Information
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= $rootURL ?>/faculty/student_management.php" class="nav-link text-white" aria-current="page">
                Student Management
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= $rootURL ?>/faculty/password_management.php" class="nav-link text-white" aria-current="page">
                Password Reset Requests
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= $rootURL ?>/faculty/section_management.php" class="nav-link text-white" aria-current="page">
                Section Management
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <strong><i class="bi bi-person-circle"></i> <?= $_COOKIE['last_name']; ?>, <?= $_COOKIE['first_name']; ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li>
                <form method="POST">
                    <button class="dropdown-item" type="submit" name="logout">Sign out</button>
                </form>
            </li>
        </ul>
    </div>
</div>