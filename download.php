<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . "/crest/crest.php";
require_once __DIR__ . "/crest/crestcurrent.php";

if (isset($_POST['documentType'])) {

    $documentType = $_POST['documentType']; // salary, noc, notice
    $template = $_POST['templateType']; // mondus_properties, mondus_events, mondus_marketing, mondus_cft
    $templatePath = __DIR__ . "/templates/$documentType/$template.docx";

    if (empty($templatePath) || !file_exists($templatePath)) {
        echo "Invalid document type or template type.";
        exit;
    }

    $data = $_POST;

    $fullName = $_POST['fullName'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $dateOfJoining = $_POST['joiningDate'] ?? '';
    $dateOfIssue = $_POST['dateOfIssue'] ?? '';
    $currentSalary = $_POST['currentSalary'] ?? '';
    $salaryCurrency = $_POST['salaryCurrency'] ?? 'AED';
    $addressTo = $_POST['addressTo'] ?? '';

    $sanitizedFileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fullName);

    $wordFile = generateWordDocument(
        $templatePath,
        $data
    );

    if ($wordFile) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . basename($templatePath, '.docx') . '_' . $sanitizedFileName . '.docx"');
        header('Content-Length: ' . filesize($wordFile));
        readfile($wordFile);

        unlink($wordFile);
        exit;
    } else {
        echo "Failed to generate the document.";
    }
} else {
    echo "No document type specified.";
}
