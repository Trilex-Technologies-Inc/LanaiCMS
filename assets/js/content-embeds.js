(function () {
    'use strict';
    const thai = window.lanaiEmbedThai;
    const labels = {poll: thai ? 'แทรกโพล' : 'Insert poll', media: thai ? 'แทรกสื่อ' : 'Insert media', contact: thai ? 'แทรกข้อมูลติดต่อ' : 'Insert contact'};
    function choices(type) { return (window.lanaiEmbedChoices || {})[type] || []; }
    function markup(type, choice) {
        if (!choice || !/^\d+$/.test(choice.value)) return '';
        let node;
        if (type === 'media') {
            const url = new URL(choice.url, document.baseURI);
            if (!['http:', 'https:'].includes(url.protocol)) return '';
            node = document.createElement(choice.kind === 'image' ? 'img' : 'a');
            if (choice.kind === 'image') {
                node.src = url.href; node.alt = choice.alt || ''; node.style.maxWidth = '100%';
            } else {
                node.href = url.href; node.textContent = choice.text;
            }
        } else {
            node = document.createElement('div');
            node.className = 'mceNonEditable';
            node.setAttribute('data-lanai-embed', type);
            node.setAttribute('data-lanai-id', choice.value);
            node.textContent = labels[type] + ': ' + choice.text;
        }
        return node.outerHTML + '<p></p>';
    }
    window.lanaiSetupEmbeds = function (editor) {
        Object.keys(labels).forEach(function (type) {
            editor.ui.registry.addButton('lanai' + type, {
                text: labels[type],
                onAction: function () {
                    const items = choices(type);
                    if (!items.length) {
                        editor.windowManager.alert(thai ? 'กรุณาสร้างรายการในโมดูลก่อน' : 'Create an active item in its module first. For images or files, upload them in Media.');
                        return;
                    }
                    editor.windowManager.open({
                        title: labels[type],
                        body: {type: 'panel', items: [{type: 'selectbox', name: 'item', label: labels[type], items: items.map(function (item) { return {value:item.value,text:item.text}; })}]},
                        initialData: {item: items[0].value},
                        buttons: [{type:'cancel',text:thai?'ยกเลิก':'Cancel'}, {type:'submit',text:thai?'แทรก':'Insert',primary:true}],
                        onSubmit: function (dialog) {
                            const choice = items.find(function (item) { return item.value === dialog.getData().item; });
                            editor.insertContent(markup(type, choice)); dialog.close();
                        }
                    });
                }
            });
        });
    };
    document.getElementById('content-insert-controls').addEventListener('click', function (event) {
        const button = event.target.closest('[data-content-insert]');
        if (!button) return;
        const type = button.dataset.contentInsert;
        const choice = choices(type).find(function (item) { return item.value === document.getElementById('content-choice-' + type).value; });
        const html = markup(type, choice);
        const target = document.getElementById('content-insert-target').value;
        const editor = window.tinymce && window.tinymce.get(target);
        if (editor && editor.initialized) { editor.focus(); editor.insertContent(html); editor.save(); }
        else {
            const textarea = document.getElementById(target);
            textarea.setRangeText(html, textarea.selectionStart, textarea.selectionEnd, 'end');
            textarea.focus();
        }
    });
}());
