(() => {
    'use strict';
    const app = document.getElementById('explorer');
    if (!app) return;
    const byId = id => document.getElementById('explorer-' + id);
    const config = JSON.parse(byId('config').textContent);
    const token = byId('token').querySelector('[name="csrf_token"]').value;
    const state = { root: byId('root').value, dir: '', page: 1, pages: 1, sort: 'name', order: 'asc', trash: false, rows: [], selected: new Set(), busy: false, generation: 0 };
    const api = 'modules/explorer/api.php';
    const el = (tag, text, className) => { const node = document.createElement(tag); if (text !== undefined) node.textContent = text; if (className) node.className = className; return node; };
    const status = (text, error = false) => { byId('status').textContent = text; byId('status').classList.toggle('error', error); };
    const button = (label, callback, className) => { const node = el('button', label, className); node.type = 'button'; node.addEventListener('click', callback); return node; };
    const parent = path => path.includes('/') ? path.slice(0, path.lastIndexOf('/')) : '';
    const url = (endpoint, params) => endpoint + '?' + new URLSearchParams(params).toString();
    const downloadUrl = (path, preview = false) => url('modules/explorer/download.php', { root: state.root, path, preview: preview ? '1' : '0' });
    const size = bytes => bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : bytes >= 1024 ? (bytes / 1024).toFixed(1) + ' KB' : bytes + ' B';

    async function jsonRequest(params, post = false) {
        const response = await fetch(post ? api : url(api, params), post ? { method: 'POST', body: params, credentials: 'same-origin' } : { credentials: 'same-origin', cache: 'no-store' });
        let data;
        try { data = await response.json(); } catch (error) { throw new Error('The server returned an unexpected response. Reload Explorer or check the server upload limits.'); }
        if (!response.ok || data.error) throw new Error(data.error || 'Request failed.');
        return data;
    }
    function formData(action, extra = {}) {
        const data = new FormData();
        Object.entries({ action, root: state.root, dir: state.dir, csrf_token: token, collision: byId('collision').value, ...extra }).forEach(([key, value]) => data.append(key, value));
        state.selected.forEach(path => data.append('paths[]', path));
        return data;
    }
    function selected() {
        byId('selection').textContent = state.selected.size + ' selected';
        const all = byId('select-all');
        all.checked = state.rows.length > 0 && state.selected.size === state.rows.length;
        all.indeterminate = state.selected.size > 0 && !all.checked;
    }
    function busy(value) {
        state.busy = value;
        app.querySelectorAll('button, input, select').forEach(node => { node.disabled = value; });
        if (!value) { byId('previous').disabled = state.page <= 1; byId('next').disabled = state.page >= state.pages; byId('up').disabled = state.dir === '' || state.trash; }
    }
    function breadcrumbs() {
        const nav = byId('breadcrumbs'); nav.replaceChildren();
        if (state.trash) { nav.append(el('strong', 'Trash — all locations')); return; }
        nav.append(button(config.roots[state.root], () => navigate('')));
        let path = '';
        state.dir.split('/').filter(Boolean).forEach(part => { path += (path ? '/' : '') + part; const target = path; nav.append(el('span', '/'), button(part, () => navigate(target))); });
    }
    function navigate(dir) { if (state.busy) return; state.dir = dir; state.page = 1; byId('preview').hidden = true; load(); }
    function renderRows(rows) {
        const body = byId('rows'); body.replaceChildren(); state.rows = rows; state.selected.clear(); selected();
        if (!rows.length) { const row = el('tr'); const cell = el('td', state.trash ? 'Trash is empty.' : 'No matching files or folders.'); cell.colSpan = 6; row.append(cell); body.append(row); }
        rows.forEach(item => {
            const key = state.trash ? item.id : item.path;
            const row = el('tr'); const selectCell = el('td'); const check = el('input'); check.type = 'checkbox'; check.setAttribute('aria-label', 'Select ' + (item.name || item.path));
            check.addEventListener('change', () => { if (check.checked) state.selected.add(key); else state.selected.delete(key); row.classList.toggle('selected', check.checked); selected(); });
            selectCell.append(check); row.append(selectCell);
            const nameCell = el('td');
            if (state.trash) { nameCell.append(el('span', item.path), el('small', config.roots[item.root] || item.root)); }
            else {
                nameCell.append(button((item.type === 'folder' ? '▸ ' : '') + item.name, () => item.type === 'folder' ? navigate(item.path) : preview(item), 'file-name'));
                if (parent(item.path) !== state.dir) nameCell.append(el('small', parent(item.path)));
                if (item.mediaId) { const media = el('a', 'Media #' + item.mediaId); media.href = 'setting.php?modname=media&mf=details&mediaId=' + item.mediaId; const note = el('small'); note.append(media); nameCell.append(note); }
            }
            row.append(nameCell, el('td', item.type), el('td', state.trash || item.type === 'folder' ? '—' : size(item.size)), el('td', new Date((state.trash ? item.deleted : item.modified) * 1000).toLocaleString()));
            const actions = el('td');
            if (!state.trash && item.type !== 'folder') {
                actions.append(button('Preview', () => preview(item)));
                const link = el('a', 'Download'); link.href = downloadUrl(item.path); actions.append(link);
            }
            row.append(actions); body.append(row);
        });
        byId('page').textContent = 'Page ' + state.page + ' of ' + state.pages;
        byId('previous').disabled = state.page <= 1; byId('next').disabled = state.page >= state.pages;
        byId('up').disabled = state.dir === '' || state.trash;
        app.querySelectorAll('th[data-sort]').forEach(th => th.setAttribute('aria-sort', th.dataset.sort === state.sort ? (state.order === 'asc' ? 'ascending' : 'descending') : 'none'));
    }
    async function load(keepStatus = false) {
        const generation = ++state.generation;
        state.selected.clear(); state.rows = []; byId('rows').replaceChildren(); selected();
        if (!keepStatus) status('Loading…');
        byId('filters').hidden = state.trash; byId('drop').hidden = state.trash; byId('actions').hidden = state.trash; byId('trash-actions').hidden = !state.trash;
        byId('trash-view').textContent = state.trash ? 'Back to files' : 'Trash'; byId('trash-view').setAttribute('aria-pressed', String(state.trash));
        breadcrumbs();
        try {
            let data;
            if (state.trash) {
                data = await jsonRequest({ action: 'trashList' });
                const key = state.sort === 'name' ? 'path' : state.sort === 'modified' ? 'deleted' : state.sort;
                data.items.sort((a, b) => (state.order === 'desc' ? -1 : 1) * (key === 'deleted' ? a[key] - b[key] : String(a[key] || '').localeCompare(String(b[key] || ''), undefined, { numeric: true })));
                data.total = data.items.length; data.pages = Math.max(1, Math.ceil(data.total / 100)); data.page = Math.min(state.page, data.pages); data.items = data.items.slice((data.page - 1) * 100, data.page * 100);
            } else {
                const filters = Object.fromEntries(new FormData(byId('filters'))); filters.recursive = byId('filters').elements.recursive.checked ? '1' : '';
                data = await jsonRequest({ action: 'list', root: state.root, dir: state.dir, page: state.page, sort: state.sort, order: state.order, ...filters });
            }
            if (generation !== state.generation) return;
            state.page = data.page; state.pages = data.pages; renderRows(data.items);
            app.querySelector('th[data-sort="modified"] button').textContent = state.trash ? 'Deleted' : 'Modified';
            if (!keepStatus) status(data.total + ' item(s)');
        } catch (error) { if (generation === state.generation) { renderRows([]); status(error.message, true); } }
    }
    let previewGeneration = 0;
    async function preview(item) {
        const generation = ++previewGeneration;
        const body = byId('preview-body'); body.replaceChildren(el('p', 'Loading…')); byId('preview-title').textContent = item.name; byId('preview').hidden = false;
        try {
            const data = await jsonRequest({ action: 'preview', root: state.root, path: item.path });
            if (generation !== previewGeneration) return;
            body.replaceChildren();
            if (data.type === 'text') { body.append(el('pre', data.text)); if (data.truncated) body.append(el('p', 'Preview limited to the first 100 KB.')); }
            else if (data.type === 'image') { const image = el('img'); image.alt = item.name; image.src = downloadUrl(item.path, true); body.append(image); }
            else if (data.type === 'pdf') { const frame = el('iframe'); frame.title = item.name; frame.setAttribute('sandbox', ''); frame.src = downloadUrl(item.path, true); body.append(frame, el('p', 'If your browser cannot display this PDF here, use Download.')); }
            else body.append(el('p', 'Preview is unavailable for this file type.'));
            const link = el('a', 'Download file'); link.href = downloadUrl(item.path); body.append(link);
        } catch (error) { if (generation === previewGeneration) body.replaceChildren(el('p', error.message)); }
    }
    function report(data) {
        if (data.message) { status(data.message); return; }
        const failed = data.results.filter(item => !item.ok);
        status((data.results.length - failed.length) + ' item(s) completed.' + (failed.length ? '\n' + failed.map(item => item.path + ': ' + item.error).join('\n') : ''), failed.length > 0);
    }
    async function perform(action, extra = {}) {
        if (state.busy) return;
        if (action !== 'mkdir' && !state.selected.size) { status('Select at least one item.', true); return; }
        if (action === 'trash' && !confirm('Move the selected items to Trash? They can be restored.')) return;
        if (action === 'purge') { if (!confirm('Permanently delete the selected trash items? This cannot be undone.')) return; extra.confirm = 'permanent'; }
        if (action === 'restore' && byId('collision').value === 'replace') { status('Choose Skip or Keep both when restoring.', true); return; }
        busy(true); status('Working…');
        try {
            const data = formData(action, extra);
            if (action === 'zip') {
                const response = await fetch(api, { method: 'POST', body: data, credentials: 'same-origin' });
                if (!response.ok || (response.headers.get('Content-Type') || '').includes('json')) { const error = await response.json(); throw new Error(error.error || 'Download failed.'); }
                const href = URL.createObjectURL(await response.blob()); const link = el('a'); link.href = href; link.download = 'files.zip'; document.body.append(link); link.click(); link.remove(); setTimeout(() => URL.revokeObjectURL(href), 60000); status('Download ready.');
            } else { report(await jsonRequest(data, true)); await load(true); }
        } catch (error) { status(error.message, true); }
        finally { busy(false); }
    }
    let nameAction;
    function askName(action) {
        if (action === 'rename' && state.selected.size !== 1) { status('Select exactly one item to rename.', true); return; }
        nameAction = action; byId('name-title').textContent = action === 'mkdir' ? 'New folder' : 'Rename';
        byId('name').value = action === 'rename' ? [...state.selected][0].split('/').pop() : '';
        byId('name-dialog').returnValue = ''; byId('name-dialog').showModal(); byId('name').focus(); byId('name').select();
    }
    byId('name-dialog').addEventListener('close', () => { if (byId('name-dialog').returnValue === 'save') perform(nameAction, { name: byId('name').value }); });
    let moveDir = '', movePage = 1, movePages = 1, moveGeneration = 0;
    async function moveFolders() {
        const generation = ++moveGeneration; const container = byId('move-folders'); container.replaceChildren(el('p', 'Loading…'));
        byId('move-dialog').querySelector('[value="move"]').disabled = true;
        try {
            const data = await jsonRequest({ action: 'list', root: byId('move-root').value, dir: moveDir, type: 'folder', page: movePage });
            if (generation !== moveGeneration) return;
            movePage = data.page; movePages = data.pages; container.replaceChildren(); byId('move-path').textContent = '/' + moveDir;
            data.items.forEach(item => container.append(button('▸ ' + item.name, () => { moveDir = item.path; movePage = 1; moveFolders(); })));
            if (!data.items.length) container.append(el('p', 'No subfolders.'));
            byId('move-page').textContent = 'Page ' + movePage + ' of ' + movePages;
            byId('move-previous').disabled = movePage <= 1; byId('move-next').disabled = movePage >= movePages; byId('move-up').disabled = moveDir === '';
            byId('move-dialog').querySelector('[value="move"]').disabled = false;
        } catch (error) { container.replaceChildren(el('p', error.message)); }
    }
    byId('move-root').addEventListener('change', () => { moveDir = ''; movePage = 1; moveFolders(); });
    byId('move-up').addEventListener('click', () => { moveDir = parent(moveDir); movePage = 1; moveFolders(); });
    byId('move-previous').addEventListener('click', () => { if (movePage > 1) { movePage--; moveFolders(); } });
    byId('move-next').addEventListener('click', () => { if (movePage < movePages) { movePage++; moveFolders(); } });
    byId('move-dialog').addEventListener('close', () => { if (byId('move-dialog').returnValue === 'move') perform('move', { targetRoot: byId('move-root').value, targetDir: moveDir }); });
    app.querySelectorAll('[data-action]').forEach(node => node.addEventListener('click', () => {
        const action = node.dataset.action;
        if (action === 'mkdir' || action === 'rename') askName(action);
        else if (action === 'move') {
            if (!state.selected.size) { status('Select items to move.', true); return; }
            moveDir = state.dir; movePage = 1; byId('move-root').value = state.root; byId('move-dialog').returnValue = ''; byId('move-dialog').showModal(); moveFolders();
        } else perform(action);
    }));
    async function upload(list) {
        if (state.busy || state.trash || !list.length) return;
        const policy = byId('collision').value;
        if (policy === 'replace' && !confirm('Replace matching files? The previous files will be saved in Trash.')) return;
        const results = byId('upload-results'); results.replaceChildren(); busy(true);
        let succeeded = 0;
        for (const file of list) {
            const row = el('li'); const progress = el('progress'); progress.max = 100; progress.value = 0; const message = el('span', 'Waiting'); row.append(el('span', file.name), progress, message); results.append(row);
            const data = formData('upload', { collision: policy }); data.append('file', file);
            try {
                await new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest(); xhr.open('POST', api); xhr.timeout = 300000;
                    xhr.upload.addEventListener('progress', event => { if (event.lengthComputable) { progress.value = event.loaded / event.total * 100; message.textContent = progress.value < 100 ? Math.round(progress.value) + '%' : 'Saving…'; } });
                    xhr.addEventListener('load', () => { try { const response = JSON.parse(xhr.responseText); if (xhr.status >= 400 || response.error) throw new Error(response.error || 'Upload failed.'); message.textContent = response.message; progress.value = 100; resolve(); } catch (error) { reject(error); } });
                    xhr.addEventListener('error', () => reject(new Error('Network error. Refresh before retrying.')));
                    xhr.addEventListener('timeout', () => reject(new Error('Upload timed out. Refresh before retrying.')));
                    xhr.send(data);
                }); succeeded++;
            } catch (error) { message.textContent = error.message; }
        }
        byId('upload').value = ''; status(succeeded + ' of ' + list.length + ' files uploaded.', succeeded !== list.length); await load(true); busy(false);
    }
    byId('upload').addEventListener('change', event => upload(Array.from(event.target.files)));
    ['dragenter', 'dragover'].forEach(type => byId('drop').addEventListener(type, event => { event.preventDefault(); if (!state.busy) byId('drop').classList.add('dragging'); }));
    byId('drop').addEventListener('dragleave', () => byId('drop').classList.remove('dragging'));
    byId('drop').addEventListener('drop', event => { event.preventDefault(); byId('drop').classList.remove('dragging'); upload(Array.from(event.dataTransfer.files)); });
    byId('root').addEventListener('change', () => { state.root = byId('root').value; state.dir = ''; state.page = 1; load(); });
    byId('up').addEventListener('click', () => navigate(parent(state.dir)));
    byId('refresh').addEventListener('click', () => load());
    byId('trash-view').addEventListener('click', () => { state.trash = !state.trash; state.page = 1; byId('preview').hidden = true; load(); });
    byId('filters').addEventListener('submit', event => { event.preventDefault(); state.page = 1; load(); });
    byId('filters').addEventListener('reset', () => setTimeout(() => { state.page = 1; load(); }, 0));
    byId('select-all').addEventListener('change', event => { const checked = event.target.checked; state.selected.clear(); byId('rows').querySelectorAll('input[type="checkbox"]').forEach(input => { input.checked = checked; input.dispatchEvent(new Event('change')); }); selected(); });
    app.querySelectorAll('th[data-sort] button').forEach(node => node.addEventListener('click', () => { const key = node.parentElement.dataset.sort; state.order = state.sort === key && state.order === 'asc' ? 'desc' : 'asc'; state.sort = key; state.page = 1; load(); }));
    byId('previous').addEventListener('click', () => { if (state.page > 1) { state.page--; load(); } });
    byId('next').addEventListener('click', () => { if (state.page < state.pages) { state.page++; load(); } });
    byId('preview-close').addEventListener('click', () => { previewGeneration++; byId('preview').hidden = true; byId('preview-body').replaceChildren(); });
    load();
})();
