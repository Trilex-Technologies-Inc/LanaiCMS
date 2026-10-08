(function () {
    'use strict';
    const root = document.getElementById('lanai-privacy');
    if (!root) return;
    const config = JSON.parse(root.dataset.config);
    const panel = document.getElementById('privacy-panel');
    const options = document.getElementById('privacy-options');
    const manage = document.getElementById('privacy-manage');
    const status = document.getElementById('privacy-status');
    function show() { panel.hidden = false; panel.focus(); }
    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-privacy-open]')) { show(); options.hidden = false; manage.setAttribute('aria-expanded', 'true'); }
    });
    manage.addEventListener('click', function () {
        options.hidden = !options.hidden;
        manage.setAttribute('aria-expanded', String(!options.hidden));
        if (!options.hidden) options.querySelector('input').focus();
    });
    let saving = false;
    async function save(choices) {
        if (saving) return;
        saving = true;
        root.querySelectorAll('button').forEach(function (button) { button.disabled = true; });
        const body = new URLSearchParams({csrf_token: config.csrf, revision: String(config.revision)});
        ['analytics','external','marketing'].forEach(function (key) { body.set(key, choices[key] ? '1' : '0'); });
        try {
            const response = await fetch(config.endpoint, {method:'POST', credentials:'same-origin', headers:{Accept:'application/json'}, body:body});
            const result = await response.json();
            if (!response.ok || !result.saved) throw new Error('save');
            // Reload unloads optional scripts already running after consent is withdrawn.
            window.location.reload();
        } catch (error) {
            status.textContent = config.error;
            saving = false;
            root.querySelectorAll('button').forEach(function (button) { button.disabled = false; });
        }
    }
    root.querySelectorAll('[data-privacy-choice]').forEach(function (button) {
        button.addEventListener('click', function () {
            const allow = button.dataset.privacyChoice === 'accept';
            save({analytics:allow, external:allow, marketing:allow});
        });
    });
    options.addEventListener('submit', function (event) {
        event.preventDefault();
        save({analytics:options.elements.analytics.checked, external:options.elements.external.checked, marketing:options.elements.marketing.checked});
    });
})();
