<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . "/crest/crest.php";
require_once __DIR__ . "/crest/crestcurrent.php";

if (isset($_POST['documentType'])) {

    $documentType = $_POST['documentType'];
$templatePath = __DIR__ . "/templates/" . 
    ($documentType === 'salary_certificate' ? 'Salary.docx' :
    ($documentType === 'noc' ? 'NOC.docx' :
    ($documentType === 'notice_period' ? 'NoticePeriod.docx' : '')));

    if (empty($templatePath) || !file_exists($templatePath)) {
        echo "Invalid document type.";
        exit;
    }

    $currentUser = CRestCurrent::call('user.current');
    $userId = $currentUser['result']['ID'];

    $user = getUserInfo($userId);
    if (!$user) {
        echo "User information could not be retrieved.";
        exit;
    }

    function generateNoticePeriodDocument($templatePath, $user, $resignationDate, $noticePeriodStartDate, $lastWorkingDay, $addressTo, $country = 'UAE') {
        if (!file_exists($templatePath)) {
            echo "Template file does not exist.";
            return false;
        }

        $template = file_get_contents($templatePath);

        $template = str_replace('${FULL_NAME}', $user['NAME'] . ' ' . $user['LAST_NAME'], $template);
        $template = str_replace('${RESIGNATION_DATE}', $resignationDate, $template);
        $template = str_replace('${NOTICE_PERIOD_START_DATE}', $noticePeriodStartDate, $template);
        $template = str_replace('${LAST_WORKING_DAY}', $lastWorkingDay, $template);
        $template = str_replace('${ADDRESS_TO}', $addressTo, $template);
        $template = str_replace('${CURRENT_DATE}', date('Y-m-d'), $template); 

        $template = str_replace('${COUNTRY}', $country, $template);

        $outputPath = __DIR__ . '/generated_documents/' . 'NoticePeriod_' . time() . '.docx';

        file_put_contents($outputPath, $template);
    
        return $outputPath;
    }
    
    $fullName = trim(ucwords(strtolower(trim($user['NAME']))) . ' ' .
        ucwords(strtolower(trim($user['SECOND_NAME'] ?? ''))) . ' ' .
        ucwords(strtolower(trim($user['LAST_NAME']))));
    $fullName = preg_replace('/\s+/', ' ', $fullName);
    
    $sanitizedFileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fullName);

    if ($documentType === 'notice_period') {
        $wordFile = generateNoticePeriodDocument(
            $templatePath,
            $user,
            $_POST['resignationDate'] ?? null,
            $_POST['noticePeriodStartDate'] ?? null,
            $_POST['lastWorkingDay'] ?? null,
            $_POST['addressTo'] ?? null,
            $_POST['country'] ?? 'UAE'
        );
    } else {
        $wordFile = generateWordDocument(
            $templatePath,
            $user,
            $_POST['startDate'] ?? null,
            $_POST['endDate'] ?? null,
            $_POST['currentSalaryNoc'] ?? null,
            $_POST['currentSalary'] ?? null,
            $_POST['addressTo'] ?? null,
            $_POST['addressToNoc'] ?? null,
            $_POST['nocReason'] ?? null,
            $_POST['country'] ?? 'UAE'
        );
    }

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
