$(function () {

    /**
     * Gestion des évenement envoyé par les liste de créance et de facture.
     */
    document.addEventListener('data-liste-event', function (e) {


        switch(e.detail.name){


            case 'event_voir_creance':
                openCreanceShow(e.detail.data,'interface');
                break;
            case 'event_delete_creance':
                deleteCreance(e.detail.data);
                break;

            case 'event_add_rappel':
                openRappelForm(e.detail.data);
                break;

            case 'event_masse_facturation_creance':
                createFactureWithListeCreances(e.detail.data);
                break;

            case 'event_masse_ajout_rappel':
                setTemporaryStorage(e.detail.data);
                openRappelForm(null);
                break;

            case 'event_masse_delete_creance':
                deleteListeCreance(e.detail.data);
                break;


        }
    }, false);

});


