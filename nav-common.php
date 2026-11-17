<style>
i {
    border: solid #666666;
    border-width: 0 2px 2px 0;
    display: inline-block;
    padding: 3px;
    margin-left: 10px;
    float: right;
    margin-top: 5px;
}

.down {
    transform: rotate(45deg);
    -webkit-transform: rotate(45deg);
}

.dropbtn {
    background-color: white;
    color: #666666;
    font-size: 14px;
    border: none;
    margin: 18px 0px 0px 10px;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f1f1f1;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #ddd;
}

.dropdown:hover .dropdown-content {
    display: block;
}
</style>

<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$base_path = parse_url($site_base_url, PHP_URL_PATH) ?: '';
if ($base_path !== '' && ($uri === $base_path || strpos($uri, $base_path . '/') === 0)) {
    $uri = substr($uri, strlen($base_path)) ?: '/';
}

$cMI = 'class="current-menu-item"';

if (empty($_SESSION['sess_username'])) {
    // Not logged in
    ?>
    <li <?= $uri == '/about-us/' ? $cMI : '' ?>><a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/about-us/">About Us</a></li>
    <li <?= $uri == '/careers/' ? $cMI : '' ?>><a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/careers/">Careers</a></li>
    <li <?= $uri == '/insights/' ? $cMI : '' ?>><a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/insights/">Insights</a></li>
    <li <?= $uri == '/' ? $cMI : '' ?>><a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/#contact">Contact Us</a></li>
    <li class="login-link <?= $uri == '/login.php' ? 'current-menu-item' : '' ?>">
        <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/login.php">Login</a>
    </li>
    <?php
} else {
    // Logged in
    if (!defined('ENV')) {
        define('ENV', getenv('SERVER_NAME'));
    }

    $show_power_search_page = $show_trend_reports_page = $show_retrieval_services_page = true;

    $page_permission = getPagePermission();
    if (!empty($_SESSION['sess_search_page_permission'])) {
        $page_permission = $_SESSION['sess_search_page_permission'];
    }

    if (empty($page_permission) || !in_array('power_search', $page_permission)) {
        $show_power_search_page = false;
    }

    if (empty($page_permission) || !in_array('trend_reports', $page_permission)) {
        $show_trend_reports_page = false;
    }

    if (empty($page_permission) || !in_array('retrieval_services', $page_permission)) {
        $show_retrieval_services_page = false;
    }

    // Dashboards
    $show_digital_dashboard_page = !empty($page_permission) && in_array('digital_dashboard', $page_permission);
    $show_rpv_dashboard_page = !empty($page_permission) && in_array('rpv_dashboard', $page_permission);

    if ($show_power_search_page) { ?>
        <li <?= $uri == '/fullsearch.php?searchview=2' ? $cMI : '' ?>>
            <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/fullsearch.php?searchview=2">Power Search</a>
        </li>
    <?php }

    if ($show_trend_reports_page) { ?>
        <li <?= $uri == '/trend_reports.php' ? $cMI : '' ?>>
            <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/trend_reports.php">Trend Reports</a>
        </li>
    <?php }

    if ($show_retrieval_services_page) { ?>
        <li <?= $uri == '/productPickup.php' ? $cMI : '' ?>>
            <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/productPickup.php">Retrieval Services</a>
        </li>
    <?php }

    // DASHBOARD Dropdown
    if (!empty($_SESSION['sess_dashboard']) || $show_digital_dashboard_page || $show_rpv_dashboard_page) { ?>
        <div class="dropdown">
            <button class="dropbtn">DASHBOARD<i class="arrow down"></i></button>
            <div class="dropdown-content">
                <?php if (!empty($_SESSION['sess_dashboard'])) { ?>
                    <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/dashboard.php">Retail Energy Pricing Dashboard</a>
                <?php } ?>
                <?php if ($show_digital_dashboard_page) { ?>
                    <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/digital-dashboard.php">Digital Dashboard</a>
                <?php } ?>
                <?php if ($show_rpv_dashboard_page) { ?>
                    <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/rpv-dashboard.php">Relative Promotional Value Dashboard</a>
                <?php } ?>
            </div>
        </div>
    <?php }

    // PROFILE Dropdown
    ?>
    <div class="dropdown">
        <button class="dropbtn">PROFILE<i class="arrow down"></i></button>
        <div class="dropdown-content">
            <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/change_password.php">Change Password</a>
            <a href="<?= htmlspecialchars($site_base_url, ENT_QUOTES, 'UTF-8') ?>/logout.php">Logout</a>
        </div>
    </div>
<?php } ?>
