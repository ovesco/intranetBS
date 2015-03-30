$('.ui.search')
    .search({
        apiSettings: {
            url: Routing.generate('interne_famille_search')+'?pattern={query}'
        }
    })
;