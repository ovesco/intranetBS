$('.event').click(function () {

    var event_container = $(this);

    var route = Routing.generate(event_container.data('event-route'), event_container.data('event-parameters'));

    if (event_container.data('event-postactions').indexOf('ShowModal') >= 0) {
        getModal(null, route);
        return null;
    }
    else {
        var callback = {
            hasError: false,
            postAction: $(this).data('event-postactions'),
            handler: function () {

                if (this.hasError)
                    return;

                switch (this.postAction) {
                    case '':
                        break;
                    case 'Link':
                        window.location.href = route;
                        break;
                    case 'RefreshList':
                    case 'RefreshPage':
                        location.reload();
                        break;

                    default:
                        alert('Unknown post action ' + $(this).data('event-postactions'));
                        break;
                }
            }
        };

        $.ajax({
            url: route,
            type: 'GET',
            success: [callback, function () {
                callback.handler(); // callback
            }],
            error: [callback, function (xhr, status, thrownError) {
                alerte.send(xhr.statusText);
                callback.hasError = true;
                callback.handler(); // callback
            }]
        });
    }
});


$('.event_mass').click(function () {

    var event_container = $(this).find('[data-event-route]');

    var $table = $(this).closest('.data-list').find('table');
    var routeName = event_container.data('event-route');

    var $selected = $table.find('tr.selected');

    var selectedParameters = [];
    $selected.each(function (i) {
        selectedParameters.push(event_container.find('[data-event-route=' + routeName + ']').data('event-parameters'));
    });

    if (event_container.data('event-postactions').indexOf('ShowModal') >= 0) {
        getModal(null, routeName);
        return null;
    }
    else {
        var callback = {
            pendingEventsCount: selectedParameters.length,
            hasError: false,
            postAction: $(this).data('event-postactions'),
            handler: function () {
                this.pendingEventsCount--;

                if (this.pendingEventsCount > 0 || this.hasError)
                    return;

                switch (this.postAction) {
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
            }
        };

        function ajaxQuery(line) {
            var route = Routing.generate(routeName, line);
            var callback = this;
            $.ajax({
                url: route,
                type: 'GET',
                success: [callback, function () {
                    callback.handler(); // callback
                }],
                error: [callback, function (xhr, status, thrownError) {
                    alerte.send(xhr.statusText);
                    callback.hasError = true;
                    callback.handler(); // callback
                }]
            });
        }

        selectedParameters.forEach(ajaxQuery, callback);
    }
});
