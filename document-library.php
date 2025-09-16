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
                <!-- ================= DOCUMENT LIBRARY ================= -->
                <div class="Document-section">
                    <section class="hh-docs">
                        <!-- Header -->
                        <div class="row">
                            <div class="col-12">
                                <div class="dl-head">
                                    <span class="pi-icon"><img src="assets/icons/pdf.png" alt=""></span>
                                    <div>
                                        <h1 class="dl-title">Document Library</h1>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search + Tabs -->
                        <div class="row">
                            <div class="col-12">
                                <div class="dl-toolbar">
                                    <div class="dl-search">
                                        <span class="dl-search-ico"><img src="assets/icons/search.png" alt=""></span>
                                        <input type="text" class="dl-input" id="dlSearch" placeholder="Search by property name, ID, address, or owner...">
                                    </div>

                                    <div class="dl-tabs">
                                        <button class="dl-tab is-active" data-filter="all">All (2)</button>
                                        <button class="dl-tab" data-filter="active">Active (2)</button>
                                        <button class="dl-tab" data-filter="archived">Archived (0)</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- List Card (TABLE VERSION) -->
                        <div class="row">
                            <div class="col-12">
                                <div class="dl-card">
                                    <!-- Section Title strip -->
                                    <div class="dl-strip">
                                        <div class="dl-strip-title">All Project</div>
                                    </div>

                                    <!-- Table -->
                                    <div class="dl-table-wrap">
                                        <table class="dl-table">
                                            <thead>
                                                <tr>
                                                    <th>Property Details</th>
                                                    <th>Area Title</th>
                                                    <th>Created</th>
                                                    <th class="th-actions">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Row 1 -->
                                                <tr>
                                                    <td>
                                                        <div class="prop-title">Green Valley Apartments</div>
                                                        <div class="prop-meta">ID: PROP-001</div>
                                                        <div class="prop-meta prop-loc">
                                                            <img src="assets/icons/location.png" alt="">
                                                            123 Main Street, Sector 15, New Mumbai
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="area-size">Area Title</div>
                                                    </td>
                                                    
                                                    <td>
                                                        <div class="date-wrap">
                                                            <img src="assets/icons/calendar.png" alt="">
                                                            <span>9/10/2024</span>
                                                        </div>
                                                    </td>
                                                    
                                                    <td class="td-actions">
                                                        <button class="act-btn" aria-label="Edit">
                                                            <img src="assets/icons/edit.png" alt="">
                                                        </button>
                                                        <button class="act-btn" aria-label="View">
                                                            <img src="assets/icons/eye.png" alt="">
                                                        </button>
                                                        <button class="act-btn" aria-label="Download">
                                                            <img src="assets/icons/download.png" alt="">
                                                        </button>
                                                        <button class="act-btn danger" aria-label="Delete">
                                                            <img src="assets/icons/trash.png" alt="">
                                                        </button>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/common-footer.php'; ?>