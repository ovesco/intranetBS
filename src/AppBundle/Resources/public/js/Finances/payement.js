$(document).ready(function () {

    /**
     * Gestion des évenement envoyé par les listes pour les payements.
     */
    document.addEventListener('data-liste-event', function (e) {


        switch(e.detail.name){
            case 'event_show_payement':
                showPayement(e.detail.data);
                break;


        }
    }, false);

});



/**
 *
 * @param id
 */
function showPayement(id)
{
    var url = Routing.generate('interne_finances_payement_show',{'payement':id});
    getModal(null,url);
}

