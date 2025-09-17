<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/db.php';

$areaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$respondWithError = static function (int $status, string $message): void {
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $message;
    exit;
};

if ($areaId === false || $areaId === null) {
    $respondWithError(400, 'Invalid area detail specified.');
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoloadPath)) {
    $respondWithError(500, 'The PDF generator is not installed. Please run "composer install" to install dependencies.');
}

require_once $autoloadPath;

use Dompdf\Dompdf;
use Dompdf\Options;

$tableCheck = $conn->query("SHOW TABLES LIKE 'area_details'");
if ($tableCheck === false) {
    $respondWithError(500, 'Unable to verify area details table.');
}

$tableExists = $tableCheck->num_rows > 0;
$tableCheck->free();

if (!$tableExists) {
    $respondWithError(404, 'Area details are not available yet.');
}

$query = <<<'SQL'
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
    WHERE id = ?
    LIMIT 1
    SQL;

$stmt = $conn->prepare($query);
if (!$stmt) {
    $respondWithError(500, 'Unable to prepare area detail query.');
}

if (!$stmt->bind_param('i', $areaId)) {
    $stmt->close();
    $respondWithError(500, 'Unable to bind area detail query parameter.');
}

if (!$stmt->execute()) {
    $stmt->close();
    $respondWithError(500, 'Unable to execute area detail query.');
}

$result = $stmt->get_result();
if ($result === false) {
    $stmt->close();
    $respondWithError(500, 'Unable to fetch area detail.');
}

$row = $result->fetch_assoc();
$result->free();
$stmt->close();

if (!$row) {
    $respondWithError(404, 'Area detail not found.');
}

$trimValue = static function ($value): string {
    return trim((string) ($value ?? ''));
};

$formatDate = static function ($value) use ($trimValue): string {
    $value = $trimValue($value ?? '');
    if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return '';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    return date('F j, Y', $timestamp);
};

$amenities = [];
$amenitiesRaw = $trimValue($row['amenities'] ?? '');
if ($amenitiesRaw !== '') {
    $decoded = json_decode($amenitiesRaw, true);
    if (is_array($decoded)) {
        foreach ($decoded as $amenity) {
            if (!is_string($amenity)) {
                continue;
            }
            $amenityValue = $trimValue($amenity);
            if ($amenityValue !== '') {
                $amenities[] = $amenityValue;
            }
        }
    }
}

$report = [
    'property_id' => $trimValue($row['property_id'] ?? ''),
    'registration_no' => $trimValue($row['registration_no'] ?? ''),
    'property_name' => $trimValue($row['property_name'] ?? ''),
    'address' => $trimValue($row['address'] ?? ''),
    'project_name' => $trimValue($row['project_name'] ?? ''),
    'developer_name' => $trimValue($row['developer_name'] ?? ''),
    'title' => $trimValue($row['title'] ?? ''),
    'about_details' => $trimValue($row['about_details'] ?? ''),
    'about_developer' => $trimValue($row['about_developer'] ?? ''),
    'starting_price' => $trimValue($row['starting_price'] ?? ''),
    'payment_plan' => $trimValue($row['payment_plan'] ?? ''),
    'handover_date' => $formatDate($row['handover_date'] ?? ''),
    'area_title' => $trimValue($row['area_title'] ?? ''),
    'area_heading' => $trimValue($row['area_heading'] ?? ''),
    'area_description' => $trimValue($row['area_description'] ?? ''),
    'amenities' => $amenities,
    'project_title_2' => $trimValue($row['project_title_2'] ?? ''),
    'project_title_3' => $trimValue($row['project_title_3'] ?? ''),
    'price_from' => $trimValue($row['price_from'] ?? ''),
    'handover_date_3' => $formatDate($row['handover_date_3'] ?? ''),
    'location_3' => $trimValue($row['location_3'] ?? ''),
    'development_time' => $trimValue($row['development_time'] ?? ''),
    'project_description_2' => $trimValue($row['project_description_2'] ?? ''),
    'down_payment' => $trimValue($row['down_payment'] ?? ''),
    'pre_handover' => $trimValue($row['pre_handover'] ?? ''),
    'handover' => $trimValue($row['handover'] ?? ''),
    'created_at' => $formatDate($row['created_at'] ?? ''),
    'generated_at' => date('F j, Y g:i A'),
];

$filesByKey = [];

$fileTableCheck = $conn->query("SHOW TABLES LIKE 'area_detail_files'");
if ($fileTableCheck === false) {
    $respondWithError(500, 'Unable to verify area detail files table.');
}

$fileTableExists = $fileTableCheck->num_rows > 0;
$fileTableCheck->free();

if ($fileTableExists) {
    $fileQuery = <<<'SQL'
        SELECT
            file_key,
            file_name,
            mime_type,
            file_size,
            file_data,
            created_at
        FROM area_detail_files
        WHERE area_detail_id = ?
        ORDER BY created_at ASC, id ASC
        SQL;

    $fileStmt = $conn->prepare($fileQuery);
    if (!$fileStmt) {
        $respondWithError(500, 'Unable to prepare area detail files query.');
    }

    if (!$fileStmt->bind_param('i', $areaId)) {
        $fileStmt->close();
        $respondWithError(500, 'Unable to bind area detail files query parameter.');
    }

    if (!$fileStmt->execute()) {
        $fileStmt->close();
        $respondWithError(500, 'Unable to execute area detail files query.');
    }

    $fileResult = $fileStmt->get_result();
    if ($fileResult === false) {
        $fileStmt->close();
        $respondWithError(500, 'Unable to fetch area detail files.');
    }

    while ($fileRow = $fileResult->fetch_assoc()) {
        $fileKey = $trimValue($fileRow['file_key'] ?? '');
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

        $mimeType = $trimValue($fileRow['mime_type'] ?? '');
        $base64 = base64_encode($fileData);
        $dataUri = 'data:' . ($mimeType !== '' ? $mimeType : 'application/octet-stream') . ';base64,' . $base64;
        $isImage = $mimeType !== '' && strpos($mimeType, 'image/') === 0;

        $filesByKey[$fileKey][] = [
            'name' => $trimValue($fileRow['file_name'] ?? ''),
            'mime' => $mimeType,
            'size' => array_key_exists('file_size', $fileRow) ? (int) $fileRow['file_size'] : null,
            'data_uri' => $dataUri,
            'is_image' => $isImage,
            'created_at' => $formatDate($fileRow['created_at'] ?? ''),
        ];
    }

    $fileResult->free();
    $fileStmt->close();
}

$templatePath = __DIR__ . '/pdfhtml/index.php';
if (!is_file($templatePath)) {
    $respondWithError(500, 'PDF template is missing.');
}

ob_start();
$report = $report;
$filesByKey = $filesByKey;
include $templatePath;
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('isHtml5ParserEnabled', true);

$chrootPath = realpath(__DIR__ . '/pdfhtml');
if ($chrootPath !== false) {
    $options->setChroot($chrootPath);
}

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4');
$dompdf->render();

$filenameSource = $report['property_name'] !== '' ? $report['property_name'] : 'area-detail-' . $areaId;
$filename = preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower($filenameSource));
$filename = trim($filename, '-');
if ($filename === '') {
    $filename = 'area-detail';
}

$dompdf->stream($filename . '.pdf', ['Attachment' => true]);
exit;
