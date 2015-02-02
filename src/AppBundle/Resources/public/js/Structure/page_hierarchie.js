$('.addChildGroupe').click(function(){

    var idParent = $(this).data('id');
    displayModalGroupeForm(idParent,null);
});

$('.editGroupe').click(function(){
    var idGroupe = $(this).data('id');
    displayModalGroupeForm(null,idGroupe);
});


$('.addGroupeRacine').click(function(){
    displayModalGroupeForm(null,null);

});



function displayModalGroupeForm(idParent,idGroupe){

    //on récupère les valeur du formulaire
    var data = {idParent:idParent, idGroupe:idGroupe};
    $.ajax({
        type: "POST",
        url: Routing.generate('groupe_get_form_modale'),
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