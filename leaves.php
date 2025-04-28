<?php
include('includes/header.php');
include('includes/components/sidebar.php');
require_once __DIR__ . '/crest/crest.php';

// Fetch active employees
$employees = CRest::call('user.get', ['filter' => ['ACTIVE' => true]]);

// Function to fetch employee attendance with leave information
function fetchEmployeeAttendance($employeeId)
{
    $attendanceRecords = CRest::call('timeman.timecontrol.reports.get', [
        'USER_ID' => $employeeId
    ]);
    return !empty($attendanceRecords['result']) ? $attendanceRecords['result']['report']['days'] : [];
}

// Initialize attendance and leave data
$attendanceData = [];
$leaveData = [];

if (isset($_POST['employee_id']) && $_POST['employee_id'] != '') {
    $employeeId = $_POST['employee_id'];
    $attendanceData = fetchEmployeeAttendance($employeeId);
    $leaveData[$employeeId] = array_filter($attendanceData, fn($day) => isset($day['ABSENCE']) && $day['ABSENCE'] === true);
} else {
    foreach ($employees['result'] as $employee) {
        $attendanceData[$employee['ID']] = fetchEmployeeAttendance($employee['ID']);
        $leaveData[$employee['ID']] = array_filter($attendanceData[$employee['ID']], fn($day) => isset($day['ABSENCE']) && $day['ABSENCE'] === true);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Leave Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6 min-h-screen">

<div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-lg space-y-8">
    <h1 class="text-3xl font-bold text-center text-gray-800">Employee Leave Report</h1>

    <!-- Employee Filter -->
    <form method="POST" class="flex flex-col md:flex-row justify-center items-center gap-4">
        <div class="flex flex-col md:flex-row items-center">
            <label for="employee_id" class="mr-2 text-lg font-medium text-gray-700">Select Employee:</label>
            <select name="employee_id" id="employee_id" onchange="this.form.submit()" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Employees</option>
                <?php foreach ($employees['result'] as $employee): ?>
                    <option value="<?= $employee['ID'] ?>" <?= (isset($_POST['employee_id']) && $_POST['employee_id'] == $employee['ID']) ? 'selected' : '' ?>>
                        <?= $employee['NAME'] . ' ' . $employee['LAST_NAME'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (isset($_POST['employee_id']) && $_POST['employee_id'] != ''): 
        $selectedEmployee = array_filter($employees['result'], fn($emp) => $emp['ID'] == $_POST['employee_id']);
        $selectedEmployee = reset($selectedEmployee);
    ?>
    <!-- Employee Basic Info Card -->
    <div class="bg-blue-50 p-6 rounded-lg shadow-md space-y-2 text-gray-700">
        <h2 class="text-2xl font-semibold mb-4 text-blue-700">Employee Details</h2>
        <div><strong>Name:</strong> <?= $selectedEmployee['NAME'] . ' ' . $selectedEmployee['LAST_NAME'] ?></div>
        <div><strong>Employee ID:</strong> <?= $selectedEmployee['ID'] ?></div>
        <?php if (!empty($selectedEmployee['WORK_POSITION'])): ?>
        <div><strong>Position:</strong> <?= $selectedEmployee['WORK_POSITION'] ?></div>
        <?php endif; ?>
        <?php if (!empty($selectedEmployee['UF_DEPARTMENT'])): ?>
        <div><strong>Department:</strong> <?= implode(', ', $selectedEmployee['UF_DEPARTMENT']) ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border-t-4 border-blue-500 p-6 rounded-lg shadow space-y-2">
            <h3 class="text-xl font-semibold text-gray-700">Total Available Leave</h3>
            <p class="text-2xl font-bold text-blue-600">32 Days</p>
        </div>

        <div class="bg-white border-t-4 border-green-500 p-6 rounded-lg shadow space-y-2">
            <h3 class="text-xl font-semibold text-gray-700">Leave Taken</h3>
            <p class="text-2xl font-bold text-green-600">
                <?php
                if (isset($_POST['employee_id']) && $_POST['employee_id'] != '') {
                    echo count($leaveData[$_POST['employee_id']]) . ' Days';
                } else {
                    echo 'Select an employee';
                }
                ?>
            </p>
        </div>
    </div>

    <!-- Leave Table -->
    <?php if (!empty($leaveData)): ?>
    <div class="overflow-x-auto bg-white rounded-lg shadow mt-8">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-4 font-semibold">Date</th>
                    <th class="text-left p-4 font-semibold">Leave Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['employee_id']) && $_POST['employee_id'] != '') {
                    foreach ($leaveData[$_POST['employee_id']] as $leaveRecord) {
                        echo "<tr class='border-t'>";
                        echo "<td class='p-4'>" . $leaveRecord['DATE'] . "</td>";
                        echo "<td class='p-4'>" . (isset($leaveRecord['ABSENCE_REASON']) ? $leaveRecord['ABSENCE_REASON'] : 'N/A') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    foreach ($leaveData as $employeeLeave) {
                        foreach ($employeeLeave as $leaveRecord) {
                            echo "<tr class='border-t'>";
                            echo "<td class='p-4'>" . $leaveRecord['DATE'] . "</td>";
                            echo "<td class='p-4'>" . (isset($leaveRecord['ABSENCE_REASON']) ? $leaveRecord['ABSENCE_REASON'] : 'N/A') . "</td>";
                            echo "</tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="text-center text-lg font-semibold text-gray-600 mt-8">
        No leave data available.
    </div>
    <?php endif; ?>

</div>

</body>
</html>
