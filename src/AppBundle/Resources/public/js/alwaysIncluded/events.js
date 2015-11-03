$('.event').click(function () {

    var route = Routing.generate($(this).data('event-route'), $(this).data('event-parameters'));

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

$('.event_mass').click(function () {

    var $table = $(this).closest('.data-list').find('table');
    var routeName = $(this).data('event-route');

    var $selected = $table.find('tr.selected');

    var selectedParameters = [];
    $selected.each(function (i) {
        selectedParameters.push($(this).find('[data-event-route=' + routeName + ']').data('event-parameters'));
    });

    if ($(this).data('event-postactions').indexOf('ShowModal') >= 0) {
        getModal(null, route);
        return;
    }
    else {
        selectedParameters.forEach(function (line) {
            var route = Routing.generate(routeName, line);
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