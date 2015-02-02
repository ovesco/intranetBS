$('.addFonction').click(function(){

    displayModalFonctionForm(null)

});

$('.editFonction').click(function(){
    var id = $(this).data('id');
    displayModalFonctionForm(id);
});



function displayModalFonctionForm(idFonction){

    //on récupère les valeur du formulaire
    var data = {idFonction:idFonction};
    $.ajax({
        type: "POST",
        url: Routing.generate('fonction_get_form_modale'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) {   },
        success: function(response) {

            $(response).modal('show');


        }
    });
}