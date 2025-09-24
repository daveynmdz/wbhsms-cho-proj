<?php
// sidebar_admin.php
// Expected (optional) from caller: $activePage, $defaults['name'], $defaults['employee_number'], $employee_id
// This file does NOT open/close <html> or <body>.

// Get the absolute root path
$root_path = dirname(__DIR__);

if (session_status() === PHP_SESSION_NONE) {
    // Include employee session configuration using absolute path
    require_once $root_path . '/config/session/employee_session.php';
}

// Keep just the variable initialization
$activePage = $activePage ?? '';
$employee_id = $employee_id ?? ($_SESSION['employee_id'] ?? null);

// Initial display values from caller/session; will be refined from DB if needed.
$displayName = $defaults['name'] ?? ($_SESSION['employee_name'] ?? ($_SESSION['employee_first_name'] . ' ' . $_SESSION['employee_last_name']) ?? 'Admin');
$employeeNo = $defaults['employee_number'] ?? ($_SESSION['employee_number'] ?? '');
$role = $_SESSION['role'] ?? 'Admin';

// If we don't have good display values yet, pull from DB (only if we have an id)
$needsName = empty($displayName) || $displayName === 'Admin';
$needsNo = empty($employeeNo);

if (($needsName || $needsNo) && $employee_id) {
    // Ensure $conn exists; use absolute path for database config
    if (!isset($conn)) {
        require_once $root_path . '/config/db.php';
    }

    if (isset($conn)) {
        $stmt = $conn->prepare("
            SELECT employee_id, first_name, middle_name, last_name, employee_number, role
            FROM employees
            WHERE employee_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($needsName) {
                $parts = [];
                if (!empty($row['first_name'])) {
                    $parts[] = $row['first_name'];
                }
                if (!empty($row['middle_name'])) {
                    $parts[] = $row['middle_name'];
                }
                if (!empty($row['last_name'])) {
                    $parts[] = $row['last_name'];
                }
                $full = trim(implode(' ', $parts));
                $displayName = $full ?: 'Admin';
            }
            if ($needsNo && !empty($row['employee_number'])) {
                $employeeNo = $row['employee_number'];
            }
            if (!empty($row['role'])) {
                $role = $row['role'];
            }
        }
        $stmt->close();
    }
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<?php
// Calculate the correct paths based on the calling file's location
// Get the web root path relative to current script
$script_path = $_SERVER['SCRIPT_NAME'];
$web_root = str_repeat('../', substr_count(trim($script_path, '/'), '/'));

$cssPath = $web_root . 'assets/css/sidebar.css';
$vendorPath = $web_root . 'vendor/photo_controller.php';

// Determine navigation base path for admin pages
if (strpos($script_path, '/pages/management/') !== false) {
    $nav_base = '';
} else {
    $nav_base = 'pages/';
}
?>
<link rel="stylesheet" href="<?= $cssPath ?>">

<!-- Mobile topbar -->
<div class="mobile-topbar">
    <a href="<?= $nav_base ?>dashboard/dashboard_admin.php">
        <img id="topbarLogo" class="logo" src="https://ik.imagekit.io/wbhsmslogo/Nav_Logo.png?updatedAt=1750422462527" alt="City Health Logo" />
    </a>
</div>
<button class="mobile-toggle" onclick="toggleNav()" aria-label="Toggle Menu">
    <i id="menuIcon" class="fas fa-bars"></i>
</button>
<!-- Sidebar -->
<nav class="nav" id="sidebarNav" aria-label="Admin sidebar">
    <button class="close-btn" type="button" onclick="closeNav()" aria-label="Close navigation">
        <i class="fas fa-times"></i>
    </button>

        <a href="<?= $nav_base ?>management/admin/dashboard.php">
        <img id="topbarLogo" class="logo" src="https://ik.imagekit.io/wbhsmslogo/Nav_Logo.png?updatedAt=1750422462527" alt="City Health Logo" />
    </a>

    <div class="menu" role="menu">
        <a href="<?= $nav_base ?>management/admin/dashboard.php"
            class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="<?= $nav_base ?>management/admin/patient_records_management.php"
            class="<?= $activePage === 'patient_records' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-users"></i> Patient Records
        </a>
        <a href="<?= $nav_base ?>management/admin/appointments_management.php"
            class="<?= $activePage === 'appointments' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-calendar-check"></i> Appointments
        </a>
        <a href="<?= $nav_base ?>management/admin/employee_management.php"
            class="<?= $activePage === 'employees' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-user-tie"></i> Employee Management
        </a>
        <!-- These links are placeholders for pages not yet created -->
        <a href="#"
            class="<?= $activePage === 'clinical' ? 'active' : '' ?> disabled" role="menuitem">
            <i class="fas fa-stethoscope"></i> Clinical Records
        </a>
        <a href="#"
            class="<?= $activePage === 'laboratory' ? 'active' : '' ?> disabled" role="menuitem">
            <i class="fas fa-vials"></i> Laboratory
        </a>
        <a href="#"
            class="<?= $activePage === 'billing' ? 'active' : '' ?> disabled" role="menuitem">
            <i class="fas fa-file-invoice-dollar"></i> Billing Management
        </a>
        <a href="#"
            class="<?= $activePage === 'reports' ? 'active' : '' ?> disabled" role="menuitem">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="#"
            class="<?= $activePage === 'queueing' ? 'active' : '' ?> disabled" role="menuitem">
            <i class="fas fa-list-ol"></i> Queue Management
        </a>
        <a href="<?= $nav_base ?>management/admin/referrals_management.php"
            class="<?= $activePage === 'referrals' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-share"></i> Referrals
        </a>
        <a href="#"
            class="<?= $activePage === 'notifications' ? 'active' : '' ?> disabled" role="menuitem">
            <i class="fas fa-bell"></i> Notifications
        </a>
    </div>

    <a href="<?= $nav_base ?>user/admin_profile.php"
        class="<?= $activePage === 'profile' ? 'active' : '' ?>" aria-label="View profile">
        <div class="user-profile">
            <div class="user-info">
                <img class="user-profile-photo"
                    src="<?= $employee_id
                                ? $vendorPath . '?employee_id=' . urlencode((string)$employee_id)
                                : 'https://ik.imagekit.io/wbhsmslogo/user.png?updatedAt=1750423429172' ?>"
                    alt="User photo"
                    onerror="this.onerror=null;this.src='https://ik.imagekit.io/wbhsmslogo/user.png?updatedAt=1750423429172';">
                <div class="user-text">
                    <div class="user-name">
                        <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="user-id">
                        <i class="fas fa-id-badge" style="margin-right:5px;color:#90e0ef;"></i>: <span style="font-weight:500;"><?= htmlspecialchars($employeeNo, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="user-role" style="font-size:11px;color:#b3d9ff;margin-top:2px;">
                        <i class="fas fa-user-shield" style="margin-right:3px;"></i><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
                <span class="tooltip">View Profile</span>
            </div>
        </div>
    </a>

    <div class="user-actions">
        <a href="<?= $nav_base ?>user/admin_settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>

<!-- Hidden logout form -->
<form id="logoutForm" action="<?= $nav_base ?>management/auth/employee_logout.php" method="post" style="display:none;"></form>

<!-- Logout Modal (can be styled via your site-wide CSS) -->
<div id="logoutModal" class="modal-overlay" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="logoutTitle">
        <h2 id="logoutTitle">Sign Out</h2>
        <p>Are you sure you want to sign out?</p>
        <div class="modal-actions">
            <button type="button" onclick="confirmLogout()" class="btn btn-danger">Sign Out</button>
            <button type="button" onclick="closeLogoutModal()" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>

<!-- Optional overlay (if your layout uses it). Safe if duplicated; JS guards for missing element. -->
<div class="overlay" id="overlay" onclick="closeNav()"></div>

<script>
    function toggleNav() {
        const s = document.getElementById('sidebarNav');
        const o = document.getElementById('overlay');
        if (s) s.classList.toggle('open');
        if (o) o.classList.toggle('active');
    }

    function closeNav() {
        const s = document.getElementById('sidebarNav');
        const o = document.getElementById('overlay');
        if (s) s.classList.remove('open');
        if (o) o.classList.remove('active');
    }

    function showLogoutModal(e) {
        if (e) e.preventDefault();
        closeNav();
        const m = document.getElementById('logoutModal');
        if (m) m.style.display = 'flex';
    }

    function closeLogoutModal() {
        const m = document.getElementById('logoutModal');
        if (m) m.style.display = 'none';
    }

    function confirmLogout() {
        const f = document.getElementById('logoutForm');
        if (f) f.submit();
    }
</script>
