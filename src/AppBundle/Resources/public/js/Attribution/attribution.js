$('#ajouter-attribution').click(function () {
    getModal(null, Routing.generate('attribution_add_modal', {membre: $(this).data('membre-id')}));
});

$('#modifier-attribution').click(function () {
    getModal(null, Routing.generate('attribution_edit_modal', {attribution: $(this).data('attribution-id')}));
});

$('#terminer-attribution').click(function () {
    var dateFin = 0;

    if (terminateAttribution($(this).data('attribution-id'), dateFin))
        $(btn).parent().parent().remove();
});

$('#supprimer-attribution').click(function () {
    if (removeAttribution($(this).data('attribution-id')))
        $(btn).parent().parent().remove();
});


function terminateAttribution(attributionId, dateFin) {
    $.ajax({
        url: Routing.generate('attribution_terminate', {attribution: attributionId, dateFin: dateFin}),
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


function removeAttribution(attributionId) {

    if (confirm('Sur ?')) {
        $.ajax({
            url: Routing.generate('attribution_delete', {attribution: attributionId}),
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
