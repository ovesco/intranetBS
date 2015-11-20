function getModal(data, url) {

    /* Delete all existing modals */
    $("[id^=modal-]").remove();

    /* Ask for modal content an show it */
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        error: function(xhr, ajaxOptions, thrownError) {
            alerte.send("Erreur lors de l'ouverture de la fenêtre.<br />Détails : " + xhr.status + " / " + thrownError, 'error');
        },
        success: function(htmlResponse) {
            $(htmlResponse).modal('show');
        }
    });
}

$(document).ready(function(){

    /**
     * Appel une modal (cf. Twig:AppExtension:modal_caller)
     */
    $(document).on('click','.modal_caller',function(){
        var url = $(this).data('modal-url');
        getModal(null,url);

    });

});