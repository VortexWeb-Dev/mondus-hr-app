<?php
// employee.php
include('includes/header.php');
include('includes/components/sidebar.php');

require_once 'crest/crest.php'; // Assuming you have this file set up correctly

// Fetch user list
$response = CRest::call('user.get', [
    'select' => ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'WORK_POSITION', 'PERSONAL_PHONE', 'PERSONAL_MOBILE', 'WORK_PHONE'],
]);

// Check if response is valid
$employees = [];
if (!empty($response['result'])) {
    $employees = $response['result'];
}

// Pagination setup
$employees_per_page = 10;
$total_employees = count($employees);
$total_pages = ceil($total_employees / $employees_per_page);

// Get current page from URL, default is 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($total_pages, $current_page));

// Calculate start index
$start_index = ($current_page - 1) * $employees_per_page;
$paginated_employees = array_slice($employees, $start_index, $employees_per_page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-8">

    <div class="max-w-7xl mx-auto">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Employee Details</h1>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">ID</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Name</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Email</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Position</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Phone</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if (!empty($paginated_employees)): ?>
                            <?php foreach ($paginated_employees as $employee): ?>
                                <tr class="border-b hover:bg-gray-100 transition duration-200">
                                    <td class="py-3 px-6"><?php echo htmlspecialchars($employee['ID']); ?></td>
                                    <td class="py-3 px-6"><?php echo htmlspecialchars(trim($employee['NAME'] . ' ' . $employee['LAST_NAME'])); ?></td>
                                    <td class="py-3 px-6"><?php echo htmlspecialchars($employee['EMAIL']); ?></td>
                                    <td class="py-3 px-6"><?php echo htmlspecialchars($employee['WORK_POSITION']); ?></td>
                                    <td class="py-3 px-6"><?php echo htmlspecialchars($employee['PERSONAL_PHONE'] ?? $employee['PERSONAL_MOBILE'] ?? $employee['WORK_PHONE']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">No employees found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="flex justify-center items-center mt-6 space-x-4">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Previous</a>
                <?php endif; ?>

                <span class="px-4 py-2 bg-gray-200 rounded">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Next</a>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>

</html>