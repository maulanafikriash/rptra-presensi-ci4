<!-- Sidebar -->
<ul class="navbar-nav bg-primary sidebar sidebar-dark" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url(); ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="sidebar-brand-text">
            RPTRA <br> <span style="font-size: 0.8em;"><?= esc(session()->get('rptra_name') ?? '—'); ?></span>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <?php
    $session = \Config\Services::session();
    $role_id = $session->get('user_role_id');

    $menus = [
        1 => [ // Admin menu
            [
                'title' => 'Dashboard',
                'url' => 'admin/dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'section' => 'Admin'
            ],
            [
                'title' => 'Department',
                'url' => 'admin/master/department',
                'icon' => 'fas fa-building',
                'section' => 'Master'
            ],
            [
                'title' => 'Pegawai',
                'url' => 'admin/master/employee',
                'icon' => 'fas fa-users',
                'section' => 'Master'
            ],
            [
                'title' => 'Shift',
                'url' => 'admin/master/shift',
                'icon' => 'fas fa-clock',
                'section' => 'Master'
            ],
            [
                'title' => 'User Account',
                'url' => 'admin/master/user_account',
                'icon' => 'fas fa-user',
                'section' => 'Master'
            ],
            [
                'title' => 'Laporan Kehadiran',
                'url' => 'admin/report',
                'icon' => 'fas fa-file-alt',
                'section' => 'Laporan'
            ],
        ],
        2 => [ // Employee menu
            [
                'title' => 'Form Presensi',
                'url' => 'employee/attendance',
                'icon' => 'fas fa-calendar-check',
                'section' => 'Kehadiran'
            ],
            [
                'title' => 'Riwayat Presensi',
                'url' => 'employee/attendance_history',
                'icon' => 'fas fa-history',
                'section' => 'Kehadiran'
            ],
            [
                'title' => 'Jadwal Kerja',
                'url' => 'employee/work_schedule',
                'icon' => 'fas fa-calendar-alt',
                'section' => 'Kehadiran'
            ],
            [
                'title' => 'Profil Saya',
                'url' => 'employee/profile',
                'icon' => 'fas fa-id-badge',
                'section' => 'Profil'
            ],
            [
                'title' => 'Ubah Password',
                'url' => 'employee/change_password',
                'icon' => 'fas fa-lock',
                'section' => 'Profil'
            ],
        ],
    ];

    function renderMenu($menus, $currentPath)
    {
        $currentSection = '';
        foreach ($menus as $menu) {
            // Check if section has changed
            if ($menu['section'] !== $currentSection) {
                $currentSection = $menu['section'];
                echo '<div class="sidebar-heading">' . htmlspecialchars($currentSection) . '</div>';
            }

            // Determine if the current menu item is active
            $menuPath = parse_url(base_url($menu['url']), PHP_URL_PATH);
            $isActive = ($currentPath === $menuPath || strpos($currentPath, $menuPath . '/') === 0) ? 'active' : '';

            // Render the menu item
            echo '<li class="nav-item">';
            echo '<a class="nav-link ' . $isActive . '" href="' . base_url($menu['url']) . '">';
            echo '<i class="' . htmlspecialchars($menu['icon']) . '"></i>';
            echo '<span>' . htmlspecialchars($menu['title']) . '</span>';
            echo '</a>';
            echo '</li>';
        }
    }

    if (isset($menus[$role_id])) {
        // Get current URL path
        $currentPath = parse_url(current_url(), PHP_URL_PATH);
        renderMenu($menus[$role_id], $currentPath);
    }
    ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center ">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<?php if ($role_id == 2): ?>
    <div class="bottom-menu bg-light d-flex justify-content-around py-2" style="display: none;">
        <a href="<?= base_url('employee/attendance'); ?>" class="text-center <?= (current_url() == base_url('employee/attendance')) ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i><br>
            <small>Presensi</small>
        </a>
        <a href="<?= base_url('employee/attendance_history'); ?>" class="text-center <?= (current_url() == base_url('employee/attendance_history')) ? 'active' : ''; ?>">
            <i class="fas fa-history"></i><br>
            <small>Riwayat</small>
        </a>
        <a href="<?= base_url('employee/work_schedule'); ?>" class="text-center <?= (current_url() == base_url('employee/work_schedule')) ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i><br>
            <small>Jadwal</small>
        </a>
        <a href="<?= base_url('employee/profile'); ?>" class="text-center <?= (current_url() == base_url('employee/profile')) ? 'active' : ''; ?>">
            <i class="fas fa-user"></i><br>
            <small>Profil</small>
        </a>
        <a href="<?= base_url('employee/change_password'); ?>" class="text-center <?= (current_url() == base_url('employee/change_password')) ? 'active' : ''; ?>">
            <i class="fas fa-lock"></i><br>
            <small>Setting</small>
        </a>
    </div>
<?php endif; ?>

<style>
    /* Bottom menu small screens */
    <?php if ($role_id == 2): ?>@media (max-width: 500px) {
        .bottom-menu {
            display: flex !important;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 1000;
            border-top: 1px solid #ddd;
        }

        .bottom-menu a {
            color: #63625d;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .bottom-menu a i {
            font-size: 20px;
        }

        .bottom-menu a small {
            margin-top: -15px !important;
        }

        .bottom-menu a.active {
            color: #0d6efd;
            font-weight: bolder;
        }

        .sidebar {
            display: none;
        }
    }

    @media (min-width: 501px) {
        .sidebar {
            display: block !important;
        }

        .bottom-menu {
            display: none !important;
        }
    }

    <?php endif; ?>
    /* Side bar style */
    @media (min-width: 501px) {
        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: rgba(0, 123, 255, 0.2);
            font-weight: bold;
        }

        /* Mengubah ikon aktif menjadi putih */
        .sidebar .nav-link.active i {
            color: #ffffff;
        }
    }

    /* Sidebar Heading Styles */
    .sidebar-heading {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1rem;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        color: #ffffff;
    }

    .nav-item {
        margin-bottom: 0px;
        /* Kurangi jarak antar menu */
    }

    .nav-link {
        padding: 0px;
    }

    .nav-link i {
        margin-right: 0px;
    }

    .nav-link span {
        font-size: 0.9rem;
        padding-left: 5px;
    }
</style>