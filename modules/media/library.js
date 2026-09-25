(() => {
    'use strict';
    const form = document.getElementById('media-upload');
    if (!form) return;
    const input = document.getElementById('media-files');
    const drop = document.getElementById('media-drop');
    const status = document.getElementById('media-upload-status');
    const describe = () => {
        status.textContent = Array.from(input.files, file => file.name).join(', ');
    };
    input.addEventListener('change', describe);
    ['dragenter', 'dragover'].forEach(name => drop.addEventListener(name, event => {
        event.preventDefault();
        drop.classList.add('is-dragging');
    }));
    drop.addEventListener('dragleave', () => drop.classList.remove('is-dragging'));
    drop.addEventListener('drop', event => {
        event.preventDefault();
        drop.classList.remove('is-dragging');
        if (!event.dataTransfer.files.length) return;
        try {
            input.files = event.dataTransfer.files;
            describe();
        } catch (error) {
            status.textContent = 'Please use Select files in this browser.';
        }
    });
    form.addEventListener('submit', event => {
        const maximum = Number(form.dataset.maxFiles);
        if (maximum > 0 && input.files.length > maximum) {
            event.preventDefault();
            status.textContent = 'Select at most ' + maximum + ' files per upload.';
            return;
        }
        status.textContent = 'Uploading ' + input.files.length + ' file(s)…';
        form.querySelector('button[type="submit"]').disabled = true;
    });
})();
