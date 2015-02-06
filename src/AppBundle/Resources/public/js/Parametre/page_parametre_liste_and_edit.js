/*
 * Cette fonction a pour but de faire des modifications en ajax sur les parametres.
 */
$('.formParametre').change(function() {

    var value = $(this).val();
    var groupe = $(this).data('groupe');
    var parametre = $(this).data('parametre');
    var data = {value: value, groupe:groupe, parametre:parametre};

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_parametre_update_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {
            alerte.send('Modification effectuée','success',2000);
        }
    });
});

jQuery(document).ready(function() {

    $('.ui.accordion').accordion();


});