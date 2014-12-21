function deleteFacture(element){
    var id = $(element).data("id");
    var data = { idFacture: id};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_facture_delete_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alert('erreur'); },
        success: function(htmlResponse) {

            $('#flash-container').html(
                    '<div class="row">' +
                    '<div class="alert alert-info alert-dismissible col-lg-6 col-lg-offset-3" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '<span class="sr-only">Close</span>' +
                    '</button><strong>Facture suprimée</strong>' +
                    '</div>' +
                    '</div>');

            //cherche le nouveau contenu
            $listeCreanceContent = $(htmlResponse).filter('#listeFactureContent');

            //rempalce le nouveau contenu
            $('#listeFactureContent').replaceWith($listeCreanceContent);

            //Redessine la table
            $('#listeFacturesTable').dataTable();
        }
    });
}