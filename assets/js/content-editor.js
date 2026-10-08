(function () {
    'use strict';
    const form = document.getElementById('content-form');
    if (!form) return;
    const status = document.getElementById('content-editor-status');
    const thai = window.lanaiEmbedThai;
    form.addEventListener('submit', function () {
        if (window.tinymce) window.tinymce.triggerSave();
    });
    function plainEditor() {
        if (!thai && status.dataset.fallback) { status.textContent = status.dataset.fallback; return; }
        status.textContent = thai ? 'ตัวแก้ไขข้อความไม่พร้อมใช้งาน คุณยังแก้ไข HTML และแทรกรายการได้' : 'The visual editor could not load. You can still edit the HTML and use the insert controls above.';
    }
    if (!window.tinymce) { plainEditor(); return; }
    window.tinymce.init({
        selector: '#content-form textarea.tinymce',
        license_key: 'gpl',
        height: 400,
        menubar: 'edit view insert format tools table help',
        plugins: 'advlist autolink lists link image media table code fullscreen preview searchreplace wordcount visualblocks charmap help',
        toolbar: 'undo redo | lanaipoll lanaimedia lanaicontact | blocks | bold italic | bullist numlist | link image media | table code fullscreen',
        toolbar_mode: 'wrap',
        noneditable_class: 'mceNonEditable',
        extended_valid_elements: 'div[class|data-lanai-embed|data-lanai-id|contenteditable]',
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        setup: function (editor) {
            window.lanaiSetupEmbeds(editor);
            editor.on('focus', function () { document.getElementById('content-insert-target').value = editor.id; });
        }
    }).catch(plainEditor);
}());
