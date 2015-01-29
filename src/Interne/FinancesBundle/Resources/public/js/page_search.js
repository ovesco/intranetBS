jQuery(document).ready(function() {

    initPage();

});

function initPage(){
    //activation du menu
    $('#search-infos-context .menu .item').tab({
        context: $('#search-infos-context')
    });
}



function sendSearch()
{
    var data = $('#searchForm').serialize();

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_search'),
        data: data,
        error: function() { alerte.send('erreur','danger',4000); },
        success: function() {

            modalDisplayClose();

            reloadPage();
        }
    });
}

function reloadPage()
{

    $.ajax({
        type: "POST",
        data: 1,
        url: Routing.generate('interne_fiances_search_load_results_ajax'),
        error: function() { alerte.send('erreur','danger',4000); },
        success: function(htmlResponse) {

            //rempalce le nouveau contenu
            $('#search-infos-context').replaceWith(htmlResponse);

            //activation du menu
            $('#search-infos-context .menu .item').tab({
                context: $('#search-infos-context')
            });

            initDataListe();
            initPage();
        }
    });



}



function outOfSearch(id,type)
{
    var data = { id: id, type: type};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_search_out_of_search_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            loadResults();

        }
    });
}


