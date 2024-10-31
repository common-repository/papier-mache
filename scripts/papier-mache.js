
jQuery(document).ready( function() {
    jQuery.getJSON( papier_mache.ajaxurl, { action: 'papier_mache' }, function(data) {
        var confetti = new ConfettiGenerator(data);
        confetti.render();
    });
});
