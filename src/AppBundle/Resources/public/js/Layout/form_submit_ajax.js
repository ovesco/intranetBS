/**
 * Cette méthode catch les événements submit d'un formulaire pour envoyer les requetes
 * en ajax. C'est utilisé dans les modal par exemple ou on veut catcher la réponse pour
 * mettre à jour la modal plutot que de charger une nouvelle page
 *
 * Attention: pour qu'un formulaire soit bindé il faut que la classe soit "ui form ajax"
 */
function bindForm() {

    $('form.ajax').submit(function (e) {
        e.preventDefault();

        postForm($(this));

        return false;
    });
}

/**
 * Envoie un formulaire en ajax
 *
 * Fait des notifications en cas d'erreur
 *
 * @param $form Un formulaire à envoyer
 */
function postForm( $form ){

    var values;
    /*
     * La methode d'envoi des donnée varie si le formulaire
     * contient un input "file" ou non. File requière un
     * envoie synchrone.
     */
    if ($form.find('input:file').length ) {
        /*
         * Get all form values, with FormData we can
         * upload file with ajax
         */
        values = new FormData($form[0]);

        $.ajax({
            type        : $form.attr( 'method' ),
            url         : $form.attr( 'action' ),
            data        : values,
            async       : false,
            success     : successForm,
            error       : errorForm,
            cache       : false,
            contentType : false,
            processData : false
        });

    }
    else
    {
        values = {};
        /*
         * Get all form values from standard form (without file type)
         */
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
            success     : successForm,
            error       : errorForm
        });
    }




}

function successForm(data)
{
    if (typeof data.post_action !== 'undefined') {
        // the variable is defined
        switch (data.post_action)
        {
            case 'reload':
                location.reload();
                break;
            default:
                location.reload();
        }
    }
    else
        location.reload();
}

function errorForm(xhr, ajaxOptions, thrownError)
{
    alerte.send("Erreur lors de l'envoi du formulaire\nDétails : " + xhr.status + " / " + thrownError, 'error');

    return;

    // TODO CMR: précédemment, le success pouvait avoir un "false" comme valeur de retour et avoir un nouveau
    // formulaire avec les messages d'erreur en retour. Sauf que c'est pas terroche, il faut tester ici le
    // code d'erreur et si c'est "bad arguments" afficher le form avec les erreurs (ci-dessous)

    /* Remove old modal and show new with fields errors */
    $("[id^=modal-]").remove();
    $(data).modal('show');

    /* Add error */
    alerte.send("Erreur lors de l'envoi du formulaire");
}
