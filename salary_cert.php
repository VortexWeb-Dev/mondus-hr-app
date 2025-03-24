<?php
include('includes/header.php');
include('includes/components/sidebar.php');
?>

<div class="p-10 flex-1 bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl mx-auto overflow-hidden flex flex-col">

        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col md:flex-row p-6 gap-6 overflow-y-auto">
            <!-- Salary Certificate Section -->
            <div class="flex-1 p-6">
                <h3 class="text-xl font-semibold text-gray-800 text-center mb-4">Salary Certificate</h3>
                <form action="download.php" method="post" class="space-y-4">
                    <input type="hidden" name="documentType" value="salary_certificate">

                    <div>
                        <label for="currentSalary" class="block text-gray-600 text-sm font-medium">Current Salary:</label>
                        <input required type="number" id="currentSalary" name="currentSalary" placeholder="Enter your current salary"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label for="addressTo" class="block text-gray-600 text-sm font-medium">Address To:</label>
                        <input required type="text" id="addressTo" name="addressTo" placeholder="Enter the recipient address"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="self-center bg-blue-600 text-white py-2 px-4 mt-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Download
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>


<script>
    // Set current year in footer
    document.getElementById("year").textContent = new Date().getFullYear();

    const nocReasonInput = document.getElementById('nocReason');
    const startDateContainer = document.getElementById('startDateContainer');
    const endDateContainer = document.getElementById('endDateContainer');

    nocReasonInput.addEventListener('change', function() {
        const selectedReason = nocReasonInput.value;
        if (selectedReason === 'travel') {
            startDateContainer.classList.remove('hidden');
            endDateContainer.classList.remove('hidden');
        } else {
            startDateContainer.classList.add('hidden');
            endDateContainer.classList.add('hidden');
        }
    });
</script>

</body>

</html>