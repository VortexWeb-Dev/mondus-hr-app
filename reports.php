<?php
include('includes/header.php');
include('includes/components/sidebar.php');

// Load Bitrix users from API
require_once __DIR__ . '/crest/crest.php';
$employeeResponse = CRest::call('user.get', ['filter' => ['ACTIVE' => true]]);

$employees = [];
if (!empty($employeeResponse['result'])) {
    foreach ($employeeResponse['result'] as $user) {
        $employees[] = ['id' => $user['ID'], 'name' => trim($user['NAME'] . ' ' . $user['LAST_NAME'])];
    }
}

// Sample work logs (static demo)
$workLogs = [
    ['employee_id' => 1, 'date' => '2025-04-28', 'hours' => 8],
    ['employee_id' => 2, 'date' => '2025-04-28', 'hours' => 7],
    ['employee_id' => 1, 'date' => '2025-04-27', 'hours' => 6],
    ['employee_id' => 3, 'date' => '2025-04-27', 'hours' => 9],
    ['employee_id' => 2, 'date' => '2025-04-26', 'hours' => 8],
    ['employee_id' => 1, 'date' => '2025-04-26', 'hours' => 5],
];

// Filters
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

// Prepare data for Chart.js
$labels = array_unique(array_column($filteredLogs, 'date'));
sort($labels);

$employeeHours = [];
foreach ($employees as $emp) {
    $empData = [];
    foreach ($labels as $date) {
        $hours = 0;
        foreach ($filteredLogs as $log) {
            if ($log['employee_id'] == $emp['id'] && $log['date'] == $date) {
                $hours = $log['hours'];
            }
        }
        $empData[] = $hours;
    }
    $employeeHours[] = [
        'label' => $emp['name'],
        'data' => $empData,
        'backgroundColor' => sprintf('rgba(%d, %d, %d, 0.6)', rand(0, 255), rand(0, 255), rand(0, 255)),
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Working Hours Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-lg space-y-8">
    <h1 class="text-3xl font-bold text-center text-gray-800">Working Hours Report</h1>

    <!-- Filters -->
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


    <!-- Table -->
    <div class="overflow-x-auto mt-12">
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Detailed Table</h2>
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

<script>
    const ctx = document.getElementById('hoursChart').getContext('2d');
    const hoursChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: <?= json_encode($employeeHours) ?>
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours Worked'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Employee Working Hours'
                }
            }
        }
    });
</script>

</body>
</html>
