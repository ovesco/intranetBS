$(document).ready(function(){
    AdresseHighlight();
});


$("input").change(function(){
    AdresseHighlight();
});



function AdresseHighlight()
{
    $('.adresse').each(function(){

        var adresseBlock = this;

        var expediable = adresseBlock.find('.expediable').firstChild().val();

        alert(expediable);

    });


}