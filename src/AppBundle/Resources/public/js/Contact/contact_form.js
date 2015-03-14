$(document).ready(function(){

    $('.ui.checkbox').checkbox();

    AdresseHighlight();
    EmailsCheckboxSetup();

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




});

function modifyAdresseCible(checkBox)
{
    $('.contact_adresse').each(function(){

        var $checkBox = $(this).find('.ui.checkbox');

        if($(checkBox).attr('id') == $checkBox.attr('id'))
        {
            var checked = $checkBox.checkbox('is checked');

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
        }


        if(!foundExpediable)
        {

            var expediableText = $expediableHidden.text();
            var expediable = ($.trim(expediableText) === '1');

            if(expediable)
            {
                //active uniquement la première adresse trouvée qui est expediable
                foundExpediable = true;
                $(this).addClass('active');
                $checkBox.checkbox('check');
            }
            else
            {
                $(this).removeClass('active');
                $checkBox.checkbox('uncheck');
            }
        }
        else{
            $(this).removeClass('active');
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