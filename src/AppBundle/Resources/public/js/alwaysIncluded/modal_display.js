function getModal(data, url) {
     $.ajax({
        type: "POST",
        url: url,
        data: data,
        error: function(xhr, ajaxOptions, thrownError) { alerte.send("Erreur lors de l'ouverture de la fenêtre.<br />Détails : " + xhr.status + " / " + thrownError, 'error'); },
        success: function(htmlResponse) {
            $(htmlResponse).modal('show');

            //$(htmlResponse).find("script").each(function(i) {
            //    eval($(this).text());
            //});
        }
    });
}
