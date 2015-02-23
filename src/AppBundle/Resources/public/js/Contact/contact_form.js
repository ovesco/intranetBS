$(document).ready(function(){
    AdresseHighlight();
});


$(".contact_adresse").change(function(){
    AdresseHighlight();
});



function AdresseHighlight()
{
    $('.contact_adresse').each(function(){

        var adresseBlock = this;

        var expediable = adresseBlock.find('.expediable').firstChild().val();

        alert(expediable);

    });


}