$('.ui.search.familleSearch')
    .search({
        apiSettings: {
            url: Routing.generate('interne_famille_search')+'?pattern={query}'
        },
        onSelect: function(result,response){
            var test = $(this).search('get value');
            alert(test);
        }
    })
;
