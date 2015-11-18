function Loader(nbTaskToDo,message){

    this.message = typeof message !== 'undefined' ? message : null;

    this.nbTaskToDo = nbTaskToDo;
    this.taskCounter = 0;

    this.htmlProgress = '<div class="ui blue progress" id="progressBarLoader"><div class="bar"><div class="progress"></div></div></div>'
    this.htmlModal = '<div class="ui basic modal" id="modalLoader"><div class="header"></div><div class="content">'+this.message+'<br>'+this.htmlProgress+'</div></div>';


    /*
     * permet à la modal du loader de venir se superposer.
     */
    var $allModal = $('.ui.modal');
    $allModal.modal({'allowMultiple':true});
    $allModal.modal({'closable':false});
    //$('.ui.modal').modal('hide');

    //affichie la modal du loader
    $(this.htmlModal).modal('show');

    //initialise la progress bar
    $('#progressBarLoader').progress({
        total: this.nbTaskToDo, value: 0
    });


    /**
     * Ajoute un incérment sur le total de tache à faire
     * dans la progresse bar
     */
    this.increment = function(){
        $('#progressBarLoader').progress('increment');

        this.taskCounter++;

        if(this.taskCounter == this.nbTaskToDo)
        {
            this.close();
        }
    };

    /**
     * Ferme le loader
     */
    this.close = function(){
        var $modalLoader = $('#modalLoader');
        $modalLoader.modal('hide');
        $modalLoader.remove();
        this.taskCounter = 0;
        var $allModal = $('.ui.modal');
        $allModal.modal({'allowMultiple':false});
        $allModal.modal({'closable':true});
    };


}