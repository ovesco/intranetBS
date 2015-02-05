function displayFormEnvoiDocument(idGroupe,tokenListe){

    var data = {idGroupe:idGroupe, tokenListe:tokenListe};
    $.ajax({
        type: "POST",
        url: Routing.generate('utils_envoi_document_form'),
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
