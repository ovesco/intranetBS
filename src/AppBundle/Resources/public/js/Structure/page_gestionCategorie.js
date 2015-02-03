$('.addCategorieForm').click(function(){
    displayModaleCategorieForm(null);
});

$('.editCategorie').click(function(){
    var id = $(this).data('id');
    displayModaleCategorieForm(id);
});


function displayModaleCategorieForm(idCategorie){

    //on récupère les valeur du formulaire
    var data = {idCategorie:idCategorie};
    $.ajax({
        type: "POST",
        url: Routing.generate('categrorie_get_form_modale'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) {   },
        success: function(response) {

            $(response).modal('show');
            $('.ui.dropdown')
                .dropdown()
            ;

        }
    });
}