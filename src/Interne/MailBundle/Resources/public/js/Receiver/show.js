/**
 * Created by NicolasUffer on 16.11.15.
 */

$(document).ready(function(){

    $('#addMailToReceiver').click(function(){
        var id = $(this).data('id');
        getModal(null,Routing.generate('interne_mail_mail_addtoreceiver',{receiver: id}));
    });

});