(function () {
  const iconMap = {
    'new.gif': 'bi-plus-circle', 'save.gif': 'bi-save', 'back.gif': 'bi-arrow-left',
    'edit.gif': 'bi-pencil-square', 'delete.gif': 'bi-trash', 'cancel.gif': 'bi-x-circle',
    'ok.gif': 'bi-check-circle', 'go.gif': 'bi-arrow-right-circle', 'go2.gif': 'bi-arrow-right-circle',
    'find.gif': 'bi-search', 'search.gif': 'bi-search', 'file.gif': 'bi-file-earmark',
    'html.gif': 'bi-filetype-html', 'config.gif': 'bi-gear', 'configure.gif': 'bi-gear',
    'uparrow.gif': 'bi-arrow-up', 'downarrow.gif': 'bi-arrow-down',
    'view_choose.gif': 'bi-ui-checks-grid', 'view_text.gif': 'bi-list-ul',
    'user.gif': 'bi-person', 'user2.gif': 'bi-person-plus', 'user3.gif': 'bi-person',
    'user4.gif': 'bi-person', 'setting.gif': 'bi-gear', 'logout.gif': 'bi-box-arrow-right',
    'worning.gif': 'bi-exclamation-triangle', 'db_comit.png': 'bi-upload',
    'db_update.png': 'bi-download', 'cnrdelete-all.png': 'bi-trash'
  };

  function replaceLegacyIcons(root) {
    root.querySelectorAll('img').forEach(function (image) {
      const source = image.getAttribute('src') || '';
      const filename = source.split('?')[0].split('/').pop().toLowerCase();
      const iconName = iconMap[filename];
      if (!iconName) return;

      const icon = document.createElement('i');
      icon.className = 'bi ' + iconName + ' legacy-action-icon';
      const label = image.getAttribute('alt') || image.getAttribute('title');
      if (label) {
        icon.setAttribute('title', label);
        icon.setAttribute('aria-label', label);
        icon.setAttribute('role', 'img');
      } else {
        icon.setAttribute('aria-hidden', 'true');
      }
      image.replaceWith(icon);
    });
  }

  const style = document.createElement('style');
  style.textContent = '.legacy-action-icon{display:inline-block;min-width:1.15em;margin-right:4px;color:#4b5563;font-size:1rem;line-height:1;vertical-align:-.125em}' +
    'a:hover>.legacy-action-icon{color:#111827}.legacy-action-icon.bi-trash,.legacy-action-icon.bi-x-circle{color:#dc3545}' +
    '.legacy-action-icon.bi-check-circle{color:#198754}';
  document.head.appendChild(style);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { replaceLegacyIcons(document); });
  } else {
    replaceLegacyIcons(document);
  }
}());
