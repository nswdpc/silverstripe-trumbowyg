(function($) {
    $(document).ready(function() {
        $('textarea#{$ID}[data-tw="1"]')
            .trumbowyg({$Options.RAW})
            .on('tbwblur', function(e) {
                try {
                    let el = new DOMParser().parseFromString($(this).val(), 'text/html');
                    let txt = el.documentElement.textContent.trim();
                    if(txt == '') {
                        $(this).val('');
                    }
                } catch (e) {
                    console.warn('Could not parse value');
                }
            });
    })
}(jQuery));
