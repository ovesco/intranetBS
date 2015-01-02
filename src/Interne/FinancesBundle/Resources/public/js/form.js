
/*
 * Permet de switcher entre un formulaire de valeur fixe à un intervale de valeurs.
 */
function searchSwitch(element){

    $(element).closest('.searchSwitch').find('.optionSearch').each(function(){
        if($(this).is(':visible'))
        {
            $(this).hide();
        }
        else
        {
            $(this).show();
        }
    });
}