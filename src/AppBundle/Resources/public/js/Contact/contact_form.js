$(document).ready(function(){

    $('.ui.checkbox').checkbox();



    $('.contact_adresse').find('.checkbox').click(function(){
        modifyAdresseCible(this);
        AdresseHighlight();
    });

    $('.contact_emails').find('.checkbox').click(function(){
        modifyEmails(this);
        EmailsCheckboxSetup();
    });


    $('.contact_add_telephone').click(function(){

        var data = {idContact: $(this).data('id') };
        var url = Routing.generate('get_form_modale_telephone');
        getModal(data, url);

    });

    $('.contact_add_email').click(function(){

        var data = {idContact: $(this).data('id') };
        var url = Routing.generate('get_form_modale_email');
        getModal(data, url);

    });


    AdresseHighlight();
    EmailsCheckboxSetup();




});

function modifyAdresseCible(checkBox)
{
    //si checked alors volonté de selectionner cette adresse.
    var checked = $(checkBox).checkbox('is checked');

    //on iter sur chaque bloc d'adresse
    $('.contact_adresse').each(function(){

        //on prend la checkbox du bloc d'adresse
        var $checkBox = $(this).find('.ui.checkbox');

        //on regarde si il s'agit de la meme checkbox que celle qui a été cliquée.
        if($(checkBox).attr('id') == $checkBox.attr('id'))
        {

            var $expediableHidden = $(this).find('.expediable').children().first();
            var expediableText = $expediableHidden.text();
            var expediable = ($.trim(expediableText) === '1');


            if(expediable != checked)
            {
                editable.init($expediableHidden);

                if(checked)
                {
                    //on met l'adresse comme expediable
                    $(this).find('input').val(1);
                }
                else
                {
                    //on met l'adresse comme non exepediable
                    $(this).find('input').val(0);
                }

                editable.apply($expediableHidden);
            }
        }

    });
}




function AdresseHighlight()
{
    var foundExpediable = false;
    $('.contact_adresse').each(function(){

        var $checkBox = $(this).find('.ui.checkbox');
        var $expediableHidden = $(this).find('.expediable').children().first();

        var expediableClass = $expediableHidden.attr('class');

        if(expediableClass == 'editable')
        {
            $checkBox.checkbox('enable');
        }
        else
        {
            $checkBox.checkbox('disable');
            $checkBox.unbind( "click" ); //remove event handler attached to this element.
        }


        if(!foundExpediable)
        {

            var expediableText = $expediableHidden.data('value');
            var expediable = ($.trim(expediableText) === '1');

            if(expediable)
            {
                //active uniquement la première adresse trouvée qui est expediable
                foundExpediable = true;
                $(this).addClass('green');
                $checkBox.checkbox('check');
            }
            else
            {
                $(this).removeClass('green');
                $checkBox.checkbox('uncheck');
            }
        }
        else{
            $(this).removeClass('green');
            $checkBox.checkbox('uncheck');
        }



    });


}

function EmailsCheckboxSetup()
{

    $('.contact_emails').each(function(){

        $('.contact_emails_item').each(function(){

            var $checkBox = $(this).find('.ui.checkbox');
            var $expediableHidden = $(this).find('.expediable').children().first();

            var expediableClass = $expediableHidden.attr('class');

            if(expediableClass == 'editable')
            {
                $checkBox.checkbox('enable');
            }
            else
            {
                $checkBox.checkbox('disable');
            }

            var expediableText = $expediableHidden.text();
            var expediable = ($.trim(expediableText) === '1');

            if(expediable)
            {
                $checkBox.checkbox('check');
            }
            else
            {
                $checkBox.checkbox('uncheck');
            }

        });

    });
}


function modifyEmails(checkbox){

    var $email = $(checkbox).closest('.contact_emails_item');

    var $expediableHidden = $email.find('.expediable').children().first();
    var expediableText = $expediableHidden.text();
    var expediable = ($.trim(expediableText) === '1');

    var checked = $(checkbox).checkbox('is checked');

    if(expediable != checked)
    {
        editable.init($expediableHidden);

        if(checked)
        {
            //on met l'adresse comme expediable
            $(this).find('input').val(1);
        }
        else
        {
            //on met l'adresse comme non exepediable
            $(this).find('input').val(0);
        }

        editable.apply($expediableHidden);
    }

}