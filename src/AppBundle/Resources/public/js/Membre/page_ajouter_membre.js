



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

*/
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