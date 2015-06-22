$(function () {
    /**
     * Gestion des évenement, utilisés notemment par les listes
     */
    document.addEventListener('data-liste-event', function (e) {

        switch (e.detail.name) {
            case 'event_masse_membre_attribution_ajout':
                getModal(null, Routing.generate('attribution_add_modal', {membre: e.detail.data}));
                break;

            case 'event_masse_membre_distinction_ajout':
                getModal(null, Routing.generate('obtention-distinction_add_modal', {membre: e.detail.data}));
                break;

            case 'event_masse_membre_creance_ajout':
                break;

            case 'event_masse_membre_export_pdf':
                break;

            case 'event_masse_membre_export_csv':
                break;

            case 'event_masse_membre_export_publipostage':
                break;

        }

    }, false);
});


