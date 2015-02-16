/**
 * s'occupe de récupérer les éventuelles données reçues par le serveur
 * et les rend accessibles
 */

var Server = {

    data: null,
    div: '#server-data',
    attr: 'data-client',


    /**
     * retourne les données transmises par le serveur à l'affichage de la page
     */
    stuff: function () {

        var data = $(this.div).attr(this.attr);
        this.data = JSON.parse(data);

        $(this.div).attr(this.attr, 'empty');

        return JSON.parse(data);
    }
};
