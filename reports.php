<?php
include('includes/header.php');
include('includes/components/sidebar.php');

// Sample employee list (Replace with database or API)
$employees = [
    ['id' => 1, 'name' => 'John Doe'],
    ['id' => 2, 'name' => 'Jane Smith'],
    ['id' => 3, 'name' => 'Alex Johnson'],
];

// Sample work logs (Replace with database or API)
$workLogs = [
    ['employee_id' => 1, 'date' => '2025-04-28', 'hours' => 8],
    ['employee_id' => 2, 'date' => '2025-04-28', 'hours' => 7],
    ['employee_id' => 1, 'date' => '2025-04-27', 'hours' => 6],
    ['employee_id' => 3, 'date' => '2025-04-27', 'hours' => 9],
    ['employee_id' => 2, 'date' => '2025-04-26', 'hours' => 8],
    ['employee_id' => 1, 'date' => '2025-04-26', 'hours' => 5],
];

// Handle filters
$filterDate = $_GET['filterDate'] ?? '';
$filterEmployee = $_GET['employee_id'] ?? '';

$filteredLogs = array_filter($workLogs, function($log) use ($filterDate, $filterEmployee) {
    $match = true;
    if ($filterDate && $log['date'] != $filterDate) {
        $match = false;
    }
    if ($filterEmployee && $log['employee_id'] != $filterEmployee) {
        $match = false;
    }
    return $match;
});

// Helper function to calculate totals
function calculateTotalHours($logs, $period = 'day') {
    $today = date('Y-m-d');
    $currentWeek = date('W');
    $currentMonth = date('m');

    $total = 0;
    foreach ($logs as $log) {
        $logDate = $log['date'];
        $logHours = $log['hours'];

        if ($period == 'day' && $logDate == $today) {
            $total += $logHours;
        }
        if ($period == 'week' && date('W', strtotime($logDate)) == $currentWeek) {
            $total += $logHours;
        }
        if ($period == 'month' && date('m', strtotime($logDate)) == $currentMonth) {
            $total += $logHours;
        }
    }
    return $total;
}

$todayHours = calculateTotalHours($filteredLogs, 'day');
$weekHours = calculateTotalHours($filteredLogs, 'week');
$monthHours = calculateTotalHours($filteredLogs, 'month');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Working Hours Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-lg space-y-8">
    <h1 class="text-3xl font-bold text-center text-gray-800">Working Hours Report</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
        <div class="bg-white border-t-4 border-blue-500 p-6 rounded-lg shadow space-y-2">
            <h3 class="text-xl font-semibold text-gray-700">Today</h3>
            <p class="text-2xl font-bold text-blue-600"><?= $todayHours ?> hrs</p>
        </div>

        <div class="bg-white border-t-4 border-green-500 p-6 rounded-lg shadow space-y-2">
            <h3 class="text-xl font-semibold text-gray-700">This Week</h3>
            <p class="text-2xl font-bold text-green-600"><?= $weekHours ?> hrs</p>
        </div>

        <div class="bg-white border-t-4 border-yellow-500 p-6 rounded-lg shadow space-y-2">
            <h3 class="text-xl font-semibold text-gray-700">This Month</h3>
            <p class="text-2xl font-bold text-yellow-600"><?= $monthHours ?> hrs</p>
        </div>
    </div>

    <!-- Filters Form -->
    <form method="GET" class="flex flex-col md:flex-row justify-center items-center space-x-4 mt-10 space-y-4 md:space-y-0">
        <div>
            <label for="filterDate" class="text-lg font-medium text-gray-700 block mb-2">Select Date:</label>
            <input type="date" id="filterDate" name="filterDate" value="<?= htmlspecialchars($filterDate) ?>" class="border p-2 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="employee_id" class="text-lg font-medium text-gray-700 block mb-2">Select Employee:</label>
            <select name="employee_id" id="employee_id" class="border p-2 rounded-md shadow-sm focus:ring-2 focus:ring-green-500">
                <option value="">All Employees</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= $filterEmployee == $emp['id'] ? 'selected' : '' ?>><?= $emp['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md mt-6 md:mt-0">
                Filter
            </button>
        </div>
    </form>

    <!-- Working Hours Table -->
    <div class="overflow-x-auto mt-10">
        <table class="min-w-full bg-white rounded-lg shadow">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Date</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Employee</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Hours Worked</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($filteredLogs)): ?>
                    <?php foreach ($filteredLogs as $log): ?>
                        <tr class="border-t">
                            <td class="py-3 px-4"><?= $log['date'] ?></td>
                            <td class="py-3 px-4">
                                <?php
                                $empName = array_filter($employees, fn($e) => $e['id'] == $log['employee_id']);
                                $empName = array_values($empName)[0]['name'] ?? 'Unknown';
                                echo $empName;
                                ?>
                            </td>
                            <td class="py-3 px-4"><?= $log['hours'] ?> hrs</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-6 text-gray-500 font-semibold">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
