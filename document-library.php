<?php
// index.php (Dashboard)

// 1) Auth guard MUST be first (no output before this)
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

// 2) Optional: page title for your header include
$pageTitle = 'Document Library';

$areaDetails = [];
$loadError = null;

$tableCheck = $conn->query("SHOW TABLES LIKE 'area_details'");
if ($tableCheck === false) {
    $loadError = 'Unable to load area details.';
} else {
    $tableExists = $tableCheck->num_rows > 0;
    $tableCheck->free();

    if ($tableExists) {
        $sql = <<<'SQL'
            SELECT
                id,
                property_id,
                property_name,
                address,
                area_title,
                created_at
            FROM area_details
            ORDER BY created_at DESC, id DESC
            SQL;

        $result = $conn->query($sql);
        if ($result === false) {
            $loadError = 'Unable to load area details.';
        } else {
            while ($row = $result->fetch_assoc()) {
                $areaDetails[] = $row;
            }
            $result->free();
        }
    }
}

$totalDocuments = count($areaDetails);
$activeDocuments = $totalDocuments;
$archivedDocuments = 0;

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
                                        <button class="dl-tab is-active" data-filter="all">All (<?php echo $totalDocuments; ?>)</button>
                                        <button class="dl-tab" data-filter="active">Active (<?php echo $activeDocuments; ?>)</button>
                                        <button class="dl-tab" data-filter="archived">Archived (<?php echo $archivedDocuments; ?>)</button>
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
                                                <?php if ($loadError): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-danger">
                                                            <?php echo htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>
                                                    </tr>
                                                <?php elseif (!$areaDetails): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">
                                                            No area details found yet.
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($areaDetails as $detail): ?>
                                                        <?php
                                                        $areaDetailId = (int) ($detail['id'] ?? 0);
                                                        $propertyName = trim((string) ($detail['property_name'] ?? ''));
                                                        if ($propertyName === '') {
                                                            $propertyName = 'Untitled Property';
                                                        }

                                                        $propertyId = trim((string) ($detail['property_id'] ?? ''));
                                                        $address = trim((string) ($detail['address'] ?? ''));
                                                        $areaTitle = trim((string) ($detail['area_title'] ?? ''));
                                                        $createdRaw = trim((string) ($detail['created_at'] ?? ''));
                                                        $createdDisplay = '—';

                                                        if ($createdRaw !== '') {
                                                            $timestamp = strtotime($createdRaw);
                                                            if ($timestamp !== false) {
                                                                $createdDisplay = date('n/j/Y', $timestamp);
                                                            } else {
                                                                $createdDisplay = $createdRaw;
                                                            }
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <div class="prop-title"><?php echo htmlspecialchars($propertyName, ENT_QUOTES, 'UTF-8'); ?></div>
                                                                <?php if ($propertyId !== ''): ?>
                                                                    <div class="prop-meta">ID: <?php echo htmlspecialchars($propertyId, ENT_QUOTES, 'UTF-8'); ?></div>
                                                                <?php endif; ?>
                                                                <?php if ($address !== ''): ?>
                                                                    <div class="prop-meta prop-loc">
                                                                        <img src="assets/icons/location.png" alt="">
                                                                        <?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="area-size">
                                                                    <?php echo $areaTitle !== '' ? htmlspecialchars($areaTitle, ENT_QUOTES, 'UTF-8') : '<span class="text-muted">—</span>'; ?>
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="date-wrap">
                                                                    <img src="assets/icons/calendar.png" alt="">
                                                                    <span><?php echo htmlspecialchars($createdDisplay, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                </div>
                                                            </td>

                                                            <td class="td-actions">
                                                                <a class="act-btn" aria-label="Edit" href="area-details.php?id=<?php echo htmlspecialchars((string) $areaDetailId, ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <img src="assets/icons/edit.png" alt="">
                                                                </a>
                                                                <a class="act-btn" aria-label="View" href="view-area-details.php?id=<?php echo htmlspecialchars((string) $areaDetailId, ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <img src="assets/icons/eye.png" alt="">
                                                                </a>
                                                                <a class="act-btn" aria-label="Generate PDF" href="generate-area-pdf.php?id=<?php echo htmlspecialchars((string) $areaDetailId, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                                                    <img src="assets/icons/download.png" alt="">
                                                                </a>
                                                                <button class="act-btn danger" aria-label="Delete">
                                                                    <img src="assets/icons/trash.png" alt="">
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
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
