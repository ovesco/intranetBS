
/*
 * Pour chaque attribut no-editable, on va génerer une popup qui lui est liée histoire de pouvoir afficher du beau html
 * à l'intérieur
 */


$('.no-editable').each(function(id, obj) {

    var brut  = window.atob($(obj).attr('data-content'));
    var id    = 'popup-' + Math.floor((Math.random() * 9999) + 1);
    var popup = '<div id="' + id + '" class="ui wide popup">' + brut + '</div>';

    $(obj).append(popup);

    $(obj).popup({popup:'#' + id, position:'right center'});
});