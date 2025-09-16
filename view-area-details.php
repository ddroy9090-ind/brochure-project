<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Property Details';

$escapeHtml = static function (?string $value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$formatDate = static function (?string $value): string {
    $value = trim((string) ($value ?? ''));
    if ($value === '') {
        return '';
    }

    if ($value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return '';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    return date('F j, Y', $timestamp);
};

$formatFileSize = static function (?int $bytes): string {
    if ($bytes === null || $bytes <= 0) {
        return '';
    }

    $size = (float) $bytes;
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $index = 0;

    while ($size >= 1024 && $index < count($units) - 1) {
        $size /= 1024;
        $index++;
    }

    $precision = ($size >= 10 || $index === 0) ? 0 : 1;

    return number_format($size, $precision) . ' ' . $units[$index];
};

$areaDetail = null;
$filesByKey = [];
$loadError = null;

$requestedId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($requestedId === false || $requestedId === null) {
    $loadError = 'Invalid property specified.';
} else {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'area_details'");
    if ($tableCheck === false) {
        $loadError = 'Unable to load property details.';
    } else {
        $tableExists = $tableCheck->num_rows > 0;
        $tableCheck->free();

        if (!$tableExists) {
            $loadError = 'No property details available yet.';
        } else {
            $requestedId = (int) $requestedId;

            $detailSql = <<<'SQL'
                SELECT
                    id,
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
                    handover,
                    created_at
                FROM area_details
                WHERE id = %d
                LIMIT 1
                SQL;

            $detailSql = sprintf($detailSql, $requestedId);
            $detailResult = $conn->query($detailSql);

            if ($detailResult === false) {
                $loadError = 'Unable to load property details.';
            } else {
                $row = $detailResult->fetch_assoc();
                $detailResult->free();

                if (!$row) {
                    $loadError = 'Property details not found.';
                } else {
                    $amenities = [];
                    $amenitiesRaw = trim((string) ($row['amenities'] ?? ''));
                    if ($amenitiesRaw !== '') {
                        $decoded = json_decode($amenitiesRaw, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $amenity) {
                                if (!is_string($amenity)) {
                                    continue;
                                }
                                $amenityValue = trim($amenity);
                                if ($amenityValue !== '') {
                                    $amenities[] = $amenityValue;
                                }
                            }
                        }
                    }

                    $areaDetail = [
                        'id' => $requestedId,
                        'property_id' => trim((string) ($row['property_id'] ?? '')),
                        'registration_no' => trim((string) ($row['registration_no'] ?? '')),
                        'property_name' => trim((string) ($row['property_name'] ?? '')),
                        'address' => trim((string) ($row['address'] ?? '')),
                        'project_name' => trim((string) ($row['project_name'] ?? '')),
                        'developer_name' => trim((string) ($row['developer_name'] ?? '')),
                        'title' => trim((string) ($row['title'] ?? '')),
                        'about_details' => trim((string) ($row['about_details'] ?? '')),
                        'about_developer' => trim((string) ($row['about_developer'] ?? '')),
                        'starting_price' => trim((string) ($row['starting_price'] ?? '')),
                        'payment_plan' => trim((string) ($row['payment_plan'] ?? '')),
                        'handover_date' => trim((string) ($row['handover_date'] ?? '')),
                        'area_title' => trim((string) ($row['area_title'] ?? '')),
                        'area_heading' => trim((string) ($row['area_heading'] ?? '')),
                        'area_description' => trim((string) ($row['area_description'] ?? '')),
                        'amenities' => $amenities,
                        'project_title_2' => trim((string) ($row['project_title_2'] ?? '')),
                        'project_title_3' => trim((string) ($row['project_title_3'] ?? '')),
                        'price_from' => trim((string) ($row['price_from'] ?? '')),
                        'handover_date_3' => trim((string) ($row['handover_date_3'] ?? '')),
                        'location_3' => trim((string) ($row['location_3'] ?? '')),
                        'development_time' => trim((string) ($row['development_time'] ?? '')),
                        'project_description_2' => trim((string) ($row['project_description_2'] ?? '')),
                        'down_payment' => trim((string) ($row['down_payment'] ?? '')),
                        'pre_handover' => trim((string) ($row['pre_handover'] ?? '')),
                        'handover' => trim((string) ($row['handover'] ?? '')),
                        'created_at' => trim((string) ($row['created_at'] ?? '')),
                    ];
                }
            }
        }
    }
}

if ($areaDetail !== null) {
    $fileTableCheck = $conn->query("SHOW TABLES LIKE 'area_detail_files'");
    if ($fileTableCheck !== false) {
        $fileTableExists = $fileTableCheck->num_rows > 0;
        $fileTableCheck->free();

        if ($fileTableExists) {
            $fileSql = sprintf(
                "SELECT file_key, file_name, mime_type, file_size, file_data, created_at\n                 FROM area_detail_files\n                 WHERE area_detail_id = %d\n                 ORDER BY created_at ASC, id ASC",
                (int) $areaDetail['id']
            );

            $fileResult = $conn->query($fileSql);
            if ($fileResult !== false) {
                while ($fileRow = $fileResult->fetch_assoc()) {
                    $fileKey = trim((string) ($fileRow['file_key'] ?? ''));
                    if ($fileKey === '') {
                        continue;
                    }

                    if (!array_key_exists('file_data', $fileRow)) {
                        continue;
                    }

                    $fileData = $fileRow['file_data'];
                    if (!is_string($fileData) || $fileData === '') {
                        continue;
                    }

                    $mimeType = trim((string) ($fileRow['mime_type'] ?? ''));
                    $base64 = base64_encode($fileData);
                    $dataUri = 'data:' . ($mimeType !== '' ? $mimeType : 'application/octet-stream') . ';base64,' . $base64;
                    $isImage = $mimeType !== '' && strpos($mimeType, 'image/') === 0;

                    $filesByKey[$fileKey][] = [
                        'name' => trim((string) ($fileRow['file_name'] ?? '')),
                        'mime' => $mimeType,
                        'size' => isset($fileRow['file_size']) ? (int) $fileRow['file_size'] : null,
                        'data_uri' => $dataUri,
                        'is_image' => $isImage,
                        'created_at' => trim((string) ($fileRow['created_at'] ?? '')),
                    ];
                }
                $fileResult->free();
            }
        }
    }
}

$amenityLabels = [
    'swimming_pool' => 'Swimming Pool',
    'gymnasium' => 'Gymnasium',
    'kids_play_area' => "Kid's Play Area",
    'jogging_area' => 'Jogging Area',
    'garden_zones' => 'Garden Zones',
    'sports_courts' => 'Sports Courts',
    'sauna_steam_rooms' => 'Sauna & Steam Rooms',
    'yoga_meditation_decks' => 'Yoga & Meditation Decks',
    'bbq_areas' => 'BBQ Areas',
];

$displayAmenities = [];
if ($areaDetail !== null) {
    foreach ($areaDetail['amenities'] as $amenity) {
        $displayAmenities[] = $amenityLabels[$amenity] ?? ucwords(str_replace('_', ' ', $amenity));
    }
}

$fileDisplayMap = [
    'banner_image' => 'Banner Image',
    'area_image' => 'Area Image',
    'project_image_2' => 'Project Image',
    'transactions_image' => 'Transactions Image',
    'property_images' => 'Property Gallery',
    'floor_plan_file' => 'Floor Plans',
];

if ($filesByKey) {
    foreach ($filesByKey as $key => $_) {
        if (!array_key_exists($key, $fileDisplayMap)) {
            $fileDisplayMap[$key] = ucwords(str_replace(['_', '-'], ' ', $key));
        }
    }
}

$hasAnyValue = static function (?array $data, array $keys): bool {
    if (!is_array($data)) {
        return false;
    }

    foreach ($keys as $key) {
        if (!array_key_exists($key, $data)) {
            continue;
        }

        $value = $data[$key];
        if (is_array($value)) {
            if (!empty($value)) {
                return true;
            }
            continue;
        }

        if (trim((string) $value) !== '') {
            return true;
        }
    }

    return false;
};

$renderField = static function (string $label, ?string $value, array $options = []) use ($escapeHtml) {
    $value = trim((string) ($value ?? ''));
    if ($value === '') {
        return false;
    }

    $colClass = $options['col'] ?? 'col-12 col-md-6 col-xl-4';
    $multiline = (bool) ($options['multiline'] ?? false);
    $isHtml = (bool) ($options['isHtml'] ?? false);

    if ($multiline) {
        $valueHtml = nl2br($escapeHtml($value));
    } elseif ($isHtml) {
        $valueHtml = $value;
    } else {
        $valueHtml = $escapeHtml($value);
    }

    echo '<div class="' . htmlspecialchars($colClass, ENT_QUOTES, 'UTF-8') . '">';
    echo '    <div class="detail-field">';
    echo '        <div class="detail-label">' . $escapeHtml($label) . '</div>';
    echo '        <div class="detail-value">' . $valueHtml . '</div>';
    echo '    </div>';
    echo '</div>';

    return true;
};

require_once __DIR__ . '/includes/common-header.php';
?>

<div class="container-fluid cms-layout">
    <div class="row h-100">
        <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

        <div class="col content" id="content">
            <?php require_once __DIR__ . '/includes/topbar.php'; ?>

            <div class="p-2">
                <div class="property-view">
                    <a class="property-view__back" href="document-library.php">&larr; Back to Document Library</a>

                    <?php if ($loadError): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $escapeHtml($loadError); ?>
                        </div>
                    <?php elseif ($areaDetail === null): ?>
                        <div class="alert alert-warning" role="alert">
                            Property details are not available.
                        </div>
                    <?php else: ?>
                        <?php
                        $propertyTitle = $areaDetail['property_name'] !== '' ? $areaDetail['property_name'] : 'Untitled Property';
                        $createdDisplay = $formatDate($areaDetail['created_at']);
                        ?>

                        <div class="detail-section detail-section--intro">
                            <div class="detail-section__header">
                                <h1 class="property-view__title"><?php echo $escapeHtml($propertyTitle); ?></h1>
                                <div class="property-view__actions">
                                    <a class="btn btn-outline-primary btn-sm" href="area-details.php?id=<?php echo $escapeHtml((string) $areaDetail['id']); ?>">Edit Property</a>
                                </div>
                            </div>
                            <div class="property-view__meta">
                                <?php if ($areaDetail['property_id'] !== ''): ?>
                                    <span><strong>ID:</strong> <?php echo $escapeHtml($areaDetail['property_id']); ?></span>
                                <?php endif; ?>
                                <?php if ($areaDetail['registration_no'] !== ''): ?>
                                    <span><strong>Registration:</strong> <?php echo $escapeHtml($areaDetail['registration_no']); ?></span>
                                <?php endif; ?>
                                <?php if ($createdDisplay !== ''): ?>
                                    <span><strong>Created:</strong> <?php echo $escapeHtml($createdDisplay); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($areaDetail['address'] !== ''): ?>
                                <div class="property-view__address">
                                    <?php echo nl2br($escapeHtml($areaDetail['address'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($hasAnyValue($areaDetail, ['property_id', 'registration_no', 'project_name', 'developer_name'])): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>Project Information</h2>
                                    <p class="detail-section__subtitle">Key identifiers and ownership details for this property.</p>
                                </div>
                                <div class="row g-3 detail-grid">
                                    <?php
                                    $renderField('Property ID', $areaDetail['property_id']);
                                    $renderField('Registration Number', $areaDetail['registration_no']);
                                    $renderField('Project Name', $areaDetail['project_name']);
                                    $renderField('Developer Name', $areaDetail['developer_name']);
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasAnyValue($areaDetail, ['title', 'about_details', 'about_developer'])): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>About the Project</h2>
                                    <p class="detail-section__subtitle">Narrative content shared for marketing collateral.</p>
                                </div>
                                <div class="row g-3 detail-grid">
                                    <?php
                                    $renderField('Page Title', $areaDetail['title']);
                                    $renderField('About the Project', $areaDetail['about_details'], ['col' => 'col-12', 'multiline' => true]);
                                    $renderField('About the Developer', $areaDetail['about_developer'], ['col' => 'col-12', 'multiline' => true]);
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasAnyValue($areaDetail, ['starting_price', 'payment_plan', 'handover_date'])): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>Financial Overview</h2>
                                    <p class="detail-section__subtitle">Pricing information and key transaction milestones.</p>
                                </div>
                                <div class="row g-3 detail-grid">
                                    <?php
                                    $renderField('Starting Price', $areaDetail['starting_price']);
                                    $renderField('Payment Plan', $areaDetail['payment_plan']);
                                    $renderField('Handover Date', $formatDate($areaDetail['handover_date']));
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasAnyValue($areaDetail, ['area_title', 'area_heading', 'area_description'])): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>Area Information</h2>
                                    <p class="detail-section__subtitle">Context about the community and surrounding amenities.</p>
                                </div>
                                <div class="row g-3 detail-grid">
                                    <?php
                                    $renderField('Area Title', $areaDetail['area_title']);
                                    $renderField('Area Heading', $areaDetail['area_heading']);
                                    $renderField('Area Description', $areaDetail['area_description'], ['col' => 'col-12', 'multiline' => true]);
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($displayAmenities)): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>Amenities</h2>
                                    <p class="detail-section__subtitle">Facilities and features highlighted for prospects.</p>
                                </div>
                                <div class="detail-amenities">
                                    <?php foreach ($displayAmenities as $amenity): ?>
                                        <span class="detail-amenities__item"><?php echo $escapeHtml($amenity); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasAnyValue($areaDetail, ['project_title_2', 'project_title_3', 'price_from', 'handover_date_3', 'location_3', 'development_time', 'project_description_2'])): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>Project Highlights</h2>
                                    <p class="detail-section__subtitle">Additional narrative and timeline details.</p>
                                </div>
                                <div class="row g-3 detail-grid">
                                    <?php
                                    $renderField('Project Title 2', $areaDetail['project_title_2']);
                                    $renderField('Project Title 3', $areaDetail['project_title_3']);
                                    $renderField('Price From', $areaDetail['price_from']);
                                    $renderField('Hand Over Date', $formatDate($areaDetail['handover_date_3']));
                                    $renderField('Location', $areaDetail['location_3']);
                                    $renderField('Development Time', $areaDetail['development_time']);
                                    $renderField('Project Description', $areaDetail['project_description_2'], ['col' => 'col-12', 'multiline' => true]);
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasAnyValue($areaDetail, ['down_payment', 'pre_handover', 'handover'])): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>Payment Plan</h2>
                                    <p class="detail-section__subtitle">Detailed installment breakdown for the brochure.</p>
                                </div>
                                <div class="row g-3 detail-grid">
                                    <?php
                                    $renderField('Down Payment', $areaDetail['down_payment']);
                                    $renderField('Pre Handover', $areaDetail['pre_handover']);
                                    $renderField('Handover', $areaDetail['handover']);
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
                        $hasMedia = false;
                        foreach ($fileDisplayMap as $key => $_) {
                            if (!empty($filesByKey[$key])) {
                                $hasMedia = true;
                                break;
                            }
                        }
                        ?>

                        <?php if ($hasMedia): ?>
                            <div class="detail-section">
                                <div class="detail-section__header">
                                    <h2>Media &amp; Files</h2>
                                    <p class="detail-section__subtitle">All uploaded visuals and documents for this property.</p>
                                </div>
                                <?php foreach ($fileDisplayMap as $key => $label): ?>
                                    <?php if (empty($filesByKey[$key])) { continue; } ?>
                                    <div class="detail-media-group">
                                        <h3 class="detail-media-group__title"><?php echo $escapeHtml($label); ?></h3>
                                        <div class="detail-media-items">
                                            <?php foreach ($filesByKey[$key] as $file): ?>
                                                <?php if ($file['is_image']): ?>
                                                    <figure class="detail-media-card">
                                                        <img src="<?php echo htmlspecialchars($file['data_uri'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $escapeHtml($file['name'] !== '' ? $file['name'] : $label); ?>">
                                                        <figcaption>
                                                            <?php if ($file['name'] !== ''): ?>
                                                                <span class="detail-media-name"><?php echo $escapeHtml($file['name']); ?></span>
                                                            <?php else: ?>
                                                                <span class="detail-media-name"><?php echo $escapeHtml($label); ?></span>
                                                            <?php endif; ?>
                                                            <?php
                                                            $fileSizeLabel = $formatFileSize($file['size']);
                                                            if ($fileSizeLabel !== '') {
                                                                echo '<span class="detail-media-size">(' . $escapeHtml($fileSizeLabel) . ')</span>';
                                                            }
                                                            ?>
                                                        </figcaption>
                                                    </figure>
                                                <?php else: ?>
                                                    <div class="detail-media-card detail-media-card--file">
                                                        <div class="detail-media-name">
                                                            <?php echo $escapeHtml($file['name'] !== '' ? $file['name'] : $label); ?>
                                                            <?php
                                                            $fileSizeLabel = $formatFileSize($file['size']);
                                                            if ($fileSizeLabel !== '') {
                                                                echo '<span class="detail-media-size">(' . $escapeHtml($fileSizeLabel) . ')</span>';
                                                            }
                                                            ?>
                                                        </div>
                                                        <a class="detail-media-download" href="<?php echo htmlspecialchars($file['data_uri'], ENT_QUOTES, 'UTF-8'); ?>" download="<?php echo $escapeHtml($file['name'] !== '' ? $file['name'] : ($label . '.file')); ?>">Download</a>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/common-footer.php'; ?>
