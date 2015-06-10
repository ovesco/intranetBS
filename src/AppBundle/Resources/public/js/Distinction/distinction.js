$('#ajouter-distinction').click(function () {
    var idMembre = {idMembre: $(this).data('membre-id')};
    getModal(idMembre, Routing.generate('obtention-distinction_add_modal'));
});


function removeDistinction(distinctionId) {

    if (confirm('Sur ?')) {
        $.ajax({
            url: Routing.generate('distinction_delete', {distinction: distinctionId}),
            type: 'GET',
            success: function () {
                return true;
            },
            error: function (data) {
                alert(JSON.stringify(data));
                return false;
            }
        });
    }
}

