/**
 * Trumbowyg editor loader
 */
class TrumbowygLoader {

    handle() {

        let editors = document.querySelectorAll('textarea[data-tw="1"]');
        editors.forEach(
            editor => {
                this.setupEditor(editor);
            }
        );
    }

    setupEditor(editor) {
        let options = JSON.parse(editor.dataset.twOptions);
        jQuery(editor).trumbowyg(options).on(
            'tbwblur',
            function(e) {
                try {
                    let el = new DOMParser().parseFromString($(this).val(), 'text/html');
                    let txt = el.documentElement.textContent.trim();
                    if(txt == '') {
                        jQuery(this).val('');
                    }
                } catch (e) {
                    console.warn('Could not parse value');
                }
            }
        );
    }

}