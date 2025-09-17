<?php
$report = $report ?? [];
$amenities = $report['amenities'] ?? [];
$filesByKey = $filesByKey ?? [];
$displayValue = static function (?string $value, string $fallback = 'N/A'): string {
    $value = trim((string) ($value ?? ''));
    return $value === '' ? htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8') : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};
$hasValue = static function (?string $value): bool {
    return trim((string) ($value ?? '')) !== '';
};
$formatParagraph = static function (?string $value): string {
    $value = trim((string) ($value ?? ''));
    if ($value === '') {
        return '';
    }

    return nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), false);
};
$displayParagraph = static function (?string $value, string $fallback = 'N/A') use ($formatParagraph): string {
    $formatted = $formatParagraph($value);
    if ($formatted === '') {
        return '<span class="muted">' . htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    return $formatted;
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
            $fileDisplayMap[$key] = ucwords(str_replace(['_', '-'], ' ', (string) $key));
        }
    }
}

$mediaGroups = [];
foreach ($fileDisplayMap as $fileKey => $label) {
    $items = $filesByKey[$fileKey] ?? [];
    if (empty($items)) {
        continue;
    }

    $group = [
        'label' => $label,
        'images' => [],
        'files' => [],
    ];

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $isImage = !empty($item['is_image']);
        if ($isImage) {
            $group['images'][] = $item;
        } else {
            $group['files'][] = $item;
        }
    }

    if ($group['images'] || $group['files']) {
        $mediaGroups[] = $group;
    }
}

$hasMediaGroups = !empty($mediaGroups);

$coverImage = null;
if (!empty($filesByKey['banner_image'])) {
    foreach ($filesByKey['banner_image'] as $bannerFile) {
        if (!empty($bannerFile['is_image'])) {
            $coverImage = $bannerFile;
            break;
        }
    }
}

