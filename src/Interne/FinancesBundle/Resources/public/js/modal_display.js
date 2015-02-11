function getModal(data,url)
{
     $.ajax({
        type: "POST",
        url: url,
        data: data,
        error: function() { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {
            $(htmlResponse).modal('show');
        }
    });
}





