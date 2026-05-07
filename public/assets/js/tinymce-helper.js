
/* 
use below 3 lines on any page
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/js/tinymce-helper.js') }}"></script>
<script>initCustomTinyMCE('textarea#activity_note');</script> 
*/

function initTinyMCE(selector = 'textarea', height = null) {
    tinymce.init({
        selector: selector,
        license_key: 'gpl',
        branding: false,
        promotion: false,
        theme: 'silver',
        skin: 'tinymce-5',
        height: height || 300,
        placeholder: 'Enter your text here...',
        setup: function (editor) {
            editor.on('init', function () {
                console.log('Editor initialized with height:', height || 300);
            });
        }
    });
}

function initCustomTinyMCE(selector = 'textarea', height = null) {
    tinymce.init({
        selector: selector,
        browser_spellcheck: true,
        license_key: 'gpl',
        branding: false,
        promotion: false,
        menubar: false,
        theme: 'silver',
        skin: 'tinymce-5',
        height: height || 400,
        placeholder: 'Enter your text here...',

        plugins: 'lists advlist link code format',
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | insertDummyText clearContent | bullist numlist | code',

        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3',
        content_css: 'default',

        setup: function (editor) {
            // Insert Dummy Text Button
            editor.ui.registry.addButton('insertDummyText', {
                icon: 'edit-block',
                tooltip: 'Insert Dummy Text',
                onAction: function () {
                    const dummyText = `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.`;
                    editor.insertContent(`<p>${dummyText}</p>`);
                }
            });

            // Clear Content Button
            editor.ui.registry.addButton('clearContent', {
                icon: 'remove',
                tooltip: 'Clear Content',
                onAction: function () {
                    editor.setContent('');
                }
            });

            editor.on('init', function () {
                editor.getBody().setAttribute('spellcheck', true);
                editor.getBody().setAttribute('lang', 'en');
            });
        }
    });
}
