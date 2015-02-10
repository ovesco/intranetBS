$('.addNews').click(function(){
    displayModaleNewsForm();
});

function displayModaleNewsForm(){

    var data = null;
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_organisation_news_get_form'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) {   },
        success: function(response) {
            $(response).modal('show');
        }
    });
}


function loadMoreNews()
{
    var $newsContainer = $('#newsContainer');
    var $loader = $('#newsLoader');
    $loader.addClass('active');
    var data = {numberOfNews: $newsContainer.find('.one_news').length };
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_organisation_news_load_more'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) {},
        success: function(response) {
            $newsContainer.append(response);
            $loader.removeClass('active');
        }
    });
}

/**
 * Declanchement a la fin du scroll de la page
 */
$(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
        loadMoreNews();
    }
});