if ($coverImage === null && !empty($filesByKey['area_image'])) {
    foreach ($filesByKey['area_image'] as $areaFile) {
        if (!empty($areaFile['is_image'])) {
            $coverImage = $areaFile;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $displayValue($report['property_name'] ?? null, 'Area Detail Report') ?></title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: "Helvetica", "Arial", sans-serif;
            font-size: 12px;
            color: #1f2a36;
            margin: 0;
            padding: 0;
        }

        .cover {            
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f5c7c, #1d7c9b);
            color: #fff;
            text-align: center;
            padding: 60px 40px;
        }

        .cover-content {
            max-width: 600px;
        }

        .cover-label {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 11px;
            margin-bottom: 10px;
            display: inline-block;
            padding: 4px 10px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 999px;
        }

        .cover h1 {
            font-size: 30px;
            margin: 10px 0 15px;
            font-weight: 600;
        }

        .cover p {
            margin: 5px 0;
            font-size: 14px;
        }

        .cover-image {
            margin-top: 25px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .cover-image img {
            display: block;
            width: 100%;
            height: auto;
        }

        .page-break {
            page-break-before: always;
        }

        .page {
            padding: 40px 60px 60px;
        }

        .section-title {
            font-size: 18px;
            margin: 0 0 15px;
            color: #0f5c7c;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .info-table th,
        .info-table td {
            padding: 10px 12px;
            border: 1px solid #d9e1e7;
            text-align: left;
            vertical-align: top;
        }

        .info-table th {
            width: 35%;
            background: #f2f6f8;
            font-weight: 600;
            color: #0f5c7c;
        }

        .paragraph {
            margin-bottom: 18px;
            line-height: 1.6;
        }

        .paragraph:last-child {
            margin-bottom: 0;
        }

        .pill {
            display: inline-block;
            padding: 6px 12px;
            margin: 4px 6px 4px 0;
            border-radius: 999px;
            background: #e5f2f6;
            color: #0f5c7c;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .amenities {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .amenities li {
            margin: 6px 0;
            padding-left: 16px;
            position: relative;
        }

        .amenities li::before {
            content: "•";
            color: #0f5c7c;
            position: absolute;
            left: 0;
        }

        .muted {
            color: #6c7a89;
        }

        .two-column {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .two-column .column {
            flex: 1 1 250px;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #6c7a89;
            text-align: right;
        }

        .media-section {
            margin-top: 10px;
        }

        .media-group {
            margin-bottom: 25px;
        }

        .media-group h3 {
            font-size: 15px;
            margin: 0 0 10px;
            color: #0f5c7c;
        }

        .media-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .media-item {
            width: calc(50% - 8px);
            border: 1px solid #d9e1e7;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            page-break-inside: avoid;
        }

        .media-item img {
            display: block;
            width: 100%;
            height: auto;
        }

        .media-item figcaption {
            padding: 8px 10px;
            font-size: 11px;
            color: #1f2a36;
        }

        .file-list {
            margin: 0;
            padding-left: 18px;
        }

        .file-list li {
            margin: 4px 0;
            font-size: 12px;
        }

        .file-list strong {
            color: #0f5c7c;
        }
    </style>
</head>

<body>
    <div class="cover">
        <div class="cover-content">
            <span class="cover-label">Area Detail Report</span>
            <h1><?= $displayValue($report['property_name'] ?? null, 'Area Detail') ?></h1>
            <?php if ($hasValue($report['address'] ?? null)): ?>
                <p><?= $displayParagraph($report['address'] ?? null) ?></p>
            <?php endif; ?>
            <?php if ($hasValue($report['developer_name'] ?? null)): ?>
                <p>Developed by <?= $displayValue($report['developer_name'] ?? null) ?></p>
            <?php endif; ?>
            <?php if ($hasValue($report['project_name'] ?? null)): ?>
                <p class="muted">Project: <?= $displayValue($report['project_name'] ?? null) ?></p>
            <?php endif; ?>
            <p class="muted">Generated on <?= $displayValue($report['generated_at'] ?? null, date('F j, Y g:i A')) ?></p>
            <?php if ($coverImage !== null && !empty($coverImage['data_uri'])): ?>
                <div class="cover-image">
                    <img src="<?= htmlspecialchars($coverImage['data_uri'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($coverImage['name'] !== '' ? $coverImage['name'] : 'Cover Image', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="page">
        <h2 class="section-title">Property Summary</h2>
        <table class="info-table">
            <tr>
                <th>Property Name</th>
                <td><?= $displayValue($report['property_name'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?= $displayParagraph($report['address'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Property ID</th>
                <td><?= $displayValue($report['property_id'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Registration Number</th>
                <td><?= $displayValue($report['registration_no'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Project Name</th>
                <td><?= $displayValue($report['project_name'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Developer</th>
                <td><?= $displayValue($report['developer_name'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Primary Title</th>
                <td><?= $displayValue($report['title'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Created On</th>
                <td><?= $displayValue($report['created_at'] ?? null) ?></td>
            </tr>
            <tr>
                <th>Expected Handover</th>
                <td><?= $displayValue($report['handover_date'] ?? null) ?></td>
            </tr>
        </table>

        <?php if ($hasValue($report['about_details'] ?? null) || $hasValue($report['about_developer'] ?? null)): ?>
            <h2 class="section-title">Project Narrative</h2>
            <?php if ($hasValue($report['about_details'] ?? null)): ?>
                <div class="paragraph"><?= $formatParagraph($report['about_details'] ?? null) ?></div>
            <?php endif; ?>
            <?php if ($hasValue($report['about_developer'] ?? null)): ?>
                <div class="paragraph"><strong>Developer insight:</strong><br><?= $formatParagraph($report['about_developer'] ?? null) ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($hasValue($report['area_title'] ?? null) || $hasValue($report['area_heading'] ?? null) || $hasValue($report['area_description'] ?? null)): ?>
            <h2 class="section-title">Area Highlights</h2>
            <?php if ($hasValue($report['area_title'] ?? null)): ?>
                <h3><?= $displayValue($report['area_title'] ?? null) ?></h3>
            <?php endif; ?>
            <?php if ($hasValue($report['area_heading'] ?? null)): ?>
                <p class="paragraph muted" style="margin-top: -8px;"><?= $displayValue($report['area_heading'] ?? null) ?></p>
            <?php endif; ?>
            <?php if ($hasValue($report['area_description'] ?? null)): ?>
                <div class="paragraph"><?= $formatParagraph($report['area_description'] ?? null) ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <h2 class="section-title">Amenities</h2>
        <?php if ($amenities): ?>
            <ul class="amenities">
                <?php foreach ($amenities as $amenity): ?>
                    <li><?= htmlspecialchars($amenity, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">Amenities have not been specified for this property.</p>
        <?php endif; ?>

        <h2 class="section-title">Payment &amp; Pricing</h2>
        <div class="two-column">
            <div class="column">
                <table class="info-table">
                    <tr>
                        <th>Starting Price</th>
                        <td><?= $displayValue($report['starting_price'] ?? null) ?></td>
                    </tr>
                    <tr>
                        <th>Price From</th>
                        <td><?= $displayValue($report['price_from'] ?? null) ?></td>
                    </tr>
                    <tr>
                        <th>Payment Plan</th>
                        <td><?= $displayParagraph($report['payment_plan'] ?? null) ?></td>
                    </tr>
                </table>
            </div>
            <div class="column">
                <table class="info-table">
                    <tr>
                        <th>Down Payment</th>
                        <td><?= $displayParagraph($report['down_payment'] ?? null) ?></td>
                    </tr>
                    <tr>
                        <th>Pre-handover</th>
                        <td><?= $displayParagraph($report['pre_handover'] ?? null) ?></td>
                    </tr>
                    <tr>
                        <th>On Handover</th>
                        <td><?= $displayParagraph($report['handover'] ?? null) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if ($hasValue($report['project_title_2'] ?? null) || $hasValue($report['project_title_3'] ?? null) || $hasValue($report['project_description_2'] ?? null) || $hasValue($report['handover_date_3'] ?? null) || $hasValue($report['location_3'] ?? null) || $hasValue($report['development_time'] ?? null)): ?>
            <h2 class="section-title">Additional Information</h2>
            <table class="info-table">
                <tr>
                    <th>Secondary Title</th>
                    <td><?= $displayValue($report['project_title_2'] ?? null) ?></td>
                </tr>
                <tr>
                    <th>Highlight</th>
                    <td><?= $displayValue($report['project_title_3'] ?? null) ?></td>
                </tr>
                <tr>
                    <th>Development Timeline</th>
                    <td><?= $displayValue($report['development_time'] ?? null) ?></td>
                </tr>
                <tr>
                    <th>Projected Handover</th>
                    <td><?= $displayValue($report['handover_date_3'] ?? null) ?></td>
                </tr>
                <tr>
                    <th>Location Detail</th>
                    <td><?= $displayValue($report['location_3'] ?? null) ?></td>
                </tr>
                <tr>
                    <th>Project Narrative</th>
                    <td><?= $displayParagraph($report['project_description_2'] ?? null) ?></td>
                </tr>
            </table>
        <?php endif; ?>

        <?php if ($hasMediaGroups): ?>
            <div class="page-break"></div>
            <div class="page media-section">
                <h2 class="section-title">Media &amp; Attachments</h2>
                <?php foreach ($mediaGroups as $group): ?>
                    <div class="media-group">
                        <h3><?= htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php if (!empty($group['images'])): ?>
                            <div class="media-gallery">
                                <?php foreach ($group['images'] as $index => $image): ?>
                                    <figure class="media-item">
                                        <img src="<?= htmlspecialchars($image['data_uri'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars(($image['name'] ?? '') !== '' ? $image['name'] : ($group['label'] . ' ' . ($index + 1)), ENT_QUOTES, 'UTF-8') ?>">
                                        <figcaption>
                                            <?php if (!empty($image['name'])): ?>
                                                <strong><?= htmlspecialchars($image['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <?php else: ?>
                                                <strong><?= htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <?php endif; ?>
                                            <?php $sizeLabel = $formatFileSize(isset($image['size']) ? (int) $image['size'] : null); ?>
                                            <?php if ($sizeLabel !== ''): ?>
                                                <br><span class="muted">Size: <?= htmlspecialchars($sizeLabel, ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($image['created_at'])): ?>
                                                <br><span class="muted">Uploaded: <?= htmlspecialchars($image['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php endif; ?>
                                        </figcaption>
                                    </figure>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($group['files'])): ?>
                            <ul class="file-list">
                                <?php foreach ($group['files'] as $file): ?>
                                    <li>
                                        <strong><?= htmlspecialchars(!empty($file['name']) ? $file['name'] : $group['label'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <?php $sizeLabel = $formatFileSize(isset($file['size']) ? (int) $file['size'] : null); ?>
                                        <?php if ($sizeLabel !== ''): ?>
                                            <span class="muted">(<?= htmlspecialchars($sizeLabel, ENT_QUOTES, 'UTF-8') ?>)</span>
                                        <?php endif; ?>
                                        <?php if (!empty($file['mime'])): ?>
                                            <br><span class="muted">Type: <?= htmlspecialchars($file['mime'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($file['created_at'])): ?>
                                            <br><span class="muted">Uploaded: <?= htmlspecialchars($file['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="footer">
            Generated on <?= $displayValue($report['generated_at'] ?? null, date('F j, Y g:i A')) ?>
        </div>
    </div>
</body>

</html>
