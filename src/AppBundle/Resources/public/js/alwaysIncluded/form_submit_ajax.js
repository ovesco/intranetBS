function postForm( $form ){

    /*
     * Get all form values
     */
    var values = {};
    $.each( $form.serializeArray(), function(i, field) {
        values[field.name] = field.value;
    });

    $('#message-box').hide();

    /*
     * Throw the form values to the server!
     */
    $.ajax({
        type        : $form.attr( 'method' ),
        url         : $form.attr( 'action' ),
        data        : values,
        success     : function(data) {

            /* Submit has succeeded */
            if(data == true) {
                location.reload();
            }
            /* Problem occured */
            else {
                /* Remove old modal and show new with fields errors */
                $("[id^=modal-]").remove();
                $(data).modal('show');

                /* Add error */
                $('#message-box .header').html("Erreur lors de l'envoi du formulaire");
                $('#message-box').addClass('error');
                $('#message-box').show();
            }

        },
        error       : function(xhr, ajaxOptions, thrownError) {
            $('#message-box .header').html("Erreur lors de l'envoi du formulaire");
            $('#message-box #messages').html("Détails : " + xhr.status + " / " + thrownError);
            $('#message-box').addClass('error');
            $('#message-box').show();
        }
    });
}

function bindForm() {

    $('#progress-bar').hide();
    $('#message-box').hide();

    $('form.ajax').submit( function( e ){
        e.preventDefault();

        postForm( $(this) );

        return false;
    });

}