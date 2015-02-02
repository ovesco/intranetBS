$('.addGroupeModelForm').click(function(){
    displayModalGroupeModelForm(null);
});

$('.editGroupeModel').click(function(){
    var id = $(this).data('id');
    displayModalGroupeModelForm(id);
});


function displayModalGroupeModelForm(idGroupeModel){

    //on récupère les valeur du formulaire
    var data = {idGroupeModel:idGroupeModel};
    $.ajax({
        type: "POST",
        url: Routing.generate('groupe_model_get_form_modale'),
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