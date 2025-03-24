<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar bg-gray-800 text-white min-h-screen hidden md:flex flex-col justify-between w-max" id="sidebar">
    <div class="p-6">
        <h1 class="font-bold mb-8 text-white text-center hover:text-gray-300">
            <a href="index.php" class="flex flex-col">
                <span class="text-xl">BWCMONDUS- HR</span>
            </a>
        </h1>
        <ul class="flex flex-col gap-2">
            <?php
            $links = [
                'salary_cert.php' => ['Salary Certificate', 'fas fa-money-check-alt'],
                'noc_cert.php' => ['NOC Certificate', 'fas fa-file-alt'],
                'leave_balance.php' => ['Leave Balance', 'fas fa-calendar-check'],
            ];

            foreach ($links as $link => $data) {
                list($label, $icon) = $data;

                // Determine if the current page matches or starts with specific keywords
                $active = '';
                if ($current_page === $link) {
                    $active = 'bg-gray-700';
                }

                echo '<li>
                        <a href="' . $link . '" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 ' . $active . '">
                            <i class="' . $icon . ' mr-2"></i> ' . $label . '
                        </a>
                      </li>';
            }
            ?>
            <li>
                <a href="https://b24-oy9apg.bitrix24.com/bizproc/processes/25/view/0/" target="_blank" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    <i class="fas fa-calendar-check mr-2"></i> Leave Application
                </a>
            </li>
        </ul>
    </div>
    <div class="p-6 text-sm text-gray-400 text-center">
        ©<?php echo date('Y'); ?> - <a href="https://vortexweb.cloud/" target="_blank">VortexWeb</a>
    </div>
</div>

<div class="md:hidden flex flex-col justify-between w-max">
    <button id="toggleSidebar" class="p-4">
        <i id="menuIcon" class="fas fa-bars hover:text-gray-500"></i>
    </button>
</div>

<!-- Overlay for Mobile View -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden"></div>

<script>
    const toggleButton = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuIcon = document.getElementById('menuIcon');

    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('hidden'); // Toggle sidebar visibility
        overlay.classList.toggle('hidden'); // Toggle overlay visibility

        // Check the presence of the 'hidden' class and update the icon accordingly
        if (sidebar.classList.contains('hidden')) {
            menuIcon.classList.add('fa-bars');
            menuIcon.classList.remove('fa-times');
        } else {
            menuIcon.classList.add('fa-times');
            menuIcon.classList.remove('fa-bars');
        }
    });

    // Close sidebar when clicking outside of it
    overlay.addEventListener('click', () => {
        sidebar.classList.add('hidden');
        overlay.classList.add('hidden');
        menuIcon.classList.add('fa-bars');
        menuIcon.classList.remove('fa-times');
    });
</script>

<style>
    #overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    #sidebar:not(.hidden)+#overlay {
        display: block;
    }
</style>