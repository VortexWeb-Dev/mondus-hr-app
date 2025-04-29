<?php
include('includes/header.php');
include('includes/components/sidebar.php');
require_once __DIR__ . '/crest/crest.php';

// Error handling function
function handleApiError($response, $operation)
{
    if (!isset($response['result']) || isset($response['error'])) {
        $errorMsg = isset($response['error_description']) ? $response['error_description'] : 'Unknown error';
        error_log("Bitrix24 API Error during {$operation}: {$errorMsg}");
        return false;
    }
    return true;
}

// Get current date and first day of current month for default date range
$today = date('Y-m-d');
$firstDayOfMonth = date('Y-m-01');

// Process date filter
$startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : $firstDayOfMonth;
$endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : $today;

// Fetch active employees with proper error handling
$employees = [];
$employeeResponse = CRest::call('user.get', ['filter' => ['ACTIVE' => true]]);
if (handleApiError($employeeResponse, 'fetching employees')) {
    $employees = $employeeResponse['result'];
}

// Function to fetch employee attendance between dates
function fetchEmployeeAttendance($employeeId, $startDate, $endDate)
{
    $params = [
        'USER_ID' => $employeeId,
        'FILTER' => [
            '>=DATE_START' => $startDate,
            '<=DATE_START' => $endDate
        ]
    ];

    $attendanceRecords = CRest::call('timeman.timecontrol.reports.get', $params);

    if (!isset($attendanceRecords['result']) || !isset($attendanceRecords['result']['report']['days'])) {
        return [];
    }

    return $attendanceRecords['result']['report']['days'];
}

// Function to fetch employee absences/leave data
function fetchEmployeeAbsence($employeeId, $startDate, $endDate)
{
    $params = [
        'FILTER' => [
            'USER_ID' => $employeeId,
            '>=DATE_FROM' => $startDate,
            '<=DATE_TO' => $endDate
        ]
    ];

    $absenceRecords = CRest::call('absence.get', $params);

    if (!isset($absenceRecords['result'])) {
        return [];
    }

    return $absenceRecords['result'];
}

// Initialize data arrays
$attendanceData = [];
$leaveData = [];
$presentDaysData = [];
$absenceData = [];
$totalLeaveAllowed = 32; // This could be fetched from Bitrix24 settings if available

// Process the selected employee or all employees
$selectedEmployeeId = isset($_POST['employee_id']) && $_POST['employee_id'] != '' ? $_POST['employee_id'] : null;

if ($selectedEmployeeId) {
    // Fetch data for specific employee
    $attendanceRecords = fetchEmployeeAttendance($selectedEmployeeId, $startDate, $endDate);
    $absenceRecords = fetchEmployeeAbsence($selectedEmployeeId, $startDate, $endDate);

    // Process attendance records
    $attendanceData[$selectedEmployeeId] = $attendanceRecords;

    // Count present days (days with work time > 0)
    $presentDaysData[$selectedEmployeeId] = count(array_filter($attendanceRecords, function ($day) {
        return isset($day['WORKTIME']) && $day['WORKTIME'] > 0;
    }));

    // Process leave data
    $leaveData[$selectedEmployeeId] = count($absenceRecords);
    $absenceData[$selectedEmployeeId] = $absenceRecords;
} else {
    // Fetch data for all employees
    foreach ($employees as $employee) {
        $employeeId = $employee['ID'];

        // Fetch and process attendance records
        $attendanceRecords = fetchEmployeeAttendance($employeeId, $startDate, $endDate);
        $attendanceData[$employeeId] = $attendanceRecords;

        // Count present days
        $presentDaysData[$employeeId] = count(array_filter($attendanceRecords, function ($day) {
            return isset($day['WORKTIME']) && $day['WORKTIME'] > 0;
        }));

        // Fetch and process absence/leave records
        $absenceRecords = fetchEmployeeAbsence($employeeId, $startDate, $endDate);
        $leaveData[$employeeId] = count($absenceRecords);
        $absenceData[$employeeId] = $absenceRecords;
    }
}

// Get selected employee details
$selectedEmployee = null;
if ($selectedEmployeeId) {
    foreach ($employees as $emp) {
        if ($emp['ID'] == $selectedEmployeeId) {
            $selectedEmployee = $emp;
            break;
        }
    }
}

// Pagination for attendance table
$recordsPerPage = 15;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Build filtered attendance records for table display
$filteredAttendance = [];

