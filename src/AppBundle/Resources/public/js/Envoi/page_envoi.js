$('.removeByToken').click(function () {

    var token = $(this).data('token');
    var $tr = $(this).closest('tr');

    $.ajax({
        type: "POST",
        url: Routing.generate('utils_envoi_remove_by_token')+'/'+token,

        error: function(jqXHR, textStatus, errorThrown) {   },
        success: function(response) {

            $tr.remove();

        }
    });

});


$('.envoisProcess').click(function () {

    var url = Routing.generate('utils_envoi_process');

    /*
     * Ouvre le pdf dans une nouvelle fenetre
     */
    window.open(url,'Envois PDF');

});



