<?php
/** @var array $areaDetail */
/** @var array $displayAmenities */
/** @var array $primaryImages */
/** @var array $propertyGallery */
/** @var array $floorPlanImages */
/** @var array $floorPlanDocuments */
/** @var callable $formatDate */
/** @var DateTimeImmutable $generatedAt */

$escape = static function (?string $value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$lineBreak = static function (?string $value) use ($escape): string {
    $value = trim((string) ($value ?? ''));
    if ($value === '') {
        return '';
    }

    return nl2br($escape($value));
};

$renderImage = static function (?array $image, string $class = '') use ($escape): string {
    if (!is_array($image) || empty($image['data_uri'])) {
        return '';
    }

    $classAttr = $class !== '' ? ' class="' . $escape($class) . '"' : '';
    $alt = isset($image['name']) && $image['name'] !== '' ? $escape($image['name']) : 'Image';

    return '<img' . $classAttr . ' src="' . $escape($image['data_uri']) . '" alt="' . $alt . '">';
};

$createdDate = $formatDate($areaDetail['created_at'] ?? '');
$handoverDate = $formatDate($areaDetail['handover_date'] ?? '');
$handoverDate3 = $formatDate($areaDetail['handover_date_3'] ?? '');

$pdfTitle = $areaDetail['property_name'] !== '' ? $areaDetail['property_name'] : 'Area Detail';
$subTitle = $areaDetail['project_name'] !== '' ? $areaDetail['project_name'] : $areaDetail['title'];
$headerImage = $primaryImages['banner_image'] ?? null;
$areaImage = $primaryImages['area_image'] ?? null;
$projectImage = $primaryImages['project_image_2'] ?? null;
$transactionsImage = $primaryImages['transactions_image'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $escape($pdfTitle); ?> &mdash; Property Brochure</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            color: #1f1f1f;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        .pdf-wrapper {
            padding: 24px;
            background: #fff;
        }
        h1, h2, h3 {
            margin: 0;
            color: #0f2d52;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
        }
        h2 {
            font-size: 16px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        h3 {
            font-size: 14px;
            margin-bottom: 4px;
        }
        .section {
            margin-top: 24px;
        }
        .section:first-of-type {
            margin-top: 0;
        }
        .section-header {
            border-bottom: 2px solid #0f2d52;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 24px;
        }
        .summary-item {
            flex: 1 1 45%;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6a6a6a;
            margin-bottom: 2px;
        }
        .summary-value {
            font-size: 13px;
            font-weight: 600;
        }
        .hero-image {
            width: 100%;
            height: 180px;
            border-radius: 10px;
            overflow: hidden;
            background: linear-gradient(120deg, #0f2d52, #174f84);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }
        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-text {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
        }
        .hero-meta {
            font-size: 11px;
            color: #555;
            text-align: right;
        }
        .two-column {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .two-column .col {
            flex: 1 1 48%;
        }
        .paragraph {
            line-height: 1.5;
            color: #2c2c2c;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table th, .info-table td {
            text-align: left;
            padding: 6px 0;
            vertical-align: top;
        }
        .info-table th {
            width: 140px;
            font-size: 11px;
            color: #6a6a6a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .info-table td {
            font-size: 12px;
            font-weight: 600;
        }
        .image-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .image-card img {
            display: block;
            width: 100%;
            height: auto;
        }
        .amenity-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .amenity-item {
            background: #e9f1fb;
            color: #0f2d52;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .gallery img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .floorplan-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .floorplan-grid img {
            width: 100%;
            height: 160px;
            object-fit: contain;
            border: 1px solid #d4dce7;
            border-radius: 8px;
            background: #fff;
        }
        .document-list {
            margin-top: 12px;
            padding-left: 18px;
        }
        .document-list li {
            margin-bottom: 4px;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #dbe3ef;
            font-size: 10px;
            color: #6a6a6a;
            display: flex;
            justify-content: space-between;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            background: #0f2d52;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
    </style>
</head>
<body>
<div class="pdf-wrapper">
    <div class="hero-image">
        <?php echo $renderImage($headerImage); ?>
    </div>
    <div class="hero-text">
        <div>
            <div class="badge">Property Brochure</div>
            <h1><?php echo $escape($pdfTitle); ?></h1>
            <?php if ($subTitle !== ''): ?>
                <h3><?php echo $escape($subTitle); ?></h3>
            <?php endif; ?>
        </div>
        <div class="hero-meta">
            <?php if ($createdDate !== ''): ?>
                <div>Created: <?php echo $escape($createdDate); ?></div>
            <?php endif; ?>
            <div>ID: <?php echo $escape($areaDetail['property_id']); ?></div>
            <?php if ($areaDetail['registration_no'] !== ''): ?>
                <div>Reg No.: <?php echo $escape($areaDetail['registration_no']); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2>Property Summary</h2>
        </div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Property Name</div>
                <div class="summary-value"><?php echo $escape($areaDetail['property_name']); ?></div>
            </div>
            <?php if ($areaDetail['address'] !== ''): ?>
                <div class="summary-item">
                    <div class="summary-label">Address</div>
                    <div class="summary-value"><?php echo $escape($areaDetail['address']); ?></div>
                </div>
            <?php endif; ?>
            <?php if ($areaDetail['developer_name'] !== ''): ?>
                <div class="summary-item">
                    <div class="summary-label">Developer</div>
                    <div class="summary-value"><?php echo $escape($areaDetail['developer_name']); ?></div>
                </div>
            <?php endif; ?>
            <?php if ($areaDetail['project_name'] !== ''): ?>
                <div class="summary-item">
                    <div class="summary-label">Project</div>
                    <div class="summary-value"><?php echo $escape($areaDetail['project_name']); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2>About the Project</h2>
        </div>
        <div class="two-column">
            <div class="col">
                <?php if ($areaDetail['title'] !== ''): ?>
                    <h3><?php echo $escape($areaDetail['title']); ?></h3>
                <?php endif; ?>
                <?php if ($areaDetail['about_details'] !== ''): ?>
                    <p class="paragraph"><?php echo $lineBreak($areaDetail['about_details']); ?></p>
                <?php endif; ?>
                <?php if ($areaDetail['about_developer'] !== ''): ?>
                    <h3>About the Developer</h3>
                    <p class="paragraph"><?php echo $lineBreak($areaDetail['about_developer']); ?></p>
                <?php endif; ?>
            </div>
            <div class="col">
                <table class="info-table">
                    <?php if ($areaDetail['starting_price'] !== ''): ?>
                        <tr>
                            <th>Starting Price</th>
                            <td><?php echo $escape($areaDetail['starting_price']); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($areaDetail['payment_plan'] !== ''): ?>
                        <tr>
                            <th>Payment Plan</th>
                            <td><?php echo $escape($areaDetail['payment_plan']); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($handoverDate !== ''): ?>
                        <tr>
                            <th>Handover Date</th>
                            <td><?php echo $escape($handoverDate); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <?php if ($projectImage !== null): ?>
                    <div class="image-card" style="margin-top:16px;">
                        <?php echo $renderImage($projectImage); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($areaImage !== null || $areaDetail['area_title'] !== '' || $areaDetail['area_description'] !== ''): ?>
        <div class="section">
            <div class="section-header">
                <h2>Area Highlights</h2>
            </div>
            <div class="two-column">
                <div class="col">
                    <?php if ($areaImage !== null): ?>
                        <div class="image-card">
                            <?php echo $renderImage($areaImage); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <?php if ($areaDetail['area_title'] !== ''): ?>
                        <h3><?php echo $escape($areaDetail['area_title']); ?></h3>
                    <?php endif; ?>
                    <?php if ($areaDetail['area_heading'] !== ''): ?>
                        <p class="paragraph" style="font-weight:600;"><?php echo $escape($areaDetail['area_heading']); ?></p>
                    <?php endif; ?>
                    <?php if ($areaDetail['area_description'] !== ''): ?>
                        <p class="paragraph"><?php echo $lineBreak($areaDetail['area_description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($displayAmenities): ?>
        <div class="section">
            <div class="section-header">
                <h2>Amenities</h2>
            </div>
            <div class="amenity-list">
                <?php foreach ($displayAmenities as $amenity): ?>
                    <span class="amenity-item"><?php echo $escape($amenity); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($areaDetail['project_title_2'] !== '' || $areaDetail['project_title_3'] !== '' || $areaDetail['price_from'] !== '' || $handoverDate3 !== '' || $areaDetail['location_3'] !== '' || $areaDetail['development_time'] !== '' || $areaDetail['project_description_2'] !== ''): ?>
        <div class="section">
            <div class="section-header">
                <h2>Project Details</h2>
            </div>
            <div class="two-column">
                <div class="col">
                    <table class="info-table">
                        <?php if ($areaDetail['project_title_2'] !== ''): ?>
                            <tr>
                                <th>Project Title 2</th>
                                <td><?php echo $escape($areaDetail['project_title_2']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($areaDetail['project_title_3'] !== ''): ?>
                            <tr>
                                <th>Project Title 3</th>
                                <td><?php echo $escape($areaDetail['project_title_3']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($areaDetail['price_from'] !== ''): ?>
                            <tr>
                                <th>Price From</th>
                                <td><?php echo $escape($areaDetail['price_from']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($handoverDate3 !== ''): ?>
                            <tr>
                                <th>Handover Date</th>
                                <td><?php echo $escape($handoverDate3); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($areaDetail['location_3'] !== ''): ?>
                            <tr>
                                <th>Location</th>
                                <td><?php echo $escape($areaDetail['location_3']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($areaDetail['development_time'] !== ''): ?>
                            <tr>
                                <th>Development Time</th>
                                <td><?php echo $escape($areaDetail['development_time']); ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col">
                    <?php if ($areaDetail['project_description_2'] !== ''): ?>
                        <p class="paragraph"><?php echo $lineBreak($areaDetail['project_description_2']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($areaDetail['down_payment'] !== '' || $areaDetail['pre_handover'] !== '' || $areaDetail['handover'] !== '' || $transactionsImage !== null): ?>
        <div class="section">
            <div class="section-header">
                <h2>Payment Plan</h2>
            </div>
            <div class="two-column">
                <div class="col">
                    <table class="info-table">
                        <?php if ($areaDetail['down_payment'] !== ''): ?>
                            <tr>
                                <th>Down Payment</th>
                                <td><?php echo $escape($areaDetail['down_payment']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($areaDetail['pre_handover'] !== ''): ?>
                            <tr>
                                <th>Pre-Handover</th>
                                <td><?php echo $escape($areaDetail['pre_handover']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($areaDetail['handover'] !== ''): ?>
                            <tr>
                                <th>Handover</th>
                                <td><?php echo $escape($areaDetail['handover']); ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col">
                    <?php if ($transactionsImage !== null): ?>
                        <div class="image-card">
                            <?php echo $renderImage($transactionsImage); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($propertyGallery): ?>
        <div class="section">
            <div class="section-header">
                <h2>Property Gallery</h2>
            </div>
            <div class="gallery">
                <?php foreach ($propertyGallery as $image): ?>
                    <?php echo $renderImage($image); ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($floorPlanImages || $floorPlanDocuments): ?>
        <div class="section">
            <div class="section-header">
                <h2>Floor Plans</h2>
            </div>
            <?php if ($floorPlanImages): ?>
                <div class="floorplan-grid">
                    <?php foreach ($floorPlanImages as $image): ?>
                        <?php echo $renderImage($image); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($floorPlanDocuments): ?>
                <ul class="document-list">
                    <?php foreach ($floorPlanDocuments as $doc): ?>
                        <li><?php echo $escape($doc['name'] !== '' ? $doc['name'] : ($doc['mime'] ?? 'Document')); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        <div>Generated on <?php echo $escape($generatedAt->format('F j, Y \a\t g:i A')); ?></div>
        <div>Property ID: <?php echo $escape($areaDetail['property_id']); ?></div>
    </div>
</div>
</body>
</html>
