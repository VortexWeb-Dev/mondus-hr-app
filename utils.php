<?php

require 'vendor/autoload.php';
require_once 'crest/crest.php';
require_once 'utils.php';

use PhpOffice\PhpWord\TemplateProcessor;

function formatDateRange($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    return sprintf(
        "%s to the %s of %s %s",
        $start->format('jS'),
        $end->format('jS'),
        $start->format('F'),
        $start->format('Y')
    );
}

function generateWordDocument($templatePath, $user, $startDate, $endDate, $salaryNoc, $salary, $address_to, $address_to_noc, $noc_reason, $country, $resignationDate, $noticePeriodStartDate, $lastWorkingDay)
{
    if (!file_exists($templatePath)) {
        error_log("Template file does not exist: $templatePath");
        return null;
    }

    $templateProcessor = new TemplateProcessor($templatePath);

    // Prepare data for the template
    $templateData = [
        'FULL_NAME' => trim($user['NAME'] . ' ' . $user['LAST_NAME'] ?? ""),
        'NATIONALITY' => $user['PERSONAL_COUNTRY'] ?? "",
        'PASSPORT_NUMBER' => $user['UF_USR_1743588068970'] ?? "",
        'DATE_OF_JOINING' => (new DateTime($user['UF_EMPLOYMENT_DATE']))->format('F Y'),
        'POSITION' => $user['WORK_POSITION'] ?? "",
        'SALARY' => number_format((int)$salary) ?? "",
        'SALARY_NOC' => $salaryNoc ?? "",
        'ADDRESS_TO' => $address_to ?? "",
        'ADDRESS_TO_NOC' => $address_to_noc ?? "",
        'CURRENT_DATE' => getTodayDateFormatted(),
        'NOC_SENTENCE' => generateNocSentence($noc_reason, $country, formatDateRange($startDate, $endDate)),
        'NOC_REASON' => generateNocReasonText($noc_reason, $country),
        'REF_NO' => generateReferenceNumber(),
        'SALARY_TEXT' => convertSalaryToText((int)$salary),
        'RESIGNATION_DATE' => (new DateTime($resignationDate))->format('jS F Y') ?? "",
        'NOTICE_PERIOD_START_DATE' => (new DateTime($noticePeriodStartDate))->format('jS F Y') ?? "",
        'LAST_WORKING_DAY' => (new DateTime($lastWorkingDay))->format('jS F Y') ?? "",
    ];

    foreach ($templateData as $placeholder => $value) {
        $templateProcessor->setValue($placeholder, $value);
    }

    // Save the document to a temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'docx');
    $templateProcessor->saveAs($tempFile);

    return $tempFile;
}

function getUserInfo($userId)
{
    $userResponse = CRest::call('user.get', ['ID' => $userId]);
    return $userResponse['result'][0] ?? null;
}

function getTodayDateFormatted()
{
    return date('jS F Y');
}

function generateNocSentence($noc_reason, $country, $travel_date)
{
    $nocSentence = '';

    switch ($noc_reason) {
        case 'visa_application':
            $nocSentence = "will be applying for a $country Visa.";
            break;
        case 'travel':
            $nocSentence = "will be traveling to $country from the $travel_date.";
            break;
        case 'mortgage_application':
            $nocSentence = "will be applying for a mortgage loan in $country.";
            break;
        case 'credit_card_application':
            $nocSentence = "will be applying for a credit card in $country.";
            break;
        case 'debit_card_application':
            $nocSentence = "will be applying for a debit card in $country.";
            break;
        case 'bank_account_opening':
            $nocSentence = "will be applying to open a bank account in $country.";
            break;
        default:
            $nocSentence = "will be applying for necessary processes in $country.";
            break;
    }

    return $nocSentence;
}

function generateNocReasonText($noc_reason, $country)
{
    $nocReasonText = '';

    switch ($noc_reason) {
        case 'visa_application':
            $nocReasonText = "No Objection Letter for $country Visa Application and Travel.";
            break;
        case 'travel':
            $nocReasonText = "No Objection Letter for Travel to $country.";
            break;
        case 'mortgage_application':
            $nocReasonText = "No Objection Letter for applying for a mortgage loan in $country.";
            break;
        case 'credit_card_application':
            $nocReasonText = "No Objection Letter for applying for a credit card in $country.";
            break;
        case 'debit_card_application':
            $nocReasonText = "No Objection Letter for applying for a debit card in $country.";
            break;
        case 'bank_account_application':
            $nocReasonText = "No Objection Letter for opening a bank account in $country.";
            break;
        default:
            $nocReasonText = "No Objection Letter for necessary processes in $country.";
            break;
    }

    return $nocReasonText;
}

function convertSalaryToText($amount)
{
    if (!is_numeric($amount)) {
        return "Invalid amount";
    }

    $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    $amountInWords = ucfirst($formatter->format($amount));

    return "$amountInWords Dirhams only";
}

function generateReferenceNumber()
{
    $prefix = "MONDUS-HR-DOC01-BS-SC";
    $year = date("Y");
    $randomNumber = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

    return "{$prefix}-{$year}-{$randomNumber}";
}
