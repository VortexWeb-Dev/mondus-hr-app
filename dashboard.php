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

<body class="bg-gray-100 p-6 min-h-screen">

    <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-lg space-y-8">
        <h1 class="text-4xl font-bold text-center text-blue-600">HR Dashboard</h1>

        <!-- Quick Links Section -->
        <div class="flex flex-wrap gap-4 mb-6 justify-between">
            <a href="noc_cert.php" class="flex-1 min-w-[200px] p-4 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 text-center">NOC</a>
            <a href="salary_cert.php" class="flex-1 min-w-[200px] p-4 bg-green-500 text-white rounded-md shadow-md hover:bg-green-600 text-center">Salary Certificate</a>
            <a href="employees.php" class="flex-1 min-w-[200px] p-4 bg-yellow-500 text-white rounded-md shadow-md hover:bg-yellow-600 text-center">Employee Details</a>
            <a href="attendance.php" class="flex-1 min-w-[200px] p-4 bg-indigo-500 text-white rounded-md shadow-md hover:bg-indigo-600 text-center">Attendance Report</a>
            <a href="https://mondus.group/bizproc/processes/12/view/0/" target="_blank" class="flex-1 min-w-[200px] p-4 bg-red-500 text-white rounded-md shadow-md hover:bg-red-600 text-center">Leave Application</a>
        </div>

        <!-- Summary Cards -->
        <!-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white border-t-4 border-blue-500 p-6 rounded-lg shadow space-y-2">
                <h3 class="text-xl font-semibold text-gray-700">Total Employees</h3>
                <p class="text-3xl font-bold text-blue-600">50 Employees</p>
            </div>

            <div class="bg-white border-t-4 border-green-500 p-6 rounded-lg shadow space-y-2">
                <h3 class="text-xl font-semibold text-gray-700">Leave Applications</h3>
                <p class="text-3xl font-bold text-green-600">5 Pending</p>
            </div>

            <div class="bg-white border-t-4 border-yellow-500 p-6 rounded-lg shadow space-y-2">
                <h3 class="text-xl font-semibold text-gray-700">Notice Periods</h3>
                <p class="text-3xl font-bold text-yellow-600">3 Employees</p>
            </div>
        </div> -->

        <!-- Employee Details Section -->
        <div class="mt-12 bg-white p-6 rounded-lg shadow space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">View Employee Details</h2>

            <form method="GET" class="flex flex-col md:flex-row items-center gap-4">
                <label for="employee_id" class="text-lg font-medium text-gray-700">Select Employee:</label>
                <select name="employee_id" id="employee_id" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choose Employee --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['ID'] ?>" <?= ($selectedEmployeeId == $emp['ID']) ? 'selected' : '' ?>>
                            <?= $emp['NAME'] . ' ' . $emp['LAST_NAME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">
                    Show
                </button>
            </form>

            <?php if ($selectedEmployee): ?>
                <div class="mt-6 border-t pt-4 space-y-2 text-gray-700">
                    <p><strong>Employee ID:</strong> <?= $selectedEmployee['ID'] ?></p>
                    <p><strong>Name:</strong> <?= $selectedEmployee['NAME'] . ' ' . $selectedEmployee['LAST_NAME'] ?></p>
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
            <?php elseif ($selectedEmployeeId !== ''): ?>
                <p class="text-red-500 mt-4 font-semibold">Employee not found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>