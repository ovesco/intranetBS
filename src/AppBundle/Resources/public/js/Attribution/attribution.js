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