if ($selectedEmployeeId) {
    $empRecords = $attendanceData[$selectedEmployeeId] ?? [];
    foreach ($empRecords as $record) {
        $recordDate = $record['DATE'];
        if ($recordDate >= $startDate && $recordDate <= $endDate) {
            $filteredAttendance[] = [
                'employee_id' => $selectedEmployeeId,
                'date' => $recordDate,
                'hours' => isset($record['WORKTIME']) ? round($record['WORKTIME'] / 3600, 2) : 0,
                'start_time' => isset($record['TIME_START']) ? date('H:i', strtotime($record['TIME_START'])) : '-',
                'end_time' => isset($record['TIME_FINISH']) ? date('H:i', strtotime($record['TIME_FINISH'])) : '-',
                'status' => isset($record['STATUS']) ? $record['STATUS'] : '-'
            ];
        }
    }
} else {
    foreach ($attendanceData as $empId => $records) {
        foreach ($records as $record) {
            $recordDate = $record['DATE'];
            if ($recordDate >= $startDate && $recordDate <= $endDate) {
                $filteredAttendance[] = [
                    'employee_id' => $empId,
                    'date' => $recordDate,
                    'hours' => isset($record['WORKTIME']) ? round($record['WORKTIME'] / 3600, 2) : 0,
                    'start_time' => isset($record['TIME_START']) ? date('H:i', strtotime($record['TIME_START'])) : '-',
                    'end_time' => isset($record['TIME_FINISH']) ? date('H:i', strtotime($record['TIME_FINISH'])) : '-',
                    'status' => isset($record['STATUS']) ? $record['STATUS'] : '-'
                ];
            }
        }
    }
}

