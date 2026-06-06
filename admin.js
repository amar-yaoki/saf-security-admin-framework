/**
 * SAF — Admin JS
 * Media uploader per i campi immagine di ⚙️ Dati Sito.
 * Vanilla JS, zero dipendenze.
 */
(function() {
    'use strict';

    var mediaFrame = null;

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.saf-media-btn');
        if (!btn) return;

        e.preventDefault();
        var targetId = btn.getAttribute('data-target');
        var input = document.getElementById(targetId);
        if (!input) return;

        if (mediaFrame) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title: 'Seleziona immagine',
            button: { text: 'Usa questa immagine' },
            multiple: false,
            library: { type: 'image' }
        });

        mediaFrame.on('select', function() {
            var attachment = mediaFrame.state().get('selection').first().toJSON();
            input.value = attachment.url;
            // Aggiorna anteprima
            var preview = document.getElementById(targetId + '_preview');
            if (preview) {
                preview.innerHTML = '<img src="' + attachment.url + '" style="max-width:300px;border-radius:4px;border:1px solid #ddd">';
            }
            // Trigger change event
            input.dispatchEvent(new Event('change'));
        });

        mediaFrame.open();
    });

})();
