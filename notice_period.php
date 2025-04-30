<?php
include('includes/header.php');
include('includes/components/sidebar.php');

require_once 'crest/crest.php';
?>

<div class="p-10 flex-1 bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl mx-auto overflow-hidden flex flex-col">

        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col md:flex-row p-6 gap-6 overflow-y-auto">
            <!-- Notice Period Letter Section -->
            <div class="flex-1 p-6">
                <h3 class="text-xl font-semibold text-gray-800 text-center mb-4">Notice Period Letter</h3>
                <form action="download.php" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="documentType" value="notice">

                    <!-- Employee Name Dropdown -->
                    <div>
                        <label for="fullName" class="block text-gray-600 text-sm font-medium">Employee Name:</label>
                        <select name="fullName" id="fullName" class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                            <!-- Options will be populated using JavaScript -->
                        </select>
                    </div>

                    <!-- Designation Dropdown -->
                    <div>
                        <label for="designation" class="block text-gray-600 text-sm font-medium">Designation / Job Title:</label>
                        <select id="designation" name="designation" class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                            <!-- Populated dynamically -->
                        </select>
                    </div>

                    <!-- Notice Period Duration -->
                    <!-- <div>
                        <label for="noticePeriodDuration" class="block text-gray-600 text-sm font-medium">Notice Period Duration:</label>
                        <input type="number" id="noticePeriodDuration" name="noticePeriodDuration" placeholder="Enter Notice Period Duration"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                    </div> -->

                    <!-- Contact Details of Employee -->
                    <!-- <div>
                        <label for="contactDetails" class="block text-gray-600 text-sm font-medium">Contact Details of Employee:</label>
                        <input type="number" id="contactDetails" name="contactDetails" placeholder="Enter Contact Number"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                    </div> -->

                    <!-- Last Working Day -->
                    <div>
                        <label for="lastWorkingDay" class="block text-gray-600 text-sm font-medium">Last Working Day:</label>
                        <input required type="date" id="lastWorkingDay" name="lastWorkingDay"
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

                    <!-- Address To -->
                    <div class="md:col-span-2">
                        <label for="addressTo" class="block text-gray-600 text-sm font-medium">Address To:</label>
                        <input required type="text" id="addressTo" name="addressTo" placeholder="Enter the recipient address"
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                    </div>

                    <!-- Template Type -->
                    <div class="md:col-span-2">
                        <label for="templateType" class="block text-gray-600 text-sm font-medium">Select Template:</label>
                        <select id="templateType" name="templateType" required
                            class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
                            <option value="mondus_properties">Mondus Properties</option>
                            <option value="mondus_events">Mondus Events</option>
                            <option value="mondus_marketing">Mondus Marketing</option>
                            <option value="mondus_cft">Close Friends Traders</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit"
                            class="self-center bg-blue-600 text-white py-2 px-4 mt-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Download
                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>
</div>


<script>
    const webhookUrl = "https://mondus.group/rest/1/dw9gd4xauhctd7ha/";

    window.onload = async function() {

        const fullNameSelect = document.getElementById("fullName");
        const designationSelect = document.getElementById("designation");
        const joiningDateInput = document.getElementById("joiningDate");

        const users = await fetchAllUsers();
        const designationsSet = new Set();

        users.forEach(user => {
            // Add to full name dropdown
            const option = document.createElement("option");
            option.value = `${user.NAME} ${user.LAST_NAME}`.trim();
            option.textContent = `${user.NAME} ${user.LAST_NAME}`.trim();
            option.dataset.position = user.WORK_POSITION || '';
            option.dataset.joining = user.DATE_REGISTER ? user.DATE_REGISTER.split("T")[0] : '';
            fullNameSelect.appendChild(option);

            // Collect unique designations
            if (user.WORK_POSITION) {
                designationsSet.add(user.WORK_POSITION);
            }
        });

        // Populate designations dropdown
        Array.from(designationsSet).sort().forEach(position => {
            const opt = document.createElement("option");
            opt.value = position;
            opt.textContent = position;
            designationSelect.appendChild(opt);
        });

        // Auto-select designation & joining date based on selected employee
        fullNameSelect.addEventListener("change", function() {
            const selectedOption = fullNameSelect.options[fullNameSelect.selectedIndex];
            designationSelect.value = selectedOption.dataset.position;
            joiningDateInput.value = selectedOption.dataset.joining;
        });
    };

    async function fetchAllUsers(start = 0, users = []) {
        const response = await fetch(`${webhookUrl}user.get.json?start=${start}`);
        const data = await response.json();

        if (data.result) {
            users = users.concat(data.result);
        }

        if (data.next !== undefined) {
            return fetchAllUsers(data.next, users);
        }

        return users;
    }
</script>

</body>

</html>