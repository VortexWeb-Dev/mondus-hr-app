<?php
include('includes/header.php');
include('includes/components/sidebar.php');
?>

<div class="p-10 flex-1 bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg max-w-7xl mx-auto overflow-hidden flex flex-col">

        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col md:flex-row p-6 gap-6 overflow-y-auto">
            <!-- Leave Balance Section -->
            <div class="flex-1 p-6">
                <h3 class="text-xl font-semibold text-gray-800 text-center mb-4">Leave Balance</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-b">Employee Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-b">Leaves Taken</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-b">Leave Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Dummy data
                            $employees = [
                                ["name" => "John Doe", "leaves_taken" => 8, "leave_balance" => 12],
                                ["name" => "Jane Smith", "leaves_taken" => 5, "leave_balance" => 15],
                                ["name" => "Emily Davis", "leaves_taken" => 10, "leave_balance" => 10],
                                ["name" => "Michael Brown", "leaves_taken" => 2, "leave_balance" => 18],
                                ["name" => "Sarah Wilson", "leaves_taken" => 7, "leave_balance" => 13],
                            ];

                            // Loop through the data and render rows
                            foreach ($employees as $employee) {
                                echo "
                                <tr class='border-b'>
                                    <td class='px-6 py-4 text-sm text-gray-800'>{$employee['name']}</td>
                                    <td class='px-6 py-4 text-sm text-gray-800'>{$employee['leaves_taken']}</td>
                                    <td class='px-6 py-4 text-sm text-gray-800'>{$employee['leave_balance']}</td>
                                </tr>
                                ";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

</body>

</html>
