jQuery(document).ready(function() {
    //affiche le modal une fois que le chargement de la page est terminé
    $('#modal-facture-searchForm').modal('show');

});

function sendSearch()
{
    var data = $('#searchForm').serialize();
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_facture_search_form'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alert('erreur'); },
        success: function(htmlResponse) {

            $('#modal-facture-searchForm').modal('hide');

            //cherche le nouveau contenu
            $factureSearchContent = $(htmlResponse).filter('#factureSearchContent');



            //rempalce le nouveau contenu
            $('#factureSearchContent').replaceWith($factureSearchContent);

            //Redessine la table
            $('#factureSearchTable').dataTable();


            //cherche le nouveau contenu
            $newCreanceContent = $(htmlResponse).filter('#creanceSearchContent');

            //rempalce le nouveau contenu
            $('#creanceSearchContent').replaceWith($newCreanceContent);

            //Redessine la table
            $('#creanceSearchTable').dataTable();



        }
    });

}


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

function switchFactureForm(element){

    var value = $(element).find('option:selected').val();

    if(value == 'yes')
    {
        $('#searchFactureForm').show();
    }
    else
    {
        $('#searchFactureForm').hide();
    }

}

/*
 * Selection/deséléction de toutes les créances
 */

function selectAllCreances(box)
{
    var table = $('#creanceSearchTable').dataTable();

    if(box.checked)
    {
        $('input.selectCreance', table.fnGetNodes()).each(function() {
            this.checked = true;

        });
    }
    else
    {
        $('input.selectCreance', table.fnGetNodes()).each(function() {
            this.checked = false;

        });
    }
}