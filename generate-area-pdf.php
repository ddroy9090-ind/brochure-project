<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

$areaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($areaId === false || $areaId === null) {
    http_response_code(400);
    echo '<!DOCTYPE html><html><body><h1>Invalid request</h1><p>An invalid area detail identifier was provided.</p></body></html>';
    exit;
}
$areaId = (int) $areaId;

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoloadPath)) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><body><h1>PDF generation unavailable</h1><p>The required PDF library is not installed. Please run <code>composer install</code> to install the project dependencies.</p></body></html>';
    exit;
}

require_once $autoloadPath;

use Dompdf\Dompdf;
use Dompdf\Options;

$detailSql = sprintf(
    "SELECT\n        id,\n        property_id,\n        registration_no,\n        property_name,\n        address,\n        project_name,\n        developer_name,\n        title,\n        about_details,\n        about_developer,\n        starting_price,\n        payment_plan,\n        handover_date,\n        area_title,\n        area_heading,\n        area_description,\n        amenities,\n        project_title_2,\n        project_title_3,\n        price_from,\n        handover_date_3,\n        location_3,\n        development_time,\n        project_description_2,\n        down_payment,\n        pre_handover,\n        handover,\n        created_at\n    FROM area_details\n    WHERE id = %d\n    LIMIT 1",
    $areaId
);

$detailResult = $conn->query($detailSql);
if ($detailResult === false) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><body><h1>Database error</h1><p>Unable to load the requested area details.</p></body></html>';
    exit;
}

$detailRow = $detailResult->fetch_assoc();
$detailResult->free();

if (!$detailRow) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><body><h1>Area details not found</h1><p>The requested property could not be located.</p></body></html>';
    exit;
}

$amenities = [];
$amenitiesRaw = trim((string) ($detailRow['amenities'] ?? ''));
if ($amenitiesRaw !== '') {
    $decodedAmenities = json_decode($amenitiesRaw, true);
    if (is_array($decodedAmenities)) {
        foreach ($decodedAmenities as $item) {
            if (!is_string($item)) {
                continue;
            }
            $value = trim($item);
            if ($value !== '') {
                $amenities[] = $value;
            }
        }
    }
}

$areaDetail = [
    'id' => $areaId,
    'property_id' => trim((string) ($detailRow['property_id'] ?? '')),
    'registration_no' => trim((string) ($detailRow['registration_no'] ?? '')),
    'property_name' => trim((string) ($detailRow['property_name'] ?? '')),
    'address' => trim((string) ($detailRow['address'] ?? '')),
    'project_name' => trim((string) ($detailRow['project_name'] ?? '')),
    'developer_name' => trim((string) ($detailRow['developer_name'] ?? '')),
    'title' => trim((string) ($detailRow['title'] ?? '')),
    'about_details' => trim((string) ($detailRow['about_details'] ?? '')),
    'about_developer' => trim((string) ($detailRow['about_developer'] ?? '')),
    'starting_price' => trim((string) ($detailRow['starting_price'] ?? '')),
    'payment_plan' => trim((string) ($detailRow['payment_plan'] ?? '')),
    'handover_date' => trim((string) ($detailRow['handover_date'] ?? '')),
    'area_title' => trim((string) ($detailRow['area_title'] ?? '')),
    'area_heading' => trim((string) ($detailRow['area_heading'] ?? '')),
    'area_description' => trim((string) ($detailRow['area_description'] ?? '')),
    'amenities' => $amenities,
    'project_title_2' => trim((string) ($detailRow['project_title_2'] ?? '')),
    'project_title_3' => trim((string) ($detailRow['project_title_3'] ?? '')),
    'price_from' => trim((string) ($detailRow['price_from'] ?? '')),
    'handover_date_3' => trim((string) ($detailRow['handover_date_3'] ?? '')),
    'location_3' => trim((string) ($detailRow['location_3'] ?? '')),
    'development_time' => trim((string) ($detailRow['development_time'] ?? '')),
    'project_description_2' => trim((string) ($detailRow['project_description_2'] ?? '')),
    'down_payment' => trim((string) ($detailRow['down_payment'] ?? '')),
    'pre_handover' => trim((string) ($detailRow['pre_handover'] ?? '')),
    'handover' => trim((string) ($detailRow['handover'] ?? '')),
    'created_at' => trim((string) ($detailRow['created_at'] ?? '')),
];

$fileSql = sprintf(
    "SELECT file_key, file_name, mime_type, file_size, file_data, created_at\n     FROM area_detail_files\n     WHERE area_detail_id = %d\n     ORDER BY created_at ASC, id ASC",
    $areaId
);

$fileResult = $conn->query($fileSql);
$filesByKey = [];
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
        $dataUri = 'data:' . ($mimeType !== '' ? $mimeType : 'application/octet-stream') . ';base64,' . base64_encode($fileData);
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
foreach ($areaDetail['amenities'] as $amenity) {
    $displayAmenities[] = $amenityLabels[$amenity] ?? ucwords(str_replace('_', ' ', $amenity));
}

$extractImages = static function (array $items): array {
    $images = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        if (!empty($item['is_image']) && isset($item['data_uri'])) {
            $images[] = $item;
        }
    }
    return $images;
};

$extractDocuments = static function (array $items): array {
    $documents = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        if (empty($item['is_image'])) {
            $documents[] = $item;
        }
    }
    return $documents;
};

$primaryImages = [
    'banner_image' => null,
    'area_image' => null,
    'project_image_2' => null,
    'transactions_image' => null,
];

foreach (array_keys($primaryImages) as $key) {
    if (!empty($filesByKey[$key])) {
        $images = $extractImages($filesByKey[$key]);
        if ($images) {
            $primaryImages[$key] = $images[0];
        }
    }
}

$propertyGallery = [];
if (!empty($filesByKey['property_images'])) {
    $propertyGallery = $extractImages($filesByKey['property_images']);
}

$floorPlanImages = [];
$floorPlanDocuments = [];
if (!empty($filesByKey['floor_plan_file'])) {
    $floorPlanImages = $extractImages($filesByKey['floor_plan_file']);
    $floorPlanDocuments = $extractDocuments($filesByKey['floor_plan_file']);
}

$formatDate = static function (?string $date): string {
    $date = trim((string) ($date ?? ''));
    if ($date === '' || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '';
    }

    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }

    return date('F j, Y', $timestamp);
};

$pdfTitle = $areaDetail['property_name'] !== '' ? $areaDetail['property_name'] : 'Area Detail';
$downloadName = preg_replace('/[^A-Za-z0-9\-]+/', '-', strtolower($pdfTitle));
$downloadName = trim($downloadName, '-');
if ($downloadName === '') {
    $downloadName = 'area-detail';
}
$downloadName .= '-brochure.pdf';

$generatedAt = new DateTimeImmutable('now');

ob_start();
include __DIR__ . '/pdfhtml/index.php';
$html = ob_get_clean();

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->setChroot(__DIR__);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream($downloadName, ['Attachment' => true]);
exit;
