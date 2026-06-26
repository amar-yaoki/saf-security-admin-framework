/* ===== SAF v2 Admin JavaScript ===== */
(function($) {
    'use strict';

    $(function() {
        // Tab persistence — silent URL update, no redirect
        var $tabs = $('.saf-tabs .nav-tab');
        $tabs.on('click', function() {
            var href = $(this).attr('href');
            if (href) {
                localStorage.setItem('saf_last_tab', href);
            }
        });

        var saved = localStorage.getItem('saf_last_tab');
        if (saved && window.location.search.indexOf('tab=') === -1) {
            history.replaceState(null, '', saved);
        }

        // Dismissible notices
        $('.notice.is-dismissible').each(function() {
            var $el = $(this);
            if ($el.find('.notice-dismiss').length === 0) {
                $el.prepend('<button type="button" class="notice-dismiss"><span class="screen-reader-text">Ignora</span></button>');
                $el.on('click', '.notice-dismiss', function() {
                    $el.fadeOut(300);
                });
            }
        });
    });

})(jQuery);
