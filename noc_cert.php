<?php
include('includes/header.php');
include('includes/components/sidebar.php');

?>

<div class="p-10 flex-1 bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl mx-auto overflow-hidden flex flex-col">

        <div class="flex-1 p-6">
            <h3 class="text-xl font-semibold text-gray-800 text-center mb-6">No Objection Certificate (NOC)</h3>
            <form action="download.php" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <input type="hidden" name="documentType" value="noc">

                <div>
                    <label for="employeeName" class="block text-gray-600 text-sm font-medium">Employee Full Name:</label>
                    <select id="employeeName" name="employeeName" class="w-full px-4 py-2 mt-1 border rounded-lg">
                        <option value="">Select Employee</option>
                    </select>
                </div>

                <div>
                    <label for="jobTitle" class="block text-gray-600 text-sm font-medium">Position / Job Title:</label>
                    <select id="jobTitle" name="jobTitle" class="w-full px-4 py-2 mt-1 border rounded-lg">
                        <option value="">Select Job Title</option>
                    </select>
                </div>


                <!-- Date of Joining -->
                <div>
                    <label for="dateOfJoining" class="block text-gray-600 text-sm font-medium">Date of Joining:</label>
                    <input type="date" id="dateOfJoining" name="dateOfJoining" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- Letter Date -->
                <div>
                    <label for="letterDate" class="block text-gray-600 text-sm font-medium">Letter Date:</label>
                    <input type="date" id="letterDate" name="letterDate" value="<?= date('Y-m-d') ?>" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- Current Salary -->
                <div>
                    <label for="currentSalaryNoc" class="block text-gray-600 text-sm font-medium">Current Salary (AED):</label>
                    <input type="number" id="currentSalaryNoc" name="currentSalaryNoc" placeholder="Enter your current salary"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contactNumber" class="block text-gray-600 text-sm font-medium">Contact Number:</label>
                    <input type="tel" id="contactNumber" name="contactNumber" placeholder="Enter contact number"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                </div>

                <!-- Address To -->
                <div>
                    <label for="addressToNoc" class="block text-gray-600 text-sm font-medium">Address To:</label>
                    <input type="text" id="addressToNoc" name="addressToNoc" placeholder="Enter the recipient address"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-gray-600 text-sm font-medium">Country of Visa / Use:</label>
                    <input type="text" id="country" name="country" placeholder="e.g. IND / India"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- NOC Reason -->
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
                        <option value="tenancy_rental">Tenancy / Rental</option>
                        <option value="job_change_resignation">Job Change / Resignation</option>
                    </select>
                </div>

                <!-- Travel Dates (optional) -->
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

                <!-- Submit Button -->
                <div class="md:col-span-2 flex justify-end">
                    <button type="button"
                        class="bg-blue-600 text-white py-2 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Download
                    </button>
                </div>
            </form>
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

        const fullNameSelect = document.getElementById('employeeName');
        const jobTitleSelect = document.getElementById('jobTitle');

        async function fetchAllUsersBitrix(url) {
            let users = [];
            let start = 0;
            let hasMore = true;

            while (hasMore) {
                const response = await fetch(`${url}?start=${start}`);
                const data = await response.json();

                if (data.result && data.result.length) {
                    users = users.concat(data.result);
                }

                if (data.next !== undefined) {
                    start = data.next;
                } else {
                    hasMore = false;
                }
            }

            return users;
        }

        function populateDropdowns(users) {
            const namesAdded = new Set();
            const positionsAdded = new Set();

            users.forEach(user => {
                const fullName = `${user.NAME} ${user.LAST_NAME}`.trim();
                const position = user.WORK_POSITION?.trim();

                if (fullName && !namesAdded.has(fullName)) {
                    const option = document.createElement('option');
                    option.value = fullName;
                    option.textContent = fullName;
                    fullNameSelect.appendChild(option);
                    namesAdded.add(fullName);
                }

                if (position && !positionsAdded.has(position)) {
                    const option = document.createElement('option');
                    option.value = position;
                    option.textContent = position;
                    jobTitleSelect.appendChild(option);
                    positionsAdded.add(position);
                }
            });
        }

        // Call it on load
        document.addEventListener('DOMContentLoaded', async () => {
            const url = 'https://mondus.group/rest/1/dw9gd4xauhctd7ha/user.get';
            const users = await fetchAllUsersBitrix(url);
            populateDropdowns(users);
        });
    </script>
</div>

<?php include 'includes/footer.php'; ?>