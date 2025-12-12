<style type="text/css">
  <!--
  @import url("theme/{$cfgTheme}/style/dhtml-horiz.css");
  -->
</style>
<!--[if gte IE 5.5]>
<script language="JavaScript" src="theme/{$cfgTheme}/style/dhtml.js" type="text/JavaScript"></script>
<![endif]-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
<style>

  :root {
    --sidebar-width: 250px;
    --sidebar-collapsed-width: 70px;
    --transition-speed: 0.3s;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    overflow-x: hidden;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
  }

  /* Sidebar Styling - FIXED SCROLLING */
  #sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    background: linear-gradient(180deg, #343a40 0%, #2c3136 100%);
    color: #fff;
    transition: all var(--transition-speed) ease;
    position: fixed;
    z-index: 1000;
    box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
  }

  #sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
  }

  /* Sidebar Header */
  .sidebar-header {
    padding: 20px 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    background: #343a40;
  }

  .sidebar-header h3 {
    font-size: 1.2rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
  }

  #sidebar.collapsed .sidebar-header h3 {
    display: none;
  }

  /* Sidebar Menu - FIXED SCROLLING */
  .sidebar-menu {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0;
  }

  /* Custom Scrollbar Styling */
  .sidebar-menu::-webkit-scrollbar {
    width: 8px;
  }

  .sidebar-menu::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
  }

  .sidebar-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
  }

  .sidebar-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
  }

  /* Firefox Scrollbar */
  .sidebar-menu {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.3) rgba(255, 255, 255, 0.1);
  }

  .sidebar-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .sidebar-menu li {
    position: relative;
  }

  .sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
    overflow: hidden;
    border-left: 3px solid transparent;
  }

  .sidebar-menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    border-left-color: #007bff;
  }

  .sidebar-menu a.active {
    background-color: rgba(0, 123, 255, 0.2);
    color: #fff;
    border-left: 3px solid #007bff;
  }

  .sidebar-menu i {
    font-size: 1.2rem;
    margin-right: 15px;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
  }

  #sidebar.collapsed .sidebar-menu span {
    display: none;
  }

  #sidebar.collapsed .sidebar-menu i {
    margin-right: 0;
  }

  /* Main Content Area */
  #content {
    margin-left: var(--sidebar-width);
    transition: all var(--transition-speed) ease;
    min-height: 100vh;
    padding: 20px;
  }

  #content.expanded {
    margin-left: var(--sidebar-collapsed-width);
  }

  /* Top Navigation Bar */
  .navbar {
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 10px 20px;
  }

  /* Toggle Button */
  #sidebarCollapse {
    background: transparent;
    border: none;
    color: #6c757d;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.2s;
    padding: 5px;
  }

  #sidebarCollapse:hover {
    color: #343a40;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
  }

  /* Dashboard Cards */
  .dashboard-card {
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    border: none;
    margin-bottom: 20px;
  }

  .dashboard-card:hover {
    transform: translateY(-5px);
  }

  .card-icon {
    font-size: 2rem;
    opacity: 0.7;
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {
    #sidebar {
      margin-left: calc(-1 * var(--sidebar-width));
    }

    #sidebar.active {
      margin-left: 0;
    }

    #content {
      margin-left: 0;
      width: 100%;
    }

    #content.active {
      margin-left: 0;
    }

    #sidebar.collapsed {
      width: var(--sidebar-width);
      margin-left: calc(-1 * var(--sidebar-width));
    }

    #sidebar.collapsed.active {
      margin-left: 0;
    }
  }</style>
</head>
<body>
<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar">
    <div class="sidebar-header">
      <h3>Admin Panel</h3>
      <button type="button" id="sidebarCollapse" class="btn">
        <i class="bi bi-list"></i>
      </button>
    </div>

    <div class="sidebar-menu">
      <ul>
        <li><a href="setting.php?modname=backup"><i class="bi bi-hdd-stack"></i> Manage Backup</a></li>
        <li><a href="setting.php?modname=carousel"><i class="bi bi-images"></i> Manage Carousel</a></li>
        <li><a href="setting.php?modname=block"><i class="bi bi-grid-3x3-gap-fill"></i> Manage Block</a></li>
        <li><a href="setting.php?modname=config"><i class="bi bi-gear"></i> Manage Config</a></li>
        <li><a href="setting.php?modname=contact"><i class="bi bi-person-lines-fill"></i> Manage Contact</a></li>
        <li><a href="setting.php?modname=content"><i class="bi bi-file-earmark-text"></i> Manage Content</a></li>
        <li><a href="setting.php?modname=explorer"><i class="bi bi-folder2-open"></i> Manage Explorer</a></li>
        <li><a href="setting.php?modname=info"><i class="bi bi-info-circle"></i> Manage Info</a></li>
        <li><a href="setting.php?modname=language"><i class="bi bi-translate"></i> Manage Language</a></li>
        <li><a href="setting.php?modname=member"><i class="bi bi-people-fill"></i> Manage Member</a></li>
        <li><a href="setting.php?modname=menu"><i class="bi bi-list"></i> Manage Menu</a></li>
        <li><a href="setting.php?modname=module"><i class="bi bi-puzzle"></i> Manage Module</a></li>
        <li><a href="setting.php?modname=news"><i class="bi bi-newspaper"></i> Manage News</a></li>
        <li><a href="setting.php?modname=poll"><i class="bi bi-bar-chart"></i> Manage Poll</a></li>
        <li><a href="setting.php?modname=rssthai"><i class="bi bi-rss"></i> Manage Rssthai</a></li>
        <li><a href="setting.php?modname=theme"><i class="bi bi-palette-fill"></i> Manage Theme</a></li>

      </ul>
    </div>
  </nav>

  <!-- Page Content -->
  <div id="content">
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid">
        <button type="button" id="sidebarCollapseMobile" class="btn d-lg-none">
          <i class="bi bi-list"></i>
        </button>

        <div class="d-flex align-items-center ms-auto">
          <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-2"></i>Admin User
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="setting.php?modname=setting"><i class="bi bi-gear me-2"></i>Settings</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="index.php" target="_blank">
                  <i class="bi bi-house me-2"></i>Home
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="module.php?modname=member&mf=memlogout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">




      <!-- Charts and Tables Section -->
      <div class="row">
        <div class="col-lg-12 mb-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3">

            </div>
            <div class="card-body">



              <div>{$setModule}</div>



            </div>
          </div>
        </div>


      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebarCollapseMobile = document.getElementById('sidebarCollapseMobile');

    // Toggle sidebar on button click
    sidebarCollapse.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
      content.classList.toggle('expanded');
    });

    // Mobile sidebar toggle
    sidebarCollapseMobile.addEventListener('click', function() {
      sidebar.classList.toggle('active');
      content.classList.toggle('active');
    });

    // Close sidebar when clicking on a menu item on mobile
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    menuItems.forEach(item => {
      item.addEventListener('click', function() {
        if (window.innerWidth < 768) {
          sidebar.classList.remove('active');
          content.classList.remove('active');
        }
      });
    });
  });

  sidebarCollapseMobile.addEventListener('click', function() {
    sidebar.classList.toggle('active');
    // Removed content.classList.toggle('active') to avoid conflicts
  });
</script>
</body>
</html>