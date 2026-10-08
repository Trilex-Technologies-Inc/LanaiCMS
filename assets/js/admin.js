
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
    const currentModule = new URLSearchParams(window.location.search).get('modname') || 'setting';
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
      pageDescription.textContent = (shell.dataset.manageDescription || 'Manage {section} for your site.').replace('{section}', label);
    }
    if (moduleContent && moduleContent.textContent.trim() !== '') emptyState.hidden = true;
    else if (moduleContent) moduleContent.hidden = true;

    let savedSidebar = '';
    try { savedSidebar = window.localStorage.getItem('lanaiSettingsSidebar'); } catch (error) {}
    if (savedSidebar === 'collapsed') {
      shell.classList.add('sidebar-collapsed');
      collapseButton.querySelector('i').className = 'bi bi-layout-sidebar-reverse';
      collapseButton.setAttribute('aria-expanded', 'false');
      collapseButton.setAttribute('aria-label', shell.dataset.expandSettings || 'Expand settings menu');
      collapseButton.setAttribute('title', shell.dataset.expandMenu || 'Expand menu');
    }

    collapseButton.addEventListener('click', function () {
      const collapsed = shell.classList.toggle('sidebar-collapsed');
      collapseButton.querySelector('i').className = collapsed ? 'bi bi-layout-sidebar-reverse' : 'bi bi-layout-sidebar';
      collapseButton.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
      collapseButton.setAttribute('aria-label', collapsed ? shell.dataset.expandSettings : shell.dataset.collapseSettings);
      collapseButton.setAttribute('title', collapsed ? shell.dataset.expandMenu : shell.dataset.collapseMenu);
      try { window.localStorage.setItem('lanaiSettingsSidebar', collapsed ? 'collapsed' : 'expanded'); } catch (error) {}
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
