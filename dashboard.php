<?php
include('includes/header.php');
include('includes/components/sidebar.php');
require_once __DIR__ . '/crest/crest.php';

// Fetch employee list from Bitrix API
$employeeResponse = CRest::call('user.get', ['filter' => ['ACTIVE' => true]]);
$employees = $employeeResponse['result'] ?? [];

$selectedEmployeeId = $_GET['employee_id'] ?? '';
$selectedEmployee = null;

if ($selectedEmployeeId !== '') {
    foreach ($employees as $emp) {
        if ($emp['ID'] == $selectedEmployeeId) {
            $selectedEmployee = $emp;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HR Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto py-10 px-6">
        <h1 class="text-4xl font-bold text-blue-700 mb-10 text-center">HR Dashboard</h1>

        <!-- Quick Links Section -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-10">
            <a href="noc_cert.php" class="p-4 bg-blue-500 text-white rounded-lg text-center font-semibold hover:bg-blue-600 transition">NOC</a>
            <a href="salary_cert.php" class="p-4 bg-green-500 text-white rounded-lg text-center font-semibold hover:bg-green-600 transition">Salary Certificate</a>
            <a href="employees.php" class="p-4 bg-yellow-500 text-white rounded-lg text-center font-semibold hover:bg-yellow-600 transition">Employee List</a>
            <a href="attendance.php" class="p-4 bg-indigo-500 text-white rounded-lg text-center font-semibold hover:bg-indigo-600 transition">Attendance</a>
            <a href="https://mondus.group/bizproc/processes/12/view/0/" target="_blank" class="p-4 bg-red-500 text-white rounded-lg text-center font-semibold hover:bg-red-600 transition">Leave Application</a>
        </div>

        <!-- Employee Details Section -->
        <div class="bg-white p-8 rounded-lg shadow">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">View Employee Details</h2>

            <form method="GET" class="flex flex-col md:flex-row items-center gap-4 mb-8">
                <select name="employee_id" id="employee_id" class="p-3 border border-gray-300 rounded-md w-full md:w-1/3 focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Choose Employee --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['ID'] ?>" <?= ($selectedEmployeeId == $emp['ID']) ? 'selected' : '' ?>>
                            <?= $emp['NAME'] . ' ' . $emp['LAST_NAME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md">
                    Show
                </button>
            </form>

            <?php if ($selectedEmployee): ?>
                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <?php
                    $userPhoto = $selectedEmployee['PERSONAL_PHOTO'];
                    if ($userPhoto) {
                        $photoUrl = CRest::call('user.get', ['ID' => $selectedEmployee['ID']])['result'][0]['PERSONAL_PHOTO'];
                    }
                    ?>

                    <?php if (!empty($photoUrl)): ?>
                        <img src="<?= $photoUrl ?>" alt="Employee Photo" class="w-32 h-32 rounded-full object-cover border">
                    <?php else: ?>
                        <div class="w-32 h-32 rounded-full bg-blue-500 flex items-center justify-center text-white text-3xl font-bold border">
                            <?= strtoupper(substr($selectedEmployee['NAME'], 0, 1) . substr($selectedEmployee['LAST_NAME'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-2 text-center md:text-left">
                        <p><strong>ID:</strong> <?= $selectedEmployee['ID'] ?></p>
                        <p><strong>Name:</strong> <?= $selectedEmployee['NAME'] . ' ' . $selectedEmployee['LAST_NAME'] ?></p>
                        <p><strong>Email:</strong> <?= $selectedEmployee['EMAIL'] ?></p>
                        <p><strong>Position:</strong> <?= $selectedEmployee['WORK_POSITION'] ?: 'N/A' ?></p>
                        <p><strong>Department:</strong>
                            <?php
                            if (!empty($selectedEmployee['UF_DEPARTMENT'])) {
                                echo implode(', ', $selectedEmployee['UF_DEPARTMENT']);
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </p>
                    </div>
                </div>

            <?php elseif ($selectedEmployeeId !== ''): ?>
                <p class="text-red-500 mt-4 font-semibold">Employee not found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>