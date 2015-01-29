jQuery(document).ready(function() {

    /**
     * Gestion des évenement envoyé par les liste de créance et de facture.
     */
    document.addEventListener('data-liste-event', function (e) {


        switch(e.detail.name){
            case 'event_voir_facture':
                openFactureShow(e.detail.data);
                break;
            case 'event_delete_facture':
                deleteFactureFromPage(e.detail.data);
                break;
            case 'event_voir_creance':
                openCreanceShow(e.detail.data,'interface');
                break;
            case 'event_delete_creance':
                deleteCreanceFromPage(e.detail.data);
                break;

            case 'event_send_facture':
                factureEnvoi(e.detail.data);
                break;

            case 'event_print_facture':
                printFacture(e.detail.data);
                break;

            case 'event_add_rappel':
                openRappelForm(e.detail.data);
                break;

            case 'event_masse_facturation_creance':
                createFactureFromPage(e.detail.data);
                break;

            case 'event_masse_ajout_rappel':
                setTemporaryStorage(e.detail.data);
                openRappelForm(null);
                break;
            case 'event_masse_delete_facture':
                deleteListeFactureFromPage(e.detail.data);
                break;



        }
    }, false);

});


