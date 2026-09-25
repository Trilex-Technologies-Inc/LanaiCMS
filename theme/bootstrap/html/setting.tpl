<link rel="stylesheet" href="assets/vendor/bootstrap-icons/font/bootstrap-icons.css">
<style>
  :root { --settings-sidebar: 264px; --settings-sidebar-collapsed: 72px; --border: #e5e7eb; --muted: #6b7280; --text: #202123; }
  body { margin: 0; overflow-x: hidden; background: #fff; color: var(--text); font-family: Inter, ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
  .settings-shell { min-height: 100vh; background: #fff; }
  .settings-sidebar { position: fixed; inset: 0 auto 0 0; z-index: 1040; display: flex; width: var(--settings-sidebar); flex-direction: column; border-right: 1px solid var(--border); background: #f9fafb; transition: transform 180ms ease; }
  .settings-sidebar-header { display: flex; min-height: 64px; align-items: center; gap: 8px; padding: 0 12px 0 18px; border-bottom: 1px solid var(--border); }
  .settings-brand { display: flex; min-width: 0; flex: 1; align-items: center; gap: 11px; color: var(--text); font-size: 15px; font-weight: 650; text-decoration: none; white-space: nowrap; }
  .settings-brand:hover { color: var(--text); }
  .settings-brand-mark { display: grid; width: 30px; height: 30px; place-items: center; border-radius: 8px; background: #111827; color: #fff; font-size: 15px; }
  .settings-collapse-button { display: grid; width: 32px; height: 32px; flex: 0 0 32px; padding: 0; place-items: center; border: 0; border-radius: 6px; background: transparent; color: #6b7280; }
  .settings-collapse-button:hover { background: #ececf1; color: #111827; }
  .settings-nav { flex: 1; overflow-y: auto; padding: 18px 12px; scrollbar-width: thin; }
  .settings-nav-section + .settings-nav-section { margin-top: 22px; }
  .settings-nav-label { margin: 0 10px 7px; color: #9ca3af; font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; }
  .settings-nav-list { margin: 0; padding: 0; list-style: none; }
  .settings-nav-link { display: flex; min-height: 38px; align-items: center; gap: 11px; margin: 2px 0; padding: 8px 10px; border-radius: 7px; color: #4b5563; font-size: 14px; font-weight: 500; line-height: 1.2; text-decoration: none; }
  .settings-nav-link i { width: 18px; color: #6b7280; font-size: 16px; text-align: center; }
  .settings-nav-link:hover { background: #f3f4f6; color: #111827; }
  .settings-nav-link.active { background: #ececf1; color: #111827; font-weight: 600; }
  .settings-nav-link.active i { color: #111827; }
  .settings-sidebar-footer { padding: 12px; border-top: 1px solid var(--border); }
  .settings-sidebar-footer .settings-nav-link { margin: 0; }
  .settings-main { min-height: 100vh; margin-left: var(--settings-sidebar); }
  .settings-sidebar, .settings-main { transition: width 180ms ease, margin-left 180ms ease, transform 180ms ease; }
  .settings-shell.sidebar-collapsed .settings-sidebar { width: var(--settings-sidebar-collapsed); }
  .settings-shell.sidebar-collapsed .settings-main { margin-left: var(--settings-sidebar-collapsed); }
  .settings-shell.sidebar-collapsed .settings-sidebar-header { justify-content: center; padding-inline: 0; }
  .settings-shell.sidebar-collapsed .settings-brand { display: none; }
  .settings-shell.sidebar-collapsed .settings-nav { padding-inline: 10px; }
  .settings-shell.sidebar-collapsed .settings-nav-label,
  .settings-shell.sidebar-collapsed .settings-nav-link span { display: none; }
  .settings-shell.sidebar-collapsed .settings-nav-section + .settings-nav-section { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border); }
  .settings-shell.sidebar-collapsed .settings-nav-link { justify-content: center; gap: 0; padding-inline: 0; }
  .settings-shell.sidebar-collapsed .settings-nav-link i { width: auto; font-size: 18px; }
  .settings-shell.sidebar-collapsed .settings-sidebar-footer { padding-inline: 10px; }
  .settings-topbar { display: flex; min-height: 64px; align-items: center; gap: 12px; padding: 0 24px; border-bottom: 1px solid var(--border); background: #fff; }
  .settings-mobile-controls { display: none; align-items: center; gap: 12px; }
  .settings-menu-button { display: grid; width: 36px; height: 36px; padding: 0; place-items: center; border: 1px solid var(--border); border-radius: 7px; background: #fff; color: #374151; }
  .settings-mobile-title { font-size: 15px; font-weight: 650; }
  .settings-user { margin-left: auto; }
  .settings-user-button { display: flex; align-items: center; gap: 9px; padding: 7px 10px; border: 1px solid transparent; border-radius: 8px; background: transparent; color: #374151; font-size: 14px; font-weight: 550; }
  .settings-user-button:hover, .settings-user-button[aria-expanded="true"] { border-color: var(--border); background: #f9fafb; }
  .settings-user-button .bi-person-circle { font-size: 19px; }
  .settings-user .dropdown-menu { min-width: 190px; padding: 6px; border-color: var(--border); border-radius: 9px; box-shadow: 0 12px 28px rgba(17, 24, 39, .12); }
  .settings-user .dropdown-item { padding: 8px 10px; border-radius: 6px; font-size: 14px; }
  .settings-workspace { width: min(100%, 1180px); padding: 42px 48px 72px; }
  .settings-page-heading { margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
  .settings-page-heading h1 { margin: 0; color: #111827; font-size: 24px; font-weight: 650; letter-spacing: -.02em; }
  .settings-page-heading p { margin: 7px 0 0; color: var(--muted); font-size: 14px; }
  .settings-module { min-width: 0; font-size: 14px; }
  .settings-module .txtContentTitle { display: inline-block; margin-bottom: 12px; color: #111827; font-size: 20px; font-weight: 650; }
  .settings-module table { max-width: 100%; }
  .legacy-action-icon { display: inline-block; min-width: 1.15em; margin-right: 4px; color: #4b5563; font-size: 1rem; line-height: 1; vertical-align: -.125em; }
  a:hover > .legacy-action-icon, a:hover + .legacy-action-icon { color: #111827; }
  .legacy-action-icon.bi-trash, .legacy-action-icon.bi-x-circle { color: #dc3545; }
  .legacy-action-icon.bi-check-circle, .legacy-action-icon.bi-check-circle-fill { color: #198754; }
  .settings-empty { max-width: 620px; padding: 28px; border: 1px solid var(--border); border-radius: 10px; background: #fafafa; color: var(--muted); }
  .settings-empty h2 { margin: 0 0 8px; color: #111827; font-size: 18px; font-weight: 650; }
  .analytics-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 18px; }
  .analytics-card, .analytics-panel { border: 1px solid var(--border); border-radius: 10px; background: #fff; }
  .analytics-card { padding: 20px; }
  .analytics-card span { display: block; color: var(--muted); font-size: 13px; }
  .analytics-card strong { display: block; margin-top: 5px; color: #111827; font-size: 28px; font-weight: 650; }
  .analytics-panel { padding: 20px; margin-bottom: 18px; }
  .analytics-panel h2 { margin: 0 0 18px; color: #111827; font-size: 16px; font-weight: 650; }
  .analytics-panel h2 small { color: var(--muted); font-size: 12px; font-weight: 400; }
  .analytics-columns { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
  .analytics-chart { display: flex; height: 155px; align-items: flex-end; gap: 12px; }
  .analytics-bar-column { display: flex; height: 100%; flex: 1; flex-direction: column; justify-content: flex-end; text-align: center; }
  .analytics-bar { min-height: 6px; border-radius: 5px 5px 2px 2px; background: #10a37f; }
  .analytics-bar-column small { margin-top: 7px; color: var(--muted); }
  .analytics-muted { color: var(--muted); }
  .settings-overlay { position: fixed; inset: 0; z-index: 1030; display: none; background: rgba(17, 24, 39, .35); }
  @media (max-width: 900px) {
    .settings-sidebar { transform: translateX(-100%); box-shadow: 12px 0 30px rgba(17, 24, 39, .12); }
    .settings-sidebar.open { transform: translateX(0); }
    .settings-overlay.open { display: block; }
    .settings-main { margin-left: 0; }
    .settings-shell.sidebar-collapsed .settings-sidebar { width: var(--settings-sidebar); }
    .settings-shell.sidebar-collapsed .settings-main { margin-left: 0; }
    .settings-shell.sidebar-collapsed .settings-sidebar-header { justify-content: flex-start; padding: 0 12px 0 18px; }
    .settings-shell.sidebar-collapsed .settings-brand { display: flex; }
    .settings-shell.sidebar-collapsed .settings-collapse-button { display: none; }
    .settings-shell.sidebar-collapsed .settings-nav { padding-inline: 12px; }
    .settings-shell.sidebar-collapsed .settings-nav-label,
    .settings-shell.sidebar-collapsed .settings-nav-link span { display: initial; }
    .settings-shell.sidebar-collapsed .settings-nav-section + .settings-nav-section { margin-top: 22px; padding-top: 0; border-top: 0; }
    .settings-shell.sidebar-collapsed .settings-nav-link { justify-content: flex-start; gap: 11px; padding-inline: 10px; }
    .settings-shell.sidebar-collapsed .settings-nav-link i { width: 18px; font-size: 16px; }
    .settings-collapse-button { display: none; }
    .settings-topbar { min-height: 58px; padding-inline: 18px; }
    .settings-mobile-controls { display: flex; }
    .settings-workspace { padding: 28px 22px 56px; }
    .analytics-grid, .analytics-columns { grid-template-columns: 1fr; }
  }
  @media (max-width: 520px) { .settings-workspace { padding-inline: 16px; } .settings-page-heading h1 { font-size: 21px; } }
</style>

<div class="settings-shell">
  <aside class="settings-sidebar" id="settingsSidebar" aria-label="Settings navigation">
    <div class="settings-sidebar-header">
      <a class="settings-brand" href="setting.php"><span class="settings-brand-mark">L</span><span>Lanai settings</span></a>
      <button class="settings-collapse-button" id="settingsCollapseButton" type="button" aria-label="Collapse settings menu" aria-expanded="true" title="Collapse menu"><i class="bi bi-layout-sidebar"></i></button>
    </div>
    <nav class="settings-nav">
      <section class="settings-nav-section">
        <p class="settings-nav-label">Content</p>
        <ul class="settings-nav-list">
          <li><a class="settings-nav-link" data-module="content" href="setting.php?modname=content"><i class="bi bi-file-earmark-text"></i><span>Content</span></a></li>
          <li><a class="settings-nav-link" data-module="ctype" href="setting.php?modname=ctype"><i class="bi bi-diagram-3"></i><span>Content types</span></a></li>
          <li><a class="settings-nav-link" data-module="media" href="setting.php?modname=media"><i class="bi bi-images"></i><span>Media</span></a></li>
          <li><a class="settings-nav-link" data-module="carousel" href="setting.php?modname=carousel"><i class="bi bi-collection"></i><span>Carousel</span></a></li>
          <li><a class="settings-nav-link" data-module="contact" href="setting.php?modname=contact"><i class="bi bi-person-lines-fill"></i><span>Contacts</span></a></li>
          <li><a class="settings-nav-link" data-module="poll" href="setting.php?modname=poll"><i class="bi bi-bar-chart"></i><span>Polls</span></a></li>
        </ul>
      </section>
      <section class="settings-nav-section">
        <p class="settings-nav-label">Site</p>
        <ul class="settings-nav-list">
          <li><a class="settings-nav-link" data-module="menu" href="setting.php?modname=menu"><i class="bi bi-list"></i><span>Menus</span></a></li>
          <li><a class="settings-nav-link" data-module="block" href="setting.php?modname=block"><i class="bi bi-grid-3x3-gap"></i><span>Blocks</span></a></li>
          <li><a class="settings-nav-link" data-module="theme" href="setting.php?modname=theme"><i class="bi bi-palette"></i><span>Theme</span></a></li>
          <li><a class="settings-nav-link" data-module="language" href="setting.php?modname=language"><i class="bi bi-translate"></i><span>Language</span></a></li>
          <li><a class="settings-nav-link" data-module="config" href="setting.php?modname=config"><i class="bi bi-sliders"></i><span>Configuration</span></a></li>
        </ul>
      </section>
      <section class="settings-nav-section">
        <p class="settings-nav-label">Workspace</p>
        <ul class="settings-nav-list">
          <li><a class="settings-nav-link" data-module="member" href="setting.php?modname=member"><i class="bi bi-people"></i><span>Members</span></a></li>
          <li><a class="settings-nav-link" data-module="role" href="setting.php?modname=role"><i class="bi bi-person-badge"></i><span>Roles</span></a></li>
          <li><a class="settings-nav-link" data-module="apitoken" href="setting.php?modname=apitoken"><i class="bi bi-key"></i><span>API tokens</span></a></li>
          <li><a class="settings-nav-link" data-module="module" href="setting.php?modname=module"><i class="bi bi-puzzle"></i><span>Modules</span></a></li>
        </ul>
      </section>
      <section class="settings-nav-section">
        <p class="settings-nav-label">System</p>
        <ul class="settings-nav-list">
          <li><a class="settings-nav-link" data-module="backup" href="setting.php?modname=backup"><i class="bi bi-hdd-stack"></i><span>Backup</span></a></li>
          <li><a class="settings-nav-link" data-module="explorer" href="setting.php?modname=explorer"><i class="bi bi-folder2-open"></i><span>File explorer</span></a></li>
          <li><a class="settings-nav-link" data-module="info" href="setting.php?modname=info"><i class="bi bi-info-circle"></i><span>System info</span></a></li>
          <li><a class="settings-nav-link" data-module="statistics" href="setting.php?modname=statistics"><i class="bi bi-graph-up"></i><span>Statistics</span></a></li>
        </ul>
      </section>
    </nav>
    <div class="settings-sidebar-footer">
      <a class="settings-nav-link" href="index.php" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i><span>View site</span></a>
      <a class="settings-nav-link" href="module.php?modname=member&amp;mf=memlogout"><i class="bi bi-box-arrow-right"></i><span>Log out</span></a>
    </div>
  </aside>

  <div class="settings-overlay" id="settingsOverlay"></div>
  <main class="settings-main">
    <header class="settings-topbar">
      <div class="settings-mobile-controls">
        <button class="settings-menu-button" id="settingsMenuButton" type="button" aria-label="Open settings menu" aria-expanded="false"><i class="bi bi-list"></i></button>
        <span class="settings-mobile-title">Lanai settings</span>
      </div>
      <div class="dropdown settings-user">
        <button class="settings-user-button dropdown-toggle" type="button" id="settingsUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle"></i><span>{$settingUserName}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsUserMenu">
          <li><a class="dropdown-item" href="setting.php?modname=member"><i class="bi bi-person me-2"></i>Members</a></li>
          <li><a class="dropdown-item" href="index.php" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-2"></i>View site</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="module.php?modname=member&amp;mf=memlogout"><i class="bi bi-box-arrow-right me-2"></i>Log out</a></li>
        </ul>
      </div>
    </header>
    <div class="settings-workspace">
      <header class="settings-page-heading">
        <h1 id="settingsPageTitle">Settings</h1>
        <p id="settingsPageDescription">Manage your LanaiCMS site and workspace.</p>
      </header>
      <section class="settings-module">
        <div class="settings-empty" id="settingsEmpty"><h2>Choose a setting</h2><p class="mb-0">Select an item from the menu to manage that part of your site.</p></div>
        <div id="settingsModuleContent">{$setModule}</div>
      </section>
    </div>
  </main>
</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const shell = document.querySelector('.settings-shell');
    const sidebar = document.getElementById('settingsSidebar');
    const overlay = document.getElementById('settingsOverlay');
    const menuButton = document.getElementById('settingsMenuButton');
    const collapseButton = document.getElementById('settingsCollapseButton');
    const moduleContent = document.getElementById('settingsModuleContent');
    const emptyState = document.getElementById('settingsEmpty');
    const pageTitle = document.getElementById('settingsPageTitle');
    const pageDescription = document.getElementById('settingsPageDescription');
    const currentModule = new URLSearchParams(window.location.search).get('modname');
    const links = document.querySelectorAll('.settings-nav-link[data-module]');
    let activeLink = null;

    links.forEach(function (link) {
      if (link.getAttribute('data-module') === currentModule) activeLink = link;
    });
    if (activeLink) {
      activeLink.classList.add('active');
      activeLink.setAttribute('aria-current', 'page');
      const label = activeLink.querySelector('span').textContent;
      pageTitle.textContent = label;
      pageDescription.textContent = 'Manage ' + label.toLowerCase() + ' for your site.';
    }
    if (moduleContent && moduleContent.textContent.trim() !== '') emptyState.hidden = true;
    else if (moduleContent) moduleContent.hidden = true;

    if (window.localStorage.getItem('lanaiSettingsSidebar') === 'collapsed') {
      shell.classList.add('sidebar-collapsed');
      collapseButton.querySelector('i').className = 'bi bi-layout-sidebar-reverse';
      collapseButton.setAttribute('aria-expanded', 'false');
      collapseButton.setAttribute('aria-label', 'Expand settings menu');
      collapseButton.setAttribute('title', 'Expand menu');
    }

    collapseButton.addEventListener('click', function () {
      const collapsed = shell.classList.toggle('sidebar-collapsed');
      collapseButton.querySelector('i').className = collapsed ? 'bi bi-layout-sidebar-reverse' : 'bi bi-layout-sidebar';
      collapseButton.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
      collapseButton.setAttribute('aria-label', collapsed ? 'Expand settings menu' : 'Collapse settings menu');
      collapseButton.setAttribute('title', collapsed ? 'Expand menu' : 'Collapse menu');
      window.localStorage.setItem('lanaiSettingsSidebar', collapsed ? 'collapsed' : 'expanded');
    });

    function closeMenu() {
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
      menuButton.setAttribute('aria-expanded', 'false');
    }
    menuButton.addEventListener('click', function () {
      const open = !sidebar.classList.contains('open');
      sidebar.classList.toggle('open', open);
      overlay.classList.toggle('open', open);
      menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    overlay.addEventListener('click', closeMenu);
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeMenu(); });
  });
</script>
<script src="assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script src="assets/js/lanai-bootstrap-icons.js"></script>
