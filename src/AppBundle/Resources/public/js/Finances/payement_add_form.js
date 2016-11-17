/**
 * Ce script est excusivement destiné à une utilisation dans la page
 * de saisie des payements.
 */
$(document).ready(function() {

    $(document).on('submit','.form_add_manually',(function (e) {
        e.preventDefault();
        $form = $(this);
        var $dimmer = $form.find('.dimmer');
        $dimmer.addClass('active');

        /*
         * Get all form values
         */
        var values = {};
        $.each( $form.serializeArray(), function(i, field) {
            values[field.name] = field.value;
        });

        /*
         * Throw the form values to the server!
         */
        $.ajax({
            type        : $form.attr( 'method' ),
            url         : $form.attr( 'action' ),
            data        : values,
            success     : function(data) {
                $form.replaceWith(data);
                $dimmer.removeClass('active');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alerte.send("Erreur lors de l'envoi du formulaire\nDétails : " + xhr.status + " / " + thrownError, 'error');
                $dimmer.removeClass('active');
            }
        });

    }));

});





