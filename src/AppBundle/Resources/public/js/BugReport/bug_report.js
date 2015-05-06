$(document).on('click','#bug_report',function(){

    $.ajax({
        type: "POST",
        url: Routing.generate('debug_report'),
        data: {url: window.location.href, html:document.documentElement.innerHTML},
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur at loading: Bug report','danger'); },
        success: function(response) {
            $(response).modal('show');
        }
    });

});

$(document).on('click','#send_bug_report',function(){
    $('#bug_report_modal').modal('hide');
        $.ajax({
            type: "POST",
            url: Routing.generate('debug_report'),
            data: $('#bug_report_form').serialize(),
            error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur at loading: Bug report','danger'); },
            success: function(response) {
                alerte.send(response);
            }
        });
});