(function($) {
    $(document).ready(function () {
        function getURLParameter(name) {
            return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
        }

        function setAllLinks(overrideColorTheme, overrideLayout) {
            var colorTheme = getURLParameter('color');
            var layout = getURLParameter('layout');

            if( overrideColorTheme ) {
                colorTheme = overrideColorTheme;
            }
            if( overrideLayout ) {
                layout = overrideLayout;
            }

            if( colorTheme || layout ) {
                var queryString;
                if( colorTheme && layout ) {
                    queryString = "color=" + colorTheme + "&layout=" + layout;
                } else if( colorTheme ) {
                    queryString = "color=" + colorTheme;
                } else if( layout ) {
                    queryString = "layout=" + layout;
                }

                $('a').each(function() {
                    var href = $(this).attr('href');

                    if( href && !$(this).hasClass('no-color-link') && !(href.indexOf('#') > -1) ) {
                        href = href.replace(/\?.*/, '');

                        href += (href.match(/\?/) ? '&' : '?') + queryString;
                        $(this).attr('href', href);
                    }
                });
            }
        }

        setAllLinks();




        var layoutSelect = $('#layout');
        if( $('div.wrap').hasClass('container-fluid') || $('div.wrap').hasClass('pseudo-fluid') ) {
            layoutSelect.val('full');
        } else {
            layoutSelect.val('normal');
        }
        setAllLinks(null, $('#layout').val());

        layoutSelect.change(function() {
            var wrap = $('div.wrap');
            if( $(this).val() == 'normal' ) {
                wrap.addClass('container');
                wrap.removeClass('container-fluid');
                wrap.removeClass('pseudo-fluid');
            } else {
                wrap.addClass('container-fluid');
                wrap.addClass('pseudo-fluid');
                wrap.removeClass('container');
            }
            resizeNoPad();

            setAllLinks(null, $(this).val());
        });



        var bgSelect = $('#background');


    });
})(jQuery);
