<?php
// sidebar_patient.php
// Expected (optional) from caller: $activePage, $defaults['name'], $defaults['patient_number'], $patient_id
// This file does NOT open/close <html> or <body>.

// Get the absolute root path
$root_path = dirname(__DIR__);

if (session_status() === PHP_SESSION_NONE) {
    // Include patient session configuration using absolute path
    require_once $root_path . '/config/session/patient_session.php';
}

$activePage = $activePage ?? '';
$patient_id = $patient_id ?? ($_SESSION['patient_id'] ?? null);

// Initial display values from session first, then caller defaults if available
$displayName = $_SESSION['patient_name'] ?? ($defaults['name'] ?? 'Patient');
$patientNo   = $_SESSION['patient_number'] ?? ($defaults['patient_number'] ?? '');

// If we don't have good display values yet, pull from DB (only if we have an id)
$needsName = empty($displayName) || $displayName === 'Patient';
$needsNo   = empty($patientNo);

if (($needsName || $needsNo) && $patient_id) {
    // Ensure $pdo exists; use absolute path for database config
    if (!isset($pdo)) {
        require_once $root_path . '/config/db.php';
    }

    if (isset($pdo)) {
        $stmt = $pdo->prepare("
            SELECT patient_id as id, first_name, middle_name, last_name, suffix, username
            FROM patients
            WHERE patient_id = ?
            LIMIT 1
        ");
        $stmt->execute([$patient_id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                if (!empty($row['suffix'])) {
                    $full .= ' ' . $row['suffix'];
                }
                $displayName = $full ?: 'Patient';
            }
            if ($needsNo && !empty($row['username'])) {
                $patientNo = $row['username'];
            }
        }
    }
}

// Calculate the correct paths based on the calling file's location
// Get the web root path relative to current script
$script_path = $_SERVER['SCRIPT_NAME'];
$web_root = str_repeat('../', substr_count(trim($script_path, '/'), '/'));

$assets_path = $web_root . 'assets/css/sidebar.css';
$vendor_path = $web_root . 'vendor/photo_controller.php';

// Determine navigation base path
if (strpos($script_path, '/pages/patient/profile/') !== false) {
    $nav_base = '../';
} elseif (strpos($script_path, '/pages/patient/') !== false) {
    $nav_base = '';
} elseif (strpos($script_path, '/pages/') !== false) {
    $nav_base = 'patient/';
} else {
    $nav_base = 'pages/patient/';
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= $assets_path ?>">

<!-- Mobile topbar -->
<div class="mobile-topbar">
    <a href="<?= $nav_base ?>dashboard.php">
        <img id="topbarLogo" class="logo" src="https://ik.imagekit.io/wbhsmslogo/Nav_Logo.png?updatedAt=1750422462527" alt="City Health Logo" />
    </a>
</div>
<button class="mobile-toggle" onclick="toggleNav()" aria-label="Toggle Menu">
    <i id="menuIcon" class="fas fa-bars"></i>
</button>
<!-- Sidebar -->
<nav class="nav" id="sidebarNav" aria-label="Patient sidebar">
    <button class="close-btn" type="button" onclick="closeNav()" aria-label="Close navigation">
        <i class="fas fa-times"></i>
    </button>

    <a href="<?= $nav_base ?>dashboard.php">
        <img id="topbarLogo" class="logo" src="https://ik.imagekit.io/wbhsmslogo/Nav_Logo.png?updatedAt=1750422462527" alt="City Health Logo" />
    </a>

    <div class="menu" role="menu">
        <a href="<?= $nav_base ?>dashboard.php"
            class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="<?= $nav_base ?>appointment/appointments.php"
            class="<?= $activePage === 'appointments' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-calendar-check"></i> Appointments & Referrals
        </a>
        <a href="<?= $nav_base ?>prescription/prescriptions.php"
            class="<?= $activePage === 'prescription' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-prescription-bottle-alt"></i> Prescription
        </a>
        <a href="<?= $nav_base ?>laboratory/lab_tests.php"
            class="<?= $activePage === 'laboratory' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-vials"></i> Laboratory
        </a>
        <a href="<?= $nav_base ?>billing/billing.php"
            class="<?= $activePage === 'billing' ? 'active' : '' ?>" role="menuitem">
            <i class="fas fa-file-invoice-dollar"></i> Billing
        </a>
    </div>

    <a href="<?= $nav_base ?>profile/profile.php"
        class="<?= $activePage === 'profile' ? 'active' : '' ?>" aria-label="View profile">
        <div class="user-profile">
            <div class="user-info">
                <img class="user-profile-photo"
                    src="<?= $patient_id
                                ? $vendor_path . '?patient_id=' . urlencode((string)$patient_id)
                                : 'https://ik.imagekit.io/wbhsmslogo/user.png?updatedAt=1750423429172' ?>"
                    alt="User photo"
                    onerror="this.onerror=null;this.src='https://ik.imagekit.io/wbhsmslogo/user.png?updatedAt=1750423429172';">
                <div class="user-text">
                    <div class="user-name">
                        <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="user-id">
                        <i class="fas fa-id-card" style="margin-right:5px;color:#90e0ef;"></i>: <span style="font-weight:500;"><?= htmlspecialchars($patientNo, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
                <span class="tooltip">View Profile</span>
            </div>
        </div>
    </a>

    <div class="user-actions">
        <a href="<?= $nav_base ?>user_settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>

<!-- Hidden logout form -->
<form id="logoutForm" action="<?= $nav_base ?>auth/patient_logout.php" method="post" style="display:none;"></form>

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