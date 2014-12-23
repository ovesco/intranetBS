function deleteCreance(element){
    var id = $(element).data("id");
    var data = { idCreance: id};
    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_delete_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger');  },
        success: function(htmlResponse) {
            //cherche le nouveau contenu
            $listeCreanceContent = $(htmlResponse).filter('#listeCreanceContent');

            //rempalce le nouveau contenu
            $('#listeCreanceContent').replaceWith($listeCreanceContent);

            alerte.send('Créance supprimée','info');
        }
    });
}

/*
 * Ajoute une créance à un membre
 *
 * (l'ajout de créance à une liste de membre ne se fait pas ici mais dans adder.js)
 */

function addCreance(){

    //on récupère les valeur du formulaire
    var form = $('#addCreanceForm').serialize();

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_creance_add_ajax'),
        data: form,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {

            //cherche le nouveau contenu
            $listeCreanceContent = $(htmlResponse).filter('#listeCreanceContent');

            //rempalce le nouveau contenu
            $('#listeCreanceContent').replaceWith($listeCreanceContent);

            alerte.send('Créance ajoutée','success',2000);

        }
    });
}






/*
 * envoie en Ajax de la liste des créances à facturer
 */
function createFactureWithSelectedCreances(fromPage){

    var listeCreance = [];

    //on récupère la liste des créances cochée
    $('.selectCreance:checked').each(function() {
        listeCreance.push($(this).val());
    });

    var data = { fromPage: fromPage, listeCreance: listeCreance};

    $.ajax({
        type: "POST",
        url: Routing.generate('interne_fiances_create_factures_ajax'),
        data: data,
        error: function(jqXHR, textStatus, errorThrown) { alerte.send('Erreur','danger'); },
        success: function(htmlResponse) {


            if((fromPage == 'Membre')||(fromPage == 'Famille'))
            {
                //cherche le nouveau contenu
                $listeCreanceContent = $(htmlResponse).filter('#listeCreanceContent');

                //rempalce le nouveau contenu
                $('#listeCreanceContent').replaceWith($listeCreanceContent);


                //cherche le nouveau contenu
                $listeCreanceContent = $(htmlResponse).filter('#listeFactureContent');

                //rempalce le nouveau contenu
                $('#listeFactureContent').replaceWith($listeCreanceContent);

                alerte.send('Facture crée','success');


            }
            else if(fromPage == 'Search')
            {
                //nothing to do
            }

        }
    });
}

/*
 * Selection/deséléction de toutes les créances
 */

function selectAllCreances(box)
{
    var $table = $(box).closest('.table');

    if(box.checked)
    {
        $table.find('input.selectCreance').each(function() {
            this.checked = true;

        });
    }
    else
    {
        $table.find('input.selectCreance').each(function() {
            this.checked = false;

        });
    }
}