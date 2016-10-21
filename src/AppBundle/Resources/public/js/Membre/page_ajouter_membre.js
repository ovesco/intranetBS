$(function () {

    var $add_form_container = $('#membre_add_form');

    $add_form_container.on('click','#previous',function(){
        $add_form_container.find('.dimmer').addClass('active');
        var previous_url = $(this).data('previous');
        AddMembreCallPreviousForm(previous_url);
    });

    $add_form_container.on('click','#next',function(){
        $add_form_container.find('.dimmer').addClass('active');
        var next_url = $(this).data('next');
        AddMembreCallNextForm(next_url);
    });
});

function AddMembreCallNextForm(next_url){
    $.ajax({
        url: next_url,//Routing.generate('membre_add_form',{step: current_step}),
        type: 'POST',
        data: $('#membre_add_form form').serialize(),
        success: function (response) {
            $('#membre_add_form').html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alerte.send("Erreur lors de l'ouverture de la fenêtre.<br />Détails : " + xhr.status + " / " + thrownError, 'error');
        }
    });
}

function AddMembreCallPreviousForm(previous_url){
    $.ajax({
        url: previous_url,//Routing.generate('membre_add_form',{step: current_step}),
        type: 'GET',
        success: function (response) {
            $('#membre_add_form').html(response);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alerte.send("Erreur lors de l'ouverture de la fenêtre.<br />Détails : " + xhr.status + " / " + thrownError, 'error');
        }
    });
}



/*

var search = $('.ui.search.familleSearch')
    .search({
        apiSettings: {
            url: Routing.generate('interne_famille_search')+'?pattern={query}'
       },
        onSelect: function(result,response){
            //result is an event.
            alert('test');
        }
    });

*
$('.familleSearch > input').change(function(){

    var query = $(this).val();



    $.ajax({
        url: Routing.generate('interne_famille_search')+'?pattern='+ query,
        type: 'GET',
        success: function (response) {

            setChoices(response);

        },
        error: function () {

        }
    });

});

function setChoices(response)
{
    response = jQuery.parseJSON(response);

    response['results'].forEach(function(result){


        $('#familleSelect').append('<option value="'+result['id']+'">'+result['title']+'</option>');

    });

    $('.ui.dropdown')
        .dropdown()
    ;
}

    */