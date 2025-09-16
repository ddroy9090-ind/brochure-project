<?php
// index.php (Dashboard)

// 1) Auth guard MUST be first (no output before this)
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

// 2) Optional: page title for your header include
$pageTitle = 'Dashboard';

$successMessage = null;
$errorMessage = null;

$normalizeDate = static function (?string $value): string {
    $value = trim((string) ($value ?? ''));
    if ($value === '') {
        return '';
    }

    $date = date_create($value);
    return $date ? $date->format('Y-m-d') : '';
};

$escapeHtml = static function (?string $value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$defaultFormData = [
    'property_id' => '',
    'registration_no' => '',
    'property_name' => '',
    'address' => '',
    'project_name' => '',
    'developer_name' => '',
    'title' => '',
    'about_details' => '',
    'about_developer' => '',
    'starting_price' => '',
    'payment_plan' => '',
    'handover_date' => '',
    'area_title' => '',
    'area_heading' => '',
    'area_description' => '',
    'amenities' => [],
    'project_title_2' => '',
    'project_title_3' => '',
    'price_from' => '',
    'handover_date_3' => '',
    'location_3' => '',
    'development_time' => '',
    'project_description_2' => '',
    'down_payment' => '',
    'pre_handover' => '',
    'handover' => '',
];

$formData = $defaultFormData;
$editingId = null;

$amenityChecked = static function (array $selectedAmenities, string $value): string {
    return in_array($value, $selectedAmenities, true) ? ' checked' : '';
};

$loadAreaDetail = static function (mysqli $conn, int $areaId) use ($normalizeDate, $defaultFormData): ?array {
    if ($areaId <= 0) {
        return null;
    }

    $sql = 'SELECT
            property_id,
            registration_no,
            property_name,
            address,
            project_name,
            developer_name,
            title,
            about_details,
            about_developer,
            starting_price,
            payment_plan,
            handover_date,
            area_title,
            area_heading,
            area_description,
            amenities,
            project_title_2,
            project_title_3,
            price_from,
            handover_date_3,
            location_3,
            development_time,
            project_description_2,
            down_payment,
            pre_handover,
            handover
        FROM area_details
        WHERE id = ' . $areaId . '
        LIMIT 1';

    $result = $conn->query($sql);
    if ($result === false) {
        throw new RuntimeException('Unable to load area details: ' . $conn->error);
    }

    $row = $result->fetch_assoc();
    $result->free();

    if (!$row) {
        return null;
    }

    $data = $defaultFormData;

    $data['property_id'] = trim((string) ($row['property_id'] ?? ''));
    $data['registration_no'] = trim((string) ($row['registration_no'] ?? ''));
    $data['property_name'] = trim((string) ($row['property_name'] ?? ''));
    $data['address'] = trim((string) ($row['address'] ?? ''));
    $data['project_name'] = trim((string) ($row['project_name'] ?? ''));
    $data['developer_name'] = trim((string) ($row['developer_name'] ?? ''));
    $data['title'] = trim((string) ($row['title'] ?? ''));
    $data['about_details'] = trim((string) ($row['about_details'] ?? ''));
    $data['about_developer'] = trim((string) ($row['about_developer'] ?? ''));
    $data['starting_price'] = trim((string) ($row['starting_price'] ?? ''));
    $data['payment_plan'] = trim((string) ($row['payment_plan'] ?? ''));
    $data['handover_date'] = $normalizeDate($row['handover_date'] ?? null);
    $data['area_title'] = trim((string) ($row['area_title'] ?? ''));
    $data['area_heading'] = trim((string) ($row['area_heading'] ?? ''));
    $data['area_description'] = trim((string) ($row['area_description'] ?? ''));

    $amenities = [];
    $amenitiesRaw = trim((string) ($row['amenities'] ?? ''));
    if ($amenitiesRaw !== '') {
        $decoded = json_decode($amenitiesRaw, true);
        if (is_array($decoded)) {
            $amenities = array_values(array_filter(array_map(static function ($item) {
                return is_string($item) ? trim($item) : '';
            }, $decoded), static function ($value) {
                return $value !== '';
            }));
        }
    }
    $data['amenities'] = $amenities;

    $data['project_title_2'] = trim((string) ($row['project_title_2'] ?? ''));
    $data['project_title_3'] = trim((string) ($row['project_title_3'] ?? ''));
    $data['price_from'] = trim((string) ($row['price_from'] ?? ''));
    $data['handover_date_3'] = $normalizeDate($row['handover_date_3'] ?? null);
    $data['location_3'] = trim((string) ($row['location_3'] ?? ''));
    $data['development_time'] = trim((string) ($row['development_time'] ?? ''));
    $data['project_description_2'] = trim((string) ($row['project_description_2'] ?? ''));
    $data['down_payment'] = trim((string) ($row['down_payment'] ?? ''));
    $data['pre_handover'] = trim((string) ($row['pre_handover'] ?? ''));
    $data['handover'] = trim((string) ($row['handover'] ?? ''));

    return $data;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $requestedId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($requestedId !== false && $requestedId !== null) {
        try {
            $loadedData = $loadAreaDetail($conn, (int) $requestedId);
            if ($loadedData === null) {
                $errorMessage = 'Area detail not found.';
            } else {
                $formData = $loadedData;
                $editingId = (int) $requestedId;
            }
        } catch (Throwable $exception) {
            $errorMessage = $exception->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionStarted = false;
    $fileStmt = null;

    try {
        $editingIdValue = trim((string) ($_POST['area_detail_id'] ?? ''));
        if ($editingIdValue !== '') {
            $editingIdCandidate = (int) $editingIdValue;
            if ($editingIdCandidate <= 0) {
                throw new RuntimeException('Invalid area detail specified.');
            }
            $editingId = $editingIdCandidate;
        } else {
            $editingId = null;
        }

        $formData = $defaultFormData;

        $formData['property_id'] = trim((string) ($_POST['property_id'] ?? ''));
        $formData['registration_no'] = trim((string) ($_POST['registration_no'] ?? ''));
        $formData['property_name'] = trim((string) ($_POST['property_name'] ?? ''));
        $formData['address'] = trim((string) ($_POST['address'] ?? ''));
        $formData['project_name'] = trim((string) ($_POST['project_name'] ?? ''));
        $formData['developer_name'] = trim((string) ($_POST['developer_name'] ?? ''));
        $formData['title'] = trim((string) ($_POST['title'] ?? ''));
        $formData['about_details'] = trim((string) ($_POST['about_details'] ?? ''));
        $formData['about_developer'] = trim((string) ($_POST['about_developer'] ?? ''));
        $formData['starting_price'] = trim((string) ($_POST['starting_price'] ?? ''));
        $formData['payment_plan'] = trim((string) ($_POST['payment_plan'] ?? ''));
        $formData['handover_date'] = $normalizeDate($_POST['handover_date'] ?? null);
        $formData['area_title'] = trim((string) ($_POST['area_title'] ?? ''));
        $formData['area_heading'] = trim((string) ($_POST['area_heading'] ?? ''));
        $formData['area_description'] = trim((string) ($_POST['area_description'] ?? ''));

        $amenitiesRaw = $_POST['amenities'] ?? [];
        if (!is_array($amenitiesRaw)) {
            $amenitiesRaw = [];
        }
        $formData['amenities'] = array_values(array_filter(array_map(static function ($item) {
            return trim((string) $item);
        }, $amenitiesRaw), static function ($value) {
            return $value !== '';
        }));

        $formData['project_title_2'] = trim((string) ($_POST['project_title_2'] ?? ''));
        $formData['project_title_3'] = trim((string) ($_POST['project_title_3'] ?? ''));
        $formData['price_from'] = trim((string) ($_POST['price_from'] ?? ''));
        $formData['handover_date_3'] = $normalizeDate($_POST['handover_date_3'] ?? null);
        $formData['location_3'] = trim((string) ($_POST['location_3'] ?? ''));
        $formData['development_time'] = trim((string) ($_POST['development_time'] ?? ''));
        $formData['project_description_2'] = trim((string) ($_POST['project_description_2'] ?? ''));
        $formData['down_payment'] = trim((string) ($_POST['down_payment'] ?? ''));
        $formData['pre_handover'] = trim((string) ($_POST['pre_handover'] ?? ''));
        $formData['handover'] = trim((string) ($_POST['handover'] ?? ''));

        if ($formData['property_id'] === '' || $formData['property_name'] === '' || $formData['address'] === '') {
            throw new RuntimeException('Property ID, Property Name and Address are required.');
        }

        $isUpdate = $editingId !== null;

        if (!$conn->query('CREATE TABLE IF NOT EXISTS `area_details` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `property_id` VARCHAR(255) NOT NULL,
                `registration_no` VARCHAR(255) DEFAULT NULL,
                `property_name` VARCHAR(255) NOT NULL,
                `address` TEXT NOT NULL,
                `project_name` VARCHAR(255) DEFAULT NULL,
                `developer_name` VARCHAR(255) DEFAULT NULL,
                `title` VARCHAR(255) DEFAULT NULL,
                `about_details` LONGTEXT DEFAULT NULL,
                `about_developer` LONGTEXT DEFAULT NULL,
                `starting_price` VARCHAR(255) DEFAULT NULL,
                `payment_plan` VARCHAR(255) DEFAULT NULL,
                `handover_date` DATE DEFAULT NULL,
                `area_title` VARCHAR(255) DEFAULT NULL,
                `area_heading` VARCHAR(255) DEFAULT NULL,
                `area_description` LONGTEXT DEFAULT NULL,
                `amenities` LONGTEXT DEFAULT NULL,
                `project_title_2` VARCHAR(255) DEFAULT NULL,
                `project_title_3` VARCHAR(255) DEFAULT NULL,
                `price_from` VARCHAR(255) DEFAULT NULL,
                `handover_date_3` DATE DEFAULT NULL,
                `location_3` VARCHAR(255) DEFAULT NULL,
                `development_time` VARCHAR(255) DEFAULT NULL,
                `project_description_2` LONGTEXT DEFAULT NULL,
                `down_payment` VARCHAR(255) DEFAULT NULL,
                `pre_handover` VARCHAR(255) DEFAULT NULL,
                `handover` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;')) {
            throw new RuntimeException('Unable to ensure area_details table exists: ' . $conn->error);
        }

        if (!$conn->query('CREATE TABLE IF NOT EXISTS `area_detail_files` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `area_detail_id` INT(11) NOT NULL,
                `file_key` VARCHAR(100) NOT NULL,
                `file_name` VARCHAR(255) DEFAULT NULL,
                `mime_type` VARCHAR(150) DEFAULT NULL,
                `file_size` INT(11) DEFAULT NULL,
                `file_data` LONGBLOB NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `fk_area_detail` (`area_detail_id`),
                CONSTRAINT `fk_area_detail` FOREIGN KEY (`area_detail_id`) REFERENCES `area_details` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;')) {
            throw new RuntimeException('Unable to ensure area_detail_files table exists: ' . $conn->error);
        }

        if ($isUpdate) {
            $existingData = $loadAreaDetail($conn, $editingId);
            if ($existingData === null) {
                throw new RuntimeException('Area detail not found.');
            }
        }

        if (!$conn->begin_transaction()) {
            throw new RuntimeException('Unable to start database transaction: ' . $conn->error);
        }
        $transactionStarted = true;

        $amenitiesJson = $formData['amenities']
            ? json_encode($formData['amenities'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : '';
        $handoverDate = $formData['handover_date'];
        $handoverDate3 = $formData['handover_date_3'];

        $propertyId = $formData['property_id'];
        $registrationNo = $formData['registration_no'];
        $propertyName = $formData['property_name'];
        $address = $formData['address'];
        $projectName = $formData['project_name'];
        $developerName = $formData['developer_name'];
        $title = $formData['title'];
        $aboutDetails = $formData['about_details'];
        $aboutDeveloper = $formData['about_developer'];
        $startingPrice = $formData['starting_price'];
        $paymentPlan = $formData['payment_plan'];
        $areaTitle = $formData['area_title'];
        $areaHeading = $formData['area_heading'];
        $areaDescription = $formData['area_description'];
        $projectTitle2 = $formData['project_title_2'];
        $projectTitle3 = $formData['project_title_3'];
        $priceFrom = $formData['price_from'];
        $location3 = $formData['location_3'];
        $developmentTime = $formData['development_time'];
        $projectDescription2 = $formData['project_description_2'];
        $downPayment = $formData['down_payment'];
        $preHandover = $formData['pre_handover'];
        $handover = $formData['handover'];

        if ($isUpdate) {
            $updateSql = <<<'SQL'
                UPDATE area_details
                SET
                    property_id = ?,
                    registration_no = ?,
                    property_name = ?,
                    address = ?,
                    project_name = ?,
                    developer_name = ?,
                    title = ?,
                    about_details = ?,
                    about_developer = ?,
                    starting_price = ?,
                    payment_plan = ?,
                    handover_date = NULLIF(?, ''),
                    area_title = ?,
                    area_heading = ?,
                    area_description = ?,
                    amenities = NULLIF(?, ''),
                    project_title_2 = ?,
                    project_title_3 = ?,
                    price_from = ?,
                    handover_date_3 = NULLIF(?, ''),
                    location_3 = ?,
                    development_time = ?,
                    project_description_2 = ?,
                    down_payment = ?,
                    pre_handover = ?,
                    handover = ?
                WHERE id = ?
                SQL;
            $stmt = $conn->prepare($updateSql);
            if (!$stmt) {
                throw new RuntimeException('Unable to prepare area details update: ' . $conn->error);
            }

            $stmt->bind_param(
                str_repeat('s', 26) . 'i',
                $propertyId,
                $registrationNo,
                $propertyName,
                $address,
                $projectName,
                $developerName,
                $title,
                $aboutDetails,
                $aboutDeveloper,
                $startingPrice,
                $paymentPlan,
                $handoverDate,
                $areaTitle,
                $areaHeading,
                $areaDescription,
                $amenitiesJson,
                $projectTitle2,
                $projectTitle3,
                $priceFrom,
                $handoverDate3,
                $location3,
                $developmentTime,
                $projectDescription2,
                $downPayment,
                $preHandover,
                $handover,
                $editingId
            );
        } else {
            $insertSql = <<<'SQL'
                INSERT INTO area_details (
                    property_id, registration_no, property_name, address,
                    project_name, developer_name, title, about_details,
                    about_developer, starting_price, payment_plan, handover_date,
                    area_title, area_heading, area_description, amenities,
                    project_title_2, project_title_3, price_from, handover_date_3,
                    location_3, development_time, project_description_2, down_payment,
                    pre_handover, handover
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULLIF(?, ''),
                    ?, ?, ?, NULLIF(?, ''), ?, ?, ?, NULLIF(?, ''),
                    ?, ?, ?, ?, ?, ?
                )
                SQL;
            $stmt = $conn->prepare($insertSql);
            if (!$stmt) {
                throw new RuntimeException('Unable to prepare area details insert: ' . $conn->error);
            }

            $stmt->bind_param(
                str_repeat('s', 26),
                $propertyId,
                $registrationNo,
                $propertyName,
                $address,
                $projectName,
                $developerName,
                $title,
                $aboutDetails,
                $aboutDeveloper,
                $startingPrice,
                $paymentPlan,
                $handoverDate,
                $areaTitle,
                $areaHeading,
                $areaDescription,
                $amenitiesJson,
                $projectTitle2,
                $projectTitle3,
                $priceFrom,
                $handoverDate3,
                $location3,
                $developmentTime,
                $projectDescription2,
                $downPayment,
                $preHandover,
                $handover
            );
        }

        if (!$stmt->execute()) {
            throw new RuntimeException('Unable to save area details: ' . $stmt->error);
        }

        $areaDetailId = $isUpdate ? $editingId : (int) $stmt->insert_id;
        $stmt->close();

        $fileStmt = $conn->prepare('INSERT INTO area_detail_files (
                area_detail_id, file_key, file_name, mime_type, file_size, file_data
            ) VALUES (
                ?, ?, ?, ?, ?, ?
            )');

        if (!$fileStmt) {
            throw new RuntimeException('Unable to prepare file insert: ' . $conn->error);
        }

        $fileAreaId = $areaDetailId;
        $fileKeyParam = '';
        $fileNameParam = '';
        $fileMimeParam = '';
        $fileSizeParam = 0;
        $fileDataParam = '';

        if (!$fileStmt->bind_param('isssis', $fileAreaId, $fileKeyParam, $fileNameParam, $fileMimeParam, $fileSizeParam, $fileDataParam)) {
            throw new RuntimeException('Unable to bind file parameters: ' . $fileStmt->error);
        }

        $storeFile = static function (string $fieldKey, array $fileInfo) use ($fileStmt, &$fileKeyParam, &$fileNameParam, &$fileMimeParam, &$fileSizeParam, &$fileDataParam): void {
            if (!isset($fileInfo['error'])) {
                return;
            }

            if (is_array($fileInfo['error'])) {
                throw new RuntimeException('Invalid upload data for ' . $fieldKey . '.');
            }

            if ($fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
                return;
            }

            if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
                throw new RuntimeException('Error while uploading ' . $fieldKey . ' (code ' . $fileInfo['error'] . ').');
            }

            $tmpPath = (string) ($fileInfo['tmp_name'] ?? '');
            if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
                throw new RuntimeException('Uploaded file for ' . $fieldKey . ' is not valid.');
            }

            $fileContents = file_get_contents($tmpPath);
            if ($fileContents === false) {
                throw new RuntimeException('Unable to read uploaded file for ' . $fieldKey . '.');
            }

            $fileKeyParam = $fieldKey;
            $fileNameParam = (string) ($fileInfo['name'] ?? '');
            $fileMimeParam = (string) ($fileInfo['type'] ?? '');
            $fileSizeParam = (int) ($fileInfo['size'] ?? 0);
            $fileDataParam = $fileContents;

            if (!$fileStmt->execute()) {
                throw new RuntimeException('Unable to save uploaded file for ' . $fieldKey . ': ' . $fileStmt->error);
            }
        };

        $processMultipleFiles = static function (string $fieldKey, array $files) use ($storeFile): void {
            if (!isset($files['name'])) {
                return;
            }

            if (is_array($files['name'])) {
                $count = count($files['name']);
                for ($i = 0; $i < $count; $i++) {
                    $storeFile($fieldKey, [
                        'name' => $files['name'][$i] ?? null,
                        'type' => $files['type'][$i] ?? null,
                        'tmp_name' => $files['tmp_name'][$i] ?? null,
                        'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                        'size' => $files['size'][$i] ?? 0,
                    ]);
                }
                return;
            }

            $storeFile($fieldKey, $files);
        };

        if (isset($_FILES['banner_image'])) {
            $processMultipleFiles('banner_image', $_FILES['banner_image']);
        }
        if (isset($_FILES['area_image'])) {
            $processMultipleFiles('area_image', $_FILES['area_image']);
        }
        if (isset($_FILES['project_image_2'])) {
            $processMultipleFiles('project_image_2', $_FILES['project_image_2']);
        }
        if (isset($_FILES['transactions_image'])) {
            $processMultipleFiles('transactions_image', $_FILES['transactions_image']);
        }
        if (isset($_FILES['property_images'])) {
            $processMultipleFiles('property_images', $_FILES['property_images']);
        }
        if (isset($_FILES['floor_plan_file'])) {
            $processMultipleFiles('floor_plan_file', $_FILES['floor_plan_file']);
        }

        $conn->commit();
        $successMessage = $isUpdate ? 'Area details updated successfully.' : 'Area details saved successfully.';
        if ($isUpdate) {
            $editingId = $areaDetailId;
        } else {
            $formData = $defaultFormData;
            $editingId = null;
        }
        $_POST = [];
        $_FILES = [];
    } catch (Throwable $exception) {
        if ($transactionStarted) {
            $conn->rollback();
        }
        $errorMessage = $exception->getMessage();
    } finally {
        if ($fileStmt instanceof mysqli_stmt) {
            $fileStmt->close();
        }
    }
}

// 3) Common header (HTML <head>, opening <body>, etc.)
require_once __DIR__ . '/includes/common-header.php';
?>

<div class="container-fluid cms-layout">
    <div class="row h-100">

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php' ?>

        <!-- Main Content -->
        <div class="col content" id="content">
            <!-- Top Navbar -->
            <?php include 'includes/topbar.php' ?>

            <!-- Dashboard Content -->
            <div class="p-2">
                <?php if ($successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
                        <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
                        <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form class="hh-area-form" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); ?>" method="post" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="area_detail_id" value="<?php echo $editingId !== null ? $escapeHtml((string) $editingId) : ''; ?>">
                    <div class="form-section">

                        <div class="form-wrap">
                            <!-- Title -->
                            <div class="page-title">
                                <span class="pi-icon">
                                    <img src="assets/icons/property-information.png" alt="Info">
                                </span>
                                <h1>Project Information</h1>
                            </div>
                            <div class="page-sub">
                                <?php echo $editingId !== null ? 'Update property and area information' : 'Enter comprehensive property and area information'; ?>
                            </div>

                            <!-- Two columns -->
                            <div class="row g-3">

                                <!-- Basic Information -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/basic-info.png" alt="Info">
                                            <h3>Basic Information</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="propertyId">Property ID <span class="req">*</span></label>
                                                        <!-- Field: property_id -->
                                                        <input type="text" id="propertyId" name="property_id" required value="<?php echo $escapeHtml($formData['property_id']); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="regNo">Registration No.</label>
                                                        <!-- Field: registration_no -->
                                                        <input type="text" id="regNo" name="registration_no" value="<?php echo $escapeHtml($formData['registration_no']); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="propName">Property Name <span class="req">*</span></label>
                                                        <!-- Field: property_name -->
                                                        <input type="text" id="propName" name="property_name" required value="<?php echo $escapeHtml($formData['property_name']); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="field">
                                                        <label for="address">Full Address <span class="req">*</span></label>
                                                        <!-- Field: address -->
                                                        <textarea id="address" name="address" required><?php echo $escapeHtml($formData['address']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Property Details -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/property-details.png" alt="Measurements">
                                            <h3>Property Details</h3>
                                        </div>
                                        <div class="section-sub">Detailed area and dimension specifications</div>

                                        <div class="section-body">
                                            <div class="row">

                                                <!-- Upload Banner Image -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="bannerImage">Upload Banner Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="bannerImage" name="banner_image" accept="image/*" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <!-- Arrow icon -->
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" alt="" width="30">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="bnrName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Project Name -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="projectName">Project Name</label>
                                                        <input type="text" id="projectName" name="project_name" value="<?php echo $escapeHtml($formData['project_name']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Developer Name -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="developerName">Developer Name</label>
                                                        <input type="text" id="developerName" name="developer_name" value="<?php echo $escapeHtml($formData['developer_name']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- About Project -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/about-project.png" alt="Info">
                                            <h3>About Project</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">
                                            <div class="row">
                                                <!-- Title -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="pageTitle">Title</label>
                                                        <input type="text" id="pageTitle" name="title" value="<?php echo $escapeHtml($formData['title']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: about_details -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="aboutDetails">About Project – Detailed Description</label>
                                                        <textarea id="aboutDetails" name="about_details"><?php echo $escapeHtml($formData['about_details']); ?></textarea>
                                                    </div>
                                                </div>

                                                <!-- Field: about_developer -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="aboutDeveloper">About the Developer</label>
                                                        <textarea id="aboutDeveloper" name="about_developer"><?php echo $escapeHtml($formData['about_developer']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Payment Information -->
                                <div class="col-12 col-lg-6">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/payment-plan.png" alt="Info">
                                            <h3>Payment Plan</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">
                                            <div class="row">
                                                <!-- Field: starting_price -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="startingPrice">Starting Price</label>
                                                        <input type="text" id="startingPrice" name="starting_price" value="<?php echo $escapeHtml($formData['starting_price']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: payment_plan -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="paymentPlan">Payment Plan</label>
                                                        <input type="text" id="paymentPlan" name="payment_plan" value="<?php echo $escapeHtml($formData['payment_plan']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: handover_date -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="handoverDate">Handover Date</label>
                                                        <input type="date" id="handoverDate" name="handover_date" value="<?php echo $escapeHtml($formData['handover_date']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Area Information -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/area-information.png" alt="Info">
                                            <h3>Area Information</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Field: area_image -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="areaImage">Upload Area Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="areaImage" name="area_image" accept="image/*" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" alt="" width="30">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="areaName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Field: area_title -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="areaTitle">Area Title</label>
                                                        <input type="text" id="areaTitle" name="area_title" value="<?php echo $escapeHtml($formData['area_title']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: area_heading -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="areaHeading">Area Heading</label>
                                                        <input type="text" id="areaHeading" name="area_heading" value="<?php echo $escapeHtml($formData['area_heading']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: area_description -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="areaDescription">Area Description</label>
                                                        <textarea id="areaDescription" name="area_description"><?php echo $escapeHtml($formData['area_description']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </section>
                                </div>

                                <!-- Ameneties -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/amenities.png" alt="Info">
                                            <h3>Ameneties</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <!-- Field: amenities[] -->
                                            <div class="row amenity-tabs">
                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-swimming" name="amenities[]" value="swimming_pool" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'swimming_pool'); ?>>
                                                        <label for="am-swimming" class="amenity-btn">
                                                            <span class="amenity-text">Swimming Pool</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-gym" name="amenities[]" value="gymnasium" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'gymnasium'); ?>>
                                                        <label for="am-gym" class="amenity-btn">
                                                            <span class="amenity-text">Gymnasium</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-kids" name="amenities[]" value="kids_play_area" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'kids_play_area'); ?>>
                                                        <label for="am-kids" class="amenity-btn">
                                                            <span class="amenity-text">Kid’s Play Area</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-jog" name="amenities[]" value="jogging_area" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'jogging_area'); ?>>
                                                        <label for="am-jog" class="amenity-btn">
                                                            <span class="amenity-text">Jogging Area</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-garden" name="amenities[]" value="garden_zones" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'garden_zones'); ?>>
                                                        <label for="am-garden" class="amenity-btn">
                                                            <span class="amenity-text">Garden Zones</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-sports" name="amenities[]" value="sports_courts" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'sports_courts'); ?>>
                                                        <label for="am-sports" class="amenity-btn">
                                                            <span class="amenity-text">Sports Courts</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-sauna" name="amenities[]" value="sauna_steam_rooms" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'sauna_steam_rooms'); ?>>
                                                        <label for="am-sauna" class="amenity-btn">
                                                            <span class="amenity-text">Sauna & Steam Rooms</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-yoga" name="amenities[]" value="yoga_meditation_decks" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'yoga_meditation_decks'); ?>>
                                                        <label for="am-yoga" class="amenity-btn">
                                                            <span class="amenity-text">Yoga & Meditation Decks</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 col-lg-4">
                                                    <div class="amenity-tab">
                                                        <input type="checkbox" id="am-bbq" name="amenities[]" value="bbq_areas" class="amenity-input"<?php echo $amenityChecked($formData['amenities'], 'bbq_areas'); ?>>
                                                        <label for="am-bbq" class="amenity-btn">
                                                            <span class="amenity-text">BBQ Areas</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                    </section>
                                </div>

                                <!-- Project Information -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/information.png" alt="Info">
                                            <h3>Project Information</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Field: project_title_2 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="projectTitle2">Project Title 2</label>
                                                        <input type="text" id="projectTitle2" name="project_title_2" value="<?php echo $escapeHtml($formData['project_title_2']); ?>">
                                                    </div>
                                                </div>



                                                <!-- Field: project_title_3 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="projectTitle3">Project Title 3</label>
                                                        <input type="text" id="projectTitle3" name="project_title_3" value="<?php echo $escapeHtml($formData['project_title_3']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: price_from -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="priceFrom">Price From</label>
                                                        <input type="text" id="priceFrom" name="price_from" value="<?php echo $escapeHtml($formData['price_from']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: handover_date_3 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="handoverDate3">Hand Over Date</label>
                                                        <input type="date" id="handoverDate3" name="handover_date_3" value="<?php echo $escapeHtml($formData['handover_date_3']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: location_3 -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="location3">Location</label>
                                                        <input type="text" id="location3" name="location_3" value="<?php echo $escapeHtml($formData['location_3']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: development_time -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="developmentTime">Development Time</label>
                                                        <input type="text" id="developmentTime" name="development_time" value="<?php echo $escapeHtml($formData['development_time']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: Upload_area_image -->
                                                <div class="col-12 col-lg-12">
                                                    <div class="field">
                                                        <label for="projectImage2">Upload Project Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="projectImage2" name="project_image_2" accept="image/*" class="upload-input">
                                                            <div class="upload-box sm">
                                                                <div class="upload-stack">
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" alt="" width="30">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="proj2Name"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Field: project_description_2 -->
                                                <div class="col-12 col-lg-12">
                                                    <div class="field">
                                                        <label for="projectDescription2">Project Description 2</label>
                                                        <textarea id="projectDescription2" name="project_description_2"><?php echo $escapeHtml($formData['project_description_2']); ?></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Payment Plan -->
                                <div class="col-12 col-lg-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/payment-plan-method.png" alt="Info">
                                            <h3>Payment Plan</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Field: down_payment -->
                                                <div class="col-12 col-lg-4">
                                                    <div class="field">
                                                        <label for="downPayment">Down Payment</label>
                                                        <input type="text" id="downPayment" name="down_payment" value="<?php echo $escapeHtml($formData['down_payment']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: pre_handover -->
                                                <div class="col-12 col-lg-4">
                                                    <div class="field">
                                                        <label for="preHandover">Pre Handover</label>
                                                        <input type="text" id="preHandover" name="pre_handover" value="<?php echo $escapeHtml($formData['pre_handover']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: handover -->
                                                <div class="col-12 col-lg-4">
                                                    <div class="field">
                                                        <label for="handover">Handover</label>
                                                        <input type="text" id="handover" name="handover" value="<?php echo $escapeHtml($formData['handover']); ?>">
                                                    </div>
                                                </div>

                                                <!-- Field: transactions_image -->
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label for="transactionsImage">Upload Transactions Image</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="transactionsImage" name="transactions_image" accept="image/*" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <div>
                                                                        <img src="assets/icons/upload.png" width="30" alt="">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp</div>
                                                                    <div class="upload-name" id="txnName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Floor Plan -->
                                <div class="col-12">
                                    <section class="section">
                                        <div class="section-head">
                                            <img src="assets/icons/floor-plan.png" alt="Info">
                                            <h3>Floor Plan</h3>
                                        </div>
                                        <div class="section-sub">Primary property details and identification</div>

                                        <div class="section-body">

                                            <div class="row">
                                                <!-- Upload Property Images (multiple) -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="propertyImages">Upload Property Images</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="propertyImages" name="property_images[]" accept="image/*" multiple class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <!-- Arrow -->
                                                                    <div>
                                                                        <img width="30" src="assets/icons/upload.png" alt="">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop files here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg &amp; .webp — multiple allowed</div>
                                                                    <div class="upload-name" id="propertyImagesName"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Upload Floor Plan (image or PDF) -->
                                                <div class="col-12 col-lg-6">
                                                    <div class="field">
                                                        <label for="floorPlanFile1">Upload Floor Plan</label>
                                                        <div class="upload-field">
                                                            <input type="file" id="floorPlanFile1" name="floor_plan_file[]" multiple accept="image/*,application/pdf" class="upload-input">
                                                            <div class="upload-box">
                                                                <div class="upload-stack">
                                                                    <!-- Arrow -->
                                                                    <div>
                                                                        <img width="30" src="assets/icons/upload.png" alt="">
                                                                    </div>
                                                                    <div class="upload-btn">Browse</div>
                                                                    <div class="upload-hint">drop a file here</div>
                                                                    <div class="upload-note"><span class="req">*</span>File supported .png, .jpg, .webp &amp; .pdf - multiple allowed</div>
                                                                    <div class="upload-name" id="floorPlanFile1Name"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </div>
                            <!-- /row -->


                        </div>

                        <!-- Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn-hh outline">Reset</button>
                            <button type="submit" class="btn-hh">Submit Details</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/common-footer.php' ?>