// Sort attendance records by date (newest first)
usort($filteredAttendance, function ($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Apply pagination
$totalRecords = count($filteredAttendance);
$totalPages = ceil($totalRecords / $recordsPerPage);
$paginatedAttendance = array_slice($filteredAttendance, ($page - 1) * $recordsPerPage, $recordsPerPage);

// Build employee name mapping
$employeeMap = [];
foreach ($employees as $emp) {
    $employeeMap[$emp['ID']] = $emp['NAME'] . ' ' . $emp['LAST_NAME'];
}

// Define status colors for UI
$statusColors = [
    'OPENED' => 'bg-green-100 text-green-800',
    'CLOSED' => 'bg-blue-100 text-blue-800',
    'EXPIRED' => 'bg-red-100 text-red-800',
    'APPROVED' => 'bg-purple-100 text-purple-800',
    'REJECTED' => 'bg-yellow-100 text-yellow-800'
];

// Helper function to get remaining leave
function getRemainingLeave($totalAllowed, $taken)
{
    return max(0, $totalAllowed - $taken);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Attendance Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 p-6 min-h-screen">

    <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-lg space-y-8">
        <h1 class="text-3xl font-bold text-center text-gray-800">Employee Attendance Report</h1>

        <!-- Employee Filter -->
        <form method="POST" class="flex flex-col md:flex-row justify-center items-center gap-4">
            <div class="flex flex-col md:flex-row items-center">
                <label for="employee_id" class="mr-2 text-lg font-medium text-gray-700">Select Employee:</label>
                <select name="employee_id" id="employee_id" onchange="this.form.submit()" class="p-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Employees</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee['ID'] ?>" <?= ($selectedEmployeeId == $employee['ID']) ? 'selected' : '' ?>>
                            <?= $employee['NAME'] . ' ' . $employee['LAST_NAME'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <!-- Date Filter Form -->
        <form method="GET" class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex items-center">
                <label for="start_date" class="mr-2 text-gray-700">From:</label>
                <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>"
                    class="border p-2 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center">
                <label for="end_date" class="mr-2 text-gray-700">To:</label>
                <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>"
                    class="border p-2 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <input type="hidden" name="employee_id" value="<?= htmlspecialchars($selectedEmployeeId ?? '') ?>">

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md">
                Apply Filters
            </button>
        </form>

        <?php if ($selectedEmployee): ?>
            <!-- Employee Basic Info Card -->
            <div class="bg-blue-50 p-6 rounded-lg shadow-md space-y-3 text-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold"><?= $selectedEmployee['NAME'] . ' ' . $selectedEmployee['LAST_NAME'] ?></h2>
                        <p class="text-sm text-gray-500"><?= $selectedEmployee['WORK_POSITION'] ?? 'Employee' ?></p>
                    </div>
                    <?php if (!empty($selectedEmployee['PERSONAL_PHOTO'])): ?>
                        <img src="<?= $selectedEmployee['PERSONAL_PHOTO'] ?>" alt="Employee Photo" class="w-16 h-16 rounded-full">
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div><strong>Email:</strong> <?= $selectedEmployee['EMAIL'] ?? 'N/A' ?></div>
                    <div><strong>Phone:</strong> <?= $selectedEmployee['PERSONAL_PHONE'] ?? $selectedEmployee['WORK_PHONE'] ?? 'N/A' ?></div>
                    <div><strong>Position:</strong> <?= $selectedEmployee['WORK_POSITION'] ?? 'N/A' ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border-t-4 border-blue-500 p-6 rounded-lg shadow space-y-2">
                <h3 class="text-xl font-semibold text-gray-700">Total Leave Allowance</h3>
                <p class="text-2xl font-bold text-blue-600"><?= $totalLeaveAllowed ?> Days</p>
            </div>

            <div class="bg-white border-t-4 border-green-500 p-6 rounded-lg shadow space-y-2">
                <h3 class="text-xl font-semibold text-gray-700">Leave Taken</h3>
                <p class="text-2xl font-bold text-green-600">
                    <?php
                    if ($selectedEmployeeId) {
                        echo $leaveData[$selectedEmployeeId] . ' Days';
                        echo '<p class="text-sm text-gray-500 mt-1">Remaining: ' .
                            getRemainingLeave($totalLeaveAllowed, $leaveData[$selectedEmployeeId]) . ' Days</p>';
                    } else {
                        echo 'Select an employee';
                    }
                    ?>
                </p>
            </div>

            <div class="bg-white border-t-4 border-yellow-500 p-6 rounded-lg shadow space-y-2">
                <h3 class="text-xl font-semibold text-gray-700">Present Days</h3>
                <p class="text-2xl font-bold text-yellow-600">
                    <?php
                    if ($selectedEmployeeId) {
                        echo $presentDaysData[$selectedEmployeeId] . ' Days';
                    } else {
                        echo 'Select an employee';
                    }
                    ?>
                </p>
            </div>
        </div>

        <?php if ($selectedEmployeeId): ?>
            <!-- Attendance Chart -->
            <div class="mt-8">
                <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Attendance Overview</h2>
                <div class="bg-white p-4 rounded-lg shadow">
                    <canvas id="attendanceChart" height="200"></canvas>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('attendanceChart');

                    // Extract hours data
                    const dates = <?= json_encode(array_map(function ($record) {
                                        return $record['date'];
                                    }, $filteredAttendance)) ?>;
                    const hours = <?= json_encode(array_map(function ($record) {
                                        return $record['hours'];
                                    }, $filteredAttendance)) ?>;

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'Hours Worked',
                                data: hours,
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Hours'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Date'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        <?php endif; ?>

        <!-- Attendance Table -->
        <div class="overflow-x-auto mt-12">
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Working Hours Report</h2>

            <?php if (empty($paginatedAttendance)): ?>
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-md text-center">
                    <p class="text-yellow-700">No attendance records found for the selected period.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Date</th>
                            <?php if (!$selectedEmployeeId): ?>
                                <th class="py-3 px-4 text-left text-sm font-semibold">Employee</th>
                            <?php endif; ?>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Clock In</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Clock Out</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Hours Worked</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginatedAttendance as $log): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-3 px-4"><?= date('M d, Y', strtotime($log['date'])) ?></td>
                                <?php if (!$selectedEmployeeId): ?>
                                    <td class="py-3 px-4"><?= $employeeMap[$log['employee_id']] ?? 'Unknown' ?></td>
                                <?php endif; ?>
                                <td class="py-3 px-4"><?= $log['start_time'] ?></td>
                                <td class="py-3 px-4"><?= $log['end_time'] ?></td>
                                <td class="py-3 px-4 font-medium"><?= $log['hours'] ?> hrs</td>
                                <td class="py-3 px-4">
                                    <?php if (!empty($log['status'])): ?>
                                        <span class="px-2 py-1 text-xs rounded-full <?= $statusColors[$log['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <?= $log['status'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center items-center space-x-1 mt-6">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?><?= $selectedEmployeeId ? '&employee_id=' . urlencode($selectedEmployeeId) : '' ?>"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-blue-500 hover:text-white">
                                Previous
                            </a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?= $i ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?><?= $selectedEmployeeId ? '&employee_id=' . urlencode($selectedEmployeeId) : '' ?>"
                                class="px-4 py-2 rounded-md <?= $i == $page ? 'bg-blue-500 text-white' : 'text-gray-700 bg-gray-200 hover:bg-blue-500 hover:text-white' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?><?= $selectedEmployeeId ? '&employee_id=' . urlencode($selectedEmployeeId) : '' ?>"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-blue-500 hover:text-white">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if ($selectedEmployeeId && !empty($absenceData[$selectedEmployeeId])): ?>
            <!-- Leave History -->
            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Leave History</h2>
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-semibold">From</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold">To</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Type</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Status</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absenceData[$selectedEmployeeId] as $absence): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-3 px-4"><?= date('M d, Y', strtotime($absence['DATE_FROM'])) ?></td>
                                <td class="py-3 px-4"><?= date('M d, Y', strtotime($absence['DATE_TO'])) ?></td>
                                <td class="py-3 px-4"><?= $absence['TYPE_NAME'] ?? 'N/A' ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                  <?= isset($absence['STATUS']) && $absence['STATUS'] === 'APPROVED' ?
                                        'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= $absence['STATUS'] ?? 'PENDING' ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <?php
                                    $start = new DateTime($absence['DATE_FROM']);
                                    $end = new DateTime($absence['DATE_TO']);
                                    $interval = $start->diff($end);
                                    echo $interval->days + 1; // +1 to include both start and end days
                                    ?> days
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Add JavaScript for form submission when changing employee
        document.getElementById('employee_id').addEventListener('change', function() {
            // Get current URL parameters for date filters
            const urlParams = new URLSearchParams(window.location.search);
            const startDate = urlParams.get('start_date');
            const endDate = urlParams.get('end_date');

            // Create a form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.pathname + (startDate && endDate ?
                `?start_date=${startDate}&end_date=${endDate}` : '');

            // Add employee ID
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'employee_id';
            input.value = this.value;
            form.appendChild(input);

            // Submit the form
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>

</html>