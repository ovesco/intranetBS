function getGraph(idGraph){

    var options = {};

    // le formulaire doit avoir le meme id que le nom du graphique (idGraph).
    var $form = $('#'+idGraph);

    $form.find('.graph_option option:selected').each(function(){
        var name = $(this).attr('name');
        var value = $(this).val();
        options[name] = value;
    })

    var data = { idGraph: idGraph, options:options };




    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_statistics_get_graph'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(graphData) {
            $('#container').highcharts(graphData);
        }
    });


}

jQuery(document).ready(function() {

    $('.ui.accordion')
        .accordion()
    ;

    $('.ui.dropdown')
        .dropdown()
    ;

});