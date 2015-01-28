/**
 * on stock les liste dans un tableau pour les garder instanciée.
 */
var listeArray =[];
var nbListe = 0;

/**
 * Peut être utiliser pour garder une liste d'ids en mémoire par exemple
 */
var temporaryStorage;

function setTemporaryStorage(value){
    temporaryStorage = value;
}
function getTemporaryStorage(){
    return temporaryStorage;
}



/**
 * Cette fonction initialise les listes présente sur la page qui vient de ce charger.
 */
jQuery(document).ready(function() {
    initDataListe();
});


function initDataListe()
{
    /**
     * Recherche de toute les listes sur la page et initialisaiton.
     */
    $('.data-liste').each(function(){
        listeArray[nbListe] = new dataListe(this);
        listeArray[nbListe].init();
        nbListe++;
    });
}

function dataListe(liste){

    this.liste = liste;

    this.name = $(this.liste).data('name');

    //recherche du contenu
    this.table = $(this.liste).find('.data-liste-table');

    //recherche de la toobar si existante.
    this.toolbar = $(this.liste).find('.data-liste-toolbar');

    this.init = function(){

        //plus simple pour la nomenclatur
        var self = this;

        //on sauve tout les lignes ici.
        this.tbody = $(self.table).find('tbody');

        this.rows = [];

        /**
         * Lie une serie d'evenement pour chaque ligne.
         * La séléction de la ligne et les acitons si il y en a...
         */
        $(this.table).find('tbody').find('tr').each(function(){

            var id = $(this).data('id');

            self.rows[id] = [];
            self.rows[id]['selected'] = false;//set as inactive
            self.rows[id]['hidden'] = false;
            self.rows[id]['html'] = $(this).html();



            /**
             * On lie les lignes à la possiblité d'être sélécitonnée.
             */
            $(this).bind({
                click: function() {
                    var id = $(this).data('id');

                    if (self.rows[id]['selected']) {
                        self.rows[id]['selected'] = false;//set as inactive
                    }
                    else {
                        self.rows[id]['selected'] = true;//set as active
                    }
                    self.showListe();
                }

            });

            /**
             * On lie les actions de chaque ligne à leur events.
             *
             * (a faire après la séléction pour la priorité des events)
             *
             */
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


        /**
         * Dans le cas ou il n'y pas de paging dans la toolbar
         */
        this.page = 1;
        //Dans le cas ou il y a pas de paging actif.
        this.paging = this.numberOfRows();

        self.initToolBar();



        /**
         * Affiche la liste avec le paging
         */
        self.showListe();

    };


    /**
     * selectionne toute les lignes
     */
    this.selectAllRows = function()
    {
        for (var id in this.rows) {
            this.rows[id]['selected'] = true;
        }
    };
    /**
     * déselectionne toute les lignes
     */
    this.unselectAllRows = function()
    {
        for (var id in this.rows) {
            this.rows[id]['selected'] = false;
        }
    };

    /**
     * renvoie tout les id des lignes selectionées.
     */
    this.getSelectedIds = function()
    {
        var selectedIds = [];
        for (var id in this.rows) {
            if(this.rows[id]['selected'])
            {
                selectedIds.push(id);
            }
        }
        return selectedIds;
    };

    /**
     * Envoi d'un événement. doit être récupéré dans les fichiers js des page en vue d'une action spécifique.
     * @param eventName
     * @param eventData
     */
    this.sendEvent = function (eventName,eventData) {
        var event = new CustomEvent('data-liste-event', { 'detail': {'name':eventName, 'data':eventData} });
        document.dispatchEvent(event);
    };


    /**
     * Initialisaiton de la partie toolbar de la liste.
     *
     */
    this.initToolBar = function(){

        var self = this;

        $(self.toolbar).find('.data-liste-button').each(function(){

            var event = $(this).data('event');

            $(this).bind({
                click: function() {
                    self.sendEvent(event,self.getSelectedIds());
                }
            });

        });

        /**
         * Réinitialisation du dropdown
         */
        $('.ui.dropdown').dropdown();

        /**
         * Initalisation de la bar de recherche.
         */
        $(self.toolbar).find('.data-liste-search').each(function(){

            $(this).bind({
                keyup: function() {
                    var searchString = $(this).val();
                    self.searchInRows(searchString);
                    self.toPageOne();
                    self.showListe();
                }
            });

        });

        /**
         * Initalisation des bouttons de selection/désélection
         */
        $(self.toolbar).find('.data-liste-button-selection').each(function(){

            $(this).bind({
                click: function() {
                    var action = $(this).data('action');
                    switch(action){
                        case 'select':
                            self.selectAllRows();
                            self.showListe();
                            break;
                        case 'unselect':
                            self.unselectAllRows();
                            self.showListe();
                            break;
                    }
                }
            });

        });


        /**
         * Initilaisation du paging
         */
        var paging = 0;
        $(self.toolbar).find('.data-liste-paging').find('.data-liste-paging-value').each(function(){

            //nombre d'element par page
            if(paging == 0)
            {
                self.paging = $(this).data('value');
                //affichage de la premiere page.
                self.page = 1;
            }
            paging++;

            $(this).bind({
                click: function() {

                    self.paging  = $(this).data('value');
                    self.toPageOne();
                    self.showListe();

                }
            });

        });

        $(self.toolbar).find('.data-liste-paging').find('.data-liste-paging-navig').each(function(){

            var action = $(this).data('action');

            /**
             * Initialisation des bouttons next/previous
             */
            switch(action){
                case 'next':
                    $(this).bind({
                        click: function() {
                            self.page++;
                            if(self.page > ((self.numberOfRows()/self.paging)+1))
                            {
                                self.page--;
                            }

                            self.resetPageIndicator();
                            self.showListe();


                        }
                    });
                    break;
                case 'previous':
                    $(this).bind({
                        click: function() {
                            self.page--;
                            if(self.page < 1)
                            {
                                self.page++;
                            }
                            self.resetPageIndicator();
                            self.showListe();


                        }
                    });
                    break;
            }

        });







    };

    /**
     * Prépare pour l'affichage de la page 1
     */
    this.toPageOne = function(){
        var self = this;
        self.page = 1;
        $(self.toolbar).find('.data-liste-paging').find('.data-liste-paging-active').text(self.page);

    };

    /**
     * Mise a jours de la valeur dans l'indicateur de
     */
    this.resetPageIndicator = function(){
        var self = this;
        $(self.toolbar).find('.data-liste-paging').find('.data-liste-paging-active').text(self.page);
    };




    this.showListe = function(){

        var self = this;
        var counter = 0;

        $(self.tbody).find('tr').each(function () {

            var id = $(this).data('id');

            /**
             * Modification de l'affichage en fonction de la selection.
             */
            if (self.rows[id]['selected']) {
                $(this).addClass('active');
            }
            else {
                $(this).removeClass('active');
            }


            /**
             * filtre les résultat en fonction du paging
             */
            if((counter < (self.page*self.paging))&&((counter >= ((self.page-1)*self.paging))))
            {
                /**
                 * filtre les résultats en fonction du champ "hidden"
                 */
                if(self.rows[id]['hidden'])
                {
                    $(this).hide();
                }
                else
                {
                    $(this).show();
                    //ne prend en compte que les résultats montrée.
                    counter++;
                }
            }
            else
            {
                //hors de la page
                $(this).hide();
                counter++;
            }









        });

    };

    this.searchInRows = function(string){

        var requests = string.split(" ");

        for (var id in this.rows) {

            var rowHtml = this.rows[id]['html'];

            /**
             * Strip les balise HTML
             * @type {HTMLElement}
             */
            var tmp = document.createElement("DIV");
            tmp.innerHTML = rowHtml;
            var rowText = tmp.textContent || tmp.innerText || "";

            var matchFound = true;

            if(string != '')
            {
                //si chaine la recherche n'est pas vide
                var founds = [];
                requests.forEach(function(request) {
                    // n = -1 si aucun match
                    var n = rowText.search(request);

                    if(n != -1){
                        founds.push(true);
                    }
                    else{
                        founds.push(false);
                    }
                });

                founds.forEach(function(found){
                    matchFound = matchFound && found;
                });


            }
            else
            {
                //si la chaine de recherche est vide. On affiche tout!
               matchFound = true;
            }


            if(matchFound)
            {
                this.rows[id]['hidden'] = false;
            }
            else
            {
                this.rows[id]['hidden'] = true;
            }

        }
    }

    /**
     * On doit utiliser cette fonction car array.lenght ne marche pas dans cette situation
     * @returns {number}
     */
    this.numberOfRows= function(){

        count = 0;
        for(i=0;i<this.rows.length;i++){
            if(this.rows[i] != null)
            {

                count++;
            }
        }
        return count++;
    }



}




