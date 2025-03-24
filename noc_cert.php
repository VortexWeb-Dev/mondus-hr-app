<?php
include('includes/header.php');
include('includes/components/sidebar.php');
?>

<div class="p-10 flex-1 bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl mx-auto overflow-hidden flex flex-col">

        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col md:flex-row p-6 gap-6 overflow-y-auto">
            <!-- NOC Section with Travel Dates -->
            <div class="flex-1 p-6">
                <h3 class="text-xl font-semibold text-gray-800 text-center mb-4">No Objection Certificate (NOC)</h3>
                <form action="download.php" method="post" class="space-y-4">
                    <input type="hidden" name="documentType" value="noc">

                    <div>
                        <label for="currentSalaryNoc" class="block text-gray-600 text-sm font-medium">Current Salary:</label>
                        <input required type="number" id="currentSalaryNoc" name="currentSalaryNoc" placeholder="Enter your current salary"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label for="addressToNoc" class="block text-gray-600 text-sm font-medium">Address To:</label>
                        <input required type="text" id="addressToNoc" name="addressToNoc" placeholder="Enter the recipient address"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                    </div>

                    <div>
                        <label for="country" class="block text-gray-600 text-sm font-medium">Country:</label>
                        <input required type="text" id="country" name="country" placeholder="Enter NOC relevant country (if applicable)"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="nocReason" class="block text-gray-600 text-sm font-medium">NOC Reason:</label>
                        <select name="nocReason" id="nocReason" class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                            <option value="">Select Reason</option>
                            <option value="travel">Travel</option>
                            <option value="visa_application">Visa Application</option>
                            <option value="mortgage_application">Mortgage Application</option>
                            <option value="credit_card_application">Credit Card Application</option>
                            <option value="debit_card_application">Debit Card Application</option>
                            <option value="bank_account_opening">Bank Account Opening</option>
                        </select>
                    </div>

                    <div id="startDateContainer" class="hidden">
                        <label for="startDate" class="block text-gray-600 text-sm font-medium">Travel Start Date:</label>
                        <input type="date" id="startDate" name="startDate"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                    </div>

                    <div id="endDateContainer" class="hidden">
                        <label for="endDate" class="block text-gray-600 text-sm font-medium">Travel End Date:</label>
                        <input type="date" id="endDate" name="endDate"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
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

    <script>
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
</div>

<?php include 'includes/footer.php'; ?>