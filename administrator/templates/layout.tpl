<!doctype html>
<html lang="{$adminLocale}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <base href="{$adminBase}">
  <title>{$adminLabels['Administration']} — {$adminSiteTitle}</title>
  <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/admin.css">
  <link rel="stylesheet" href="include/jscalendar/calendar-brown.css">
  <script src="include/jscalendar/calendar.js"></script>
  <script src="include/jscalendar/lang/calendar-en.js"></script>
  <script src="include/jscalendar/calendar-setup.js"></script>
  {$adminHead}
</head>
<body>
<a class="settings-skip" href="#admin-main">{$adminLabels['Skip to content']}</a>
<link rel="stylesheet" href="assets/vendor/bootstrap-icons/font/bootstrap-icons.css">


<div class="settings-shell" data-manage-description="{$adminLabels['Manage {section} for your site.']}" data-expand-settings="{$adminLabels['Expand settings menu']}" data-collapse-settings="{$adminLabels['Collapse settings menu']}" data-expand-menu="{$adminLabels['Expand menu']}" data-collapse-menu="{$adminLabels['Collapse menu']}">
  <aside class="settings-sidebar" id="settingsSidebar" aria-label="{$adminLabels['Settings navigation']}">
    <div class="settings-sidebar-header">
      <a class="settings-brand" href="setting.php"><span class="settings-brand-mark">L</span><span>{$adminLabels['Lanai settings']}</span></a>
      <button class="settings-collapse-button" id="settingsCollapseButton" type="button" aria-label="{$adminLabels['Collapse settings menu']}" aria-expanded="true" title="{$adminLabels['Collapse menu']}"><i class="bi bi-layout-sidebar"></i></button>
    </div>
    <nav class="settings-nav">{if $adminFull}<a class="settings-nav-link" data-module="setting" href="setting.php"><i class="bi bi-house"></i><span>{$adminLabels['Dashboard']}</span></a>{/if}
      <section class="settings-nav-section">
        <p class="settings-nav-label">{$adminLabels['Content']}</p>
        <ul class="settings-nav-list">
          {if $adminFull || $adminModules.content}<li><a class="settings-nav-link" data-module="content" href="setting.php?modname=content"><i class="bi bi-file-earmark-text"></i><span>{$adminLabels['Content']}</span></a></li>{/if}
          {if $adminFull}<li><a class="settings-nav-link" data-module="ctype" href="setting.php?modname=ctype"><i class="bi bi-diagram-3"></i><span>{$adminLabels['Content types']}</span></a></li>{/if}
          {if $adminFull || $adminModules.media}<li><a class="settings-nav-link" data-module="media" href="setting.php?modname=media"><i class="bi bi-images"></i><span>{$adminLabels['Media']}</span></a></li>{/if}
          {if $adminFull}<li><a class="settings-nav-link" data-module="carousel" href="setting.php?modname=carousel"><i class="bi bi-collection"></i><span>{$adminLabels['Carousel']}</span></a></li>{/if}
          {if $adminFull}<li><a class="settings-nav-link" data-module="contact" href="setting.php?modname=contact"><i class="bi bi-person-lines-fill"></i><span>{$adminLabels['Contacts']}</span></a></li>{/if}
          {if $adminFull}<li><a class="settings-nav-link" data-module="poll" href="setting.php?modname=poll"><i class="bi bi-bar-chart"></i><span>{$adminLabels['Polls']}</span></a></li>{/if}
        </ul>
      </section>
      {if $adminFull}<section class="settings-nav-section">
        <p class="settings-nav-label">{$adminLabels['Site']}</p>
        <ul class="settings-nav-list">
          <li><a class="settings-nav-link" data-module="menu" href="setting.php?modname=menu"><i class="bi bi-list"></i><span>{$adminLabels['Menus']}</span></a></li>
          <li><a class="settings-nav-link" data-module="block" href="setting.php?modname=block"><i class="bi bi-grid-3x3-gap"></i><span>{$adminLabels['Blocks']}</span></a></li>
          <li><a class="settings-nav-link" data-module="theme" href="setting.php?modname=theme"><i class="bi bi-palette"></i><span>{$adminLabels['Theme']}</span></a></li>
          <li><a class="settings-nav-link" data-module="language" href="setting.php?modname=language"><i class="bi bi-translate"></i><span>{$adminLabels['Language']}</span></a></li>
          <li><a class="settings-nav-link" data-module="privacy" href="setting.php?modname=privacy"><i class="bi bi-shield-check"></i><span>{$adminLabels['Privacy &amp; Compliance']}</span></a></li>
          <li><a class="settings-nav-link" data-module="config" href="setting.php?modname=config"><i class="bi bi-sliders"></i><span>{$adminLabels['Configuration']}</span></a></li>
        </ul>
      </section>{/if}
      {if $adminFull}<section class="settings-nav-section">
        <p class="settings-nav-label">{$adminLabels['Workspace']}</p>
        <ul class="settings-nav-list">
          <li><a class="settings-nav-link" data-module="member" href="setting.php?modname=member"><i class="bi bi-people"></i><span>{$adminLabels['Members']}</span></a></li>
          <li><a class="settings-nav-link" data-module="role" href="setting.php?modname=role"><i class="bi bi-person-badge"></i><span>{$adminLabels['Roles']}</span></a></li>
          <li><a class="settings-nav-link" data-module="apitoken" href="setting.php?modname=apitoken"><i class="bi bi-key"></i><span>{$adminLabels['API tokens']}</span></a></li>
          <li><a class="settings-nav-link" data-module="module" href="setting.php?modname=module"><i class="bi bi-puzzle"></i><span>{$adminLabels['Modules']}</span></a></li>
        </ul>
      </section>{/if}
      {if $adminFull}<section class="settings-nav-section">
        <p class="settings-nav-label">{$adminLabels['System']}</p>
        <ul class="settings-nav-list">
          <li><a class="settings-nav-link" data-module="backup" href="setting.php?modname=backup"><i class="bi bi-hdd-stack"></i><span>{$adminLabels['Backup']}</span></a></li>
          <li><a class="settings-nav-link" data-module="explorer" href="setting.php?modname=explorer"><i class="bi bi-folder2-open"></i><span>{$adminLabels['File explorer']}</span></a></li>
          <li><a class="settings-nav-link" data-module="info" href="setting.php?modname=info"><i class="bi bi-info-circle"></i><span>{$adminLabels['System info']}</span></a></li>
          <li><a class="settings-nav-link" data-module="statistics" href="setting.php?modname=statistics"><i class="bi bi-graph-up"></i><span>{$adminLabels['Statistics']}</span></a></li>
        </ul>
      </section>{/if}
    </nav>
    <div class="settings-sidebar-footer">
      <a class="settings-nav-link" href="index.php" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i><span>{$adminLabels['View site']}</span></a>
      <a class="settings-nav-link" href="module.php?modname=member&amp;mf=memlogout"><i class="bi bi-box-arrow-right"></i><span>{$adminLabels['Log out']}</span></a>
    </div>
  </aside>

  <div class="settings-overlay" id="settingsOverlay"></div>
  <main class="settings-main" id="admin-main" tabindex="-1">
    <header class="settings-topbar">
      <div class="settings-mobile-controls">
        <button class="settings-menu-button" id="settingsMenuButton" type="button" aria-label="{$adminLabels['Open settings menu']}" aria-expanded="false"><i class="bi bi-list"></i></button>
        <span class="settings-mobile-title">{$adminLabels['Lanai settings']}</span>
      </div>
      <div class="dropdown settings-user">
        <button class="settings-user-button dropdown-toggle" type="button" id="settingsUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle"></i><span>{$settingUserName}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsUserMenu">
          <li><a class="dropdown-item" href="module.php?modname=member&amp;mf=meminfo"><i class="bi bi-person me-2"></i>{$adminLabels['My account']}</a></li>
          <li><a class="dropdown-item" href="index.php" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-2"></i>{$adminLabels['View site']}</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="module.php?modname=member&amp;mf=memlogout"><i class="bi bi-box-arrow-right me-2"></i>{$adminLabels['Log out']}</a></li>
        </ul>
      </div>
    </header>
    <div class="settings-workspace">
      <header class="settings-page-heading">
        <h1 id="settingsPageTitle">{if $isSettingsDashboard}{$adminLabels['Dashboard']}{else}{$adminLabels['Settings']}{/if}</h1>
        <p id="settingsPageDescription">{if $isSettingsDashboard}{$adminLabels['A little overview of everything happening on your site.']}{else}{$adminLabels['Manage your LanaiCMS site and workspace.']}{/if}</p>
      </header>
      <section class="settings-module">
        <div class="settings-empty" id="settingsEmpty"{if $setModule} hidden{/if}><h2>{$adminLabels['Choose a setting']}</h2><p class="mb-0">{$adminLabels['Select an item from the menu to manage that part of your site.']}</p></div>
        <div id="settingsModuleContent">{$setModule}</div>
      </section>
    </div>
  </main>
</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin.js"></script>
<script src="assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script src="assets/js/lanai-bootstrap-icons.js"></script>

</body></html>
