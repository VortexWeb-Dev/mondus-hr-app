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
                    <label for="fullName" class="block text-gray-600 text-sm font-medium">Employee Full Name:</label>
                    <select id="fullName" name="fullName" class="w-full px-4 py-2 mt-1 border rounded-lg">

                    </select>
                </div>

                <div>
                    <label for="designation" class="block text-gray-600 text-sm font-medium">Position / Job Title:</label>
                    <select id="designation" name="designation" class="w-full px-4 py-2 mt-1 border rounded-lg">

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
                    <label for="dateOfIssue" class="block text-gray-600 text-sm font-medium">Letter Date:</label>
                    <input type="date" id="dateOfIssue" name="dateOfIssue" value="<?= date('Y-m-d') ?>" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- Current Salary -->
                <div>
                    <label for="currentSalary" class="block text-gray-600 text-sm font-medium">Current Salary (AED):</label>
                    <input type="number" id="currentSalary" name="currentSalary" placeholder="Enter your current salary"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                </div>

                <!-- Travel or Visa -->
                <div id="countryContainer" class="hidden">
                    <label for="country" class="block text-gray-600 text-sm font-medium">Country of Visa / Use:</label>
                    <input type="text" id="country" name="country" placeholder="e.g. IND / India"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <div>
                    <label for="templateType" class="block text-gray-600 text-sm font-medium">Select Template:</label>
                    <select id="templateType" name="templateType" required
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                        <option value="mondus_properties">Mondus Properties</option>
                        <option value="mondus_events">Mondus Events</option>
                        <option value="mondus_marketing">Mondus Marketing</option>
                        <option value="mondus_cft">Close Friends Traders</option>
                    </select>
                </div>

                <!-- NOC Reason -->
                <div>
                    <label for="nocReason" class="block text-gray-600 text-sm font-medium">NOC Reason:</label>
                    <select name="nocReason" id="nocReason" class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500" required>
                        <option value="">Select Reason</option>
                        <option value="travel">Travel</option>
                        <option value="visa">Visa Application</option>
                        <option value="mortgage">Mortgage Application</option>
                        <option value="credit">Credit Card Application</option>
                        <option value="debit">Debit Card Application</option>
                        <option value="bank">Bank Account Opening</option>
                        <option value="tenancy">Tenancy / Rental</option>
                        <option value="resignation">Job Change / Resignation</option>
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

                <!-- Debit Card or Credit Card or Bank Account -->
                <div id="bankNameContainer" class="hidden">
                    <label for="bankName" class="block text-gray-600 text-sm font-medium">Bank Name:</label>
                    <input type="text" id="bankName" name="bankName" placeholder="Enter Bank Name"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- Mortgage -->
                <div id="institutionNameContainer" class="hidden">
                    <label for="institutionName" class="block text-gray-600 text-sm font-medium">Institution Name:</label>
                    <input type="text" id="institutionName" name="institutionName" placeholder="Enter Institution Name"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- Tenancy -->
                <div id="propertyAddressContainer" class="hidden">
                    <label for="propertyAddress" class="block text-gray-600 text-sm font-medium">Property Address:</label>
                    <input type="text" id="propertyAddress" name="propertyAddress" placeholder="Enter Property Address"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit"
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
        const bankNameContainer = document.getElementById('bankNameContainer');
        const institutionNameContainer = document.getElementById('institutionNameContainer');
        const propertyAddressContainer = document.getElementById('propertyAddressContainer');
        const countryContainer = document.getElementById('countryContainer');

        nocReasonInput.addEventListener('change', function() {
            // Always hide all containers first
            startDateContainer.classList.add('hidden');
            endDateContainer.classList.add('hidden');
            bankNameContainer.classList.add('hidden');
            institutionNameContainer.classList.add('hidden');
            propertyAddressContainer.classList.add('hidden');
            countryContainer.classList.add('hidden');

            const selectedReason = nocReasonInput.value;

            if (selectedReason === 'travel') {
                startDateContainer.classList.remove('hidden');
                endDateContainer.classList.remove('hidden');
                countryContainer.classList.remove('hidden');
            } else if (['credit', 'debit', 'bank'].includes(selectedReason)) {
                bankNameContainer.classList.remove('hidden');
            } else if (selectedReason === 'mortgage') {
                institutionNameContainer.classList.remove('hidden');
            } else if (selectedReason === 'tenancy') {
                propertyAddressContainer.classList.remove('hidden');
            } else if (selectedReason === 'visa') {
                countryContainer.classList.remove('hidden');
            }
        });

        const fullNameSelect = document.getElementById('fullName');
        const designationSelect = document.getElementById('designation');

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
                    designationSelect.appendChild(option);
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