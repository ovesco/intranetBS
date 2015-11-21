jQuery(document).ready(function() {

    linkActionOfPage();

});


function reloadPage()
{
    var id = $('#owner-entity-id').val();
    var type = $('#owner-entity-type').val();
    var data = { ownerId: id, ownerType: type};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_interface_reload_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur lors du chargement','danger');  },
        success: function(htmlResponse) {

            //cherche le nouveau contenu
            $interfaceContent = $(htmlResponse).find('#infoFinanceContent');

            //rempalce le nouveau contenu
            $('#infoFinanceContent').replaceWith($interfaceContent);

            /**
             * On réinitialise les listes.
             */
            initDataListe();
            /**
             * On réinitialise la page
             */
            linkActionOfPage();

        }
    });
}




function linkActionOfPage()
{
    /**
     * Gestion des boutons de l'interface
     */
    $('#interface-add-creance').click(function () {
        var ownerId = $(this).data('id');
        var ownerClass = $(this).data('class');

        openCreanceForm(ownerId, ownerClass);

    });
}


