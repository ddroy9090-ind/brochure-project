<?php
// index.php (Dashboard)

// 1) Auth guard MUST be first (no output before this)
require_once __DIR__ . '/includes/auth_check.php';

// 2) Optional: page title for your header include
$pageTitle = 'Dashboard';

// 3) Common header (HTML <head>, opening <body>, etc.)
require_once __DIR__ . '/includes/common-header.php';
?>

<div class="container-fluid cms-layout">
    <div class="row h-100">

        <!-- Sidebar -->
        <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col content" id="content">
            <!-- Top Navbar -->
            <?php require_once __DIR__ . '/includes/topbar.php'; ?>

            <!-- Dashboard Content -->
            <div class="p-2">
                <section class="cms-section py-4">
                    <div class="container">

                        <!-- Welcome (optional) -->
                        <?php if (!empty($_SESSION['user_name'])): ?>
                            <div class="mb-3">
                                <h5 class="mb-0">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                                <small class="text-muted">You are logged in as <?= htmlspecialchars($_SESSION['user_email'] ?? ''); ?></small>
                            </div>
                        <?php endif; ?>

                        <!-- Top Stats -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <h6>Total Reports <img src="assets/icons/report.png" alt="" width="16"></h6>
                                    <p>5</p>
                                    <span class="text-success">100% from last month</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <h6>Completed <img src="assets/icons/check.png" alt="" width="16"></h6>
                                    <p>2</p>
                                    <span class="text-success">100% completion rate</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <h6>Processing <img src="assets/icons/clock.png" alt="" width="16"></h6>
                                    <p>1</p>
                                    <span class="text-muted">Currently in progress</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card">
                                    <h6>Failed <img src="assets/icons/warning.png" alt="" width="16"></h6>
                                    <p>1</p>
                                    <span class="text-danger">Requires attention</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add your dashboard widgets here -->

                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/common-footer.php'; ?>
