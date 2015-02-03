$('.addModelForm').click(function(){
    displayModaleModelForm(null);
});

$('.editModel').click(function(){
    var id = $(this).data('id');
    displayModaleModelForm(id);
});


function displayModaleModelForm(idModel){

    //on récupère les valeur du formulaire
    var data = {idModel:idModel};
    $.ajax({
        type: "POST",
        url: Routing.generate('model_get_form_modale'),
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