$('#ajouter-distinction').click(function () {
    getModal(null, Routing.generate('obtentiondistinction_add_modal', {membre: $(this).data('membre-id')}));
});

$('#supprimer-distinction').click(function () {
    if (removeDistinction($(this).data('distinction-id')))
        $(btn).parent().parent().remove();
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

