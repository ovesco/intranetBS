/**
 * Cette fonction initialise les listes présente sur la page qui vient de ce charger.
 */
jQuery(document).ready(function() {

    $('.data-liste').each(function(){

        var content = $(this).find('.data-liste-content');
        data_liste.init(content);

        var toolbar = $(this).find('.data-liste-toolbar');
        data_liste.initToolBar(toolbar);

    });

});


/**
 * Objet data_liste qui est instencier pour charque liste présente sur la page.
 *
 *
 *
 */
var data_liste = {

    /**
     * Initialisation de la partie "content"
     * @param table
     */
    init: function(table){

        //plus simple pour la nomenclatur
        var self = this;

        this.table = table;

        this.rows = [];


        /**
         * Lie une serie d'evenement pour chaque ligne.
         * La séléction de la ligne et les acitons si il y en a...
         */
       $(this.table).find('tbody').find('tr').each(function(){

           var id = $(this).data('id');

           self.rows[id] = 0; //set as inactive

           $(this).bind({
               click: function() {
                   var id = $(this).data('id');

                   if($(this).hasClass('active'))
                   {
                       $(this).removeClass('active');
                       self.rows[id] = 0;//set as inactive
                   }
                   else{
                       $(this).addClass('active');
                       self.rows[id] = 1;//set as active
                   }
               }
           });

           $(this).find('.data-liste-row-action').each(function(){
               $(this).bind({
                   click: function(e) {
                       //evite que l'evenement selection aussi la ligne
                       e.stopPropagation();

                       //récupération des infos de l'action
                       var id = $(this).data('id');
                       var event = $(this).data('event');

                       //envoi de l'event.
                       self.sendEvent(event,id);
                   }
               });
           });

       });





    },

    /**
     * Initialisaiton de la partie toolbar de la liste.
     *
     * @param toolbar
     */
    initToolBar:function(toolbar){

        var self = this;

        $(toolbar).find('.data-liste-button ').each(function(){

            var id = $(this).data('id');

            switch(id) {
                case 'test':
                    $(this).bind({
                        click: function() {
                            self.getSelectedIds();
                        }
                    });
                    break;
            }
        });

    },

    /**
     * renvoie tout les id des lignes selectionées.
     */
    getSelectedIds: function()
    {

        var selectedIds = [];

        for (var id in this.rows) {
            if(this.rows[id] == 1)
            {
                selectedIds.push(id);
            }
        }
        return selectedIds;
    },

    /**
     * Envoi d'un événement. doit être récupéré dans les fichiers js des page en vue d'une action spécifique.
     * @param eventName
     * @param eventData
     */
    sendEvent: function (eventName,eventData) {
        var event = new CustomEvent('data-liste-event', { 'detail': {'name':eventName, 'data':eventData} });
        document.dispatchEvent(event);
    }



};