$('.event').click(function () {

    var params = $(this).data('event-parameters');

    //params = '{"attribution":"88"}';
    //params = JSON.parse(params);

    var route = Routing.generate($(this).data('event-route'), params);
    //var route = Routing.generate($(this).data('event-route'), $(this).data('event-parameters'));

    if ($(this).data('event-postactions').indexOf('ShowModal') >= 0) {
        getModal(null, route);
        return;
    }
    else {
        $.ajax({
            url: route,
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


    switch ($(this).data('event-postactions')) {
        case '':

            break;

        case 'RefreshList':
        case 'RefreshPage':
            location.reload();
            break;

        default:
            alert('Unknown post action ' + $(this).data('event-postactions'));
            break;
    }

});