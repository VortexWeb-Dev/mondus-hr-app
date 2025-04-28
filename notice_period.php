<?php
include('includes/header.php');
include('includes/components/sidebar.php');
?>

<div class="p-10 flex-1 bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl mx-auto overflow-hidden flex flex-col">

        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col md:flex-row p-6 gap-6 overflow-y-auto">
            <!-- Notice Period Letter Section -->
            <div class="flex-1 p-6">
                <h3 class="text-xl font-semibold text-gray-800 text-center mb-4">Notice Period Letter</h3>
                <form action="download.php" method="post" class="space-y-4">
                    <!-- Hidden input for document type -->
                    <input type="hidden" name="documentType" value="notice_period">

                    <!-- Last Working Day -->
                    <div>
                        <label for="lastWorkingDay" class="block text-gray-600 text-sm font-medium">Last Working Day:</label>
                        <input required type="date" id="lastWorkingDay" name="lastWorkingDay"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <!-- Address To -->
                    <div>
                        <label for="addressTo" class="block text-gray-600 text-sm font-medium">Address To:</label>
                        <input required type="text" id="addressTo" name="addressTo" placeholder="Enter the recipient address"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <!-- Resignation Date -->
                    <div>
                        <label for="resignationDate" class="block text-gray-600 text-sm font-medium">Resignation Date:</label>
                        <input required type="date" id="resignationDate" name="resignationDate"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <!-- Notice Period Start Date -->
                    <div>
                        <label for="noticePeriodStartDate" class="block text-gray-600 text-sm font-medium">Notice Period Start Date:</label>
                        <input required type="date" id="noticePeriodStartDate" name="noticePeriodStartDate"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <!-- Submit Button -->
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
</script>

</body>
</html>
