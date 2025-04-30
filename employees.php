<?php
include('includes/header.php');
include('includes/components/sidebar.php');
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
                <table class="min-w-full table-auto" id="employeeTable">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">ID</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Name</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Email</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Position</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold uppercase">Phone</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700" id="employeeBody">
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="flex justify-center items-center mt-6 space-x-4" id="pagination"></div>
        </div>
    </div>

    <script>
        const API_URL = 'https://mondus.group/rest/1/dw9gd4xauhctd7ha/user.get';
        const EMPLOYEES_PER_PAGE = 10;

        let employees = [];
        let currentPage = 1;

        async function fetchEmployees() {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        select: ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'WORK_POSITION', 'PERSONAL_PHONE', 'PERSONAL_MOBILE', 'WORK_PHONE']
                    })
                });

                const data = await response.json();
                employees = data.result || [];
                renderTable();
                renderPagination();
            } catch (error) {
                console.error('Error fetching data:', error);
                document.getElementById('employeeBody').innerHTML = `
                    <tr>
                        <td colspan="5" class="py-6 text-center text-red-500">Failed to load employees.</td>
                    </tr>`;
            }
        }

        function renderTable() {
            const tbody = document.getElementById('employeeBody');
            tbody.innerHTML = '';

            const start = (currentPage - 1) * EMPLOYEES_PER_PAGE;
            const end = start + EMPLOYEES_PER_PAGE;
            const paginated = employees.slice(start, end);

            if (paginated.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-gray-500">No employees found.</td></tr>`;
                return;
            }

            paginated.forEach(emp => {
                const phone = emp.PERSONAL_PHONE || emp.PERSONAL_MOBILE || emp.WORK_PHONE || '-';
                const row = `
                    <tr class="border-b hover:bg-gray-50 transition duration-200">
                        <td class="py-3 px-6">${emp.ID}</td>
                        <td class="py-3 px-6">${emp.NAME} ${emp.LAST_NAME}</td>
                        <td class="py-3 px-6">${emp.EMAIL || '-'}</td>
                        <td class="py-3 px-6">${emp.WORK_POSITION || '-'}</td>
                        <td class="py-3 px-6">${phone}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(employees.length / EMPLOYEES_PER_PAGE);
            const container = document.getElementById('pagination');
            container.innerHTML = '';

            if (currentPage > 1) {
                container.innerHTML += `<button onclick="changePage(${currentPage - 1})" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Previous</button>`;
            }

            container.innerHTML += `<span class="px-4 py-2 bg-gray-200 rounded">Page ${currentPage} of ${totalPages}</span>`;

            if (currentPage < totalPages) {
                container.innerHTML += `<button onclick="changePage(${currentPage + 1})" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Next</button>`;
            }
        }

        function changePage(page) {
            currentPage = page;
            renderTable();
            renderPagination();
        }

        fetchEmployees();
    </script>
</body>

</html>