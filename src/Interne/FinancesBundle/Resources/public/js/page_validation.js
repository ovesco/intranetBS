$(document).ready(function(){


    /**
     * Cette fonction permet la gestion du formulaire de répartion
     * des sommes lors de la validation des payements.
     *
     * Elle calcule le montant restant à répartire.
     * Elle verifie que la somme entrée est correct avant de pouvoir
     * soumettre le formulaire.
     */
    $('.repartitionForm').change(function(){

        var montantRecu = parseFloat($(this).find('.montantRecu').val());
        var montantRepartit = 0.0;

        $(this).find('.repartitionMontant').each(function(){
            var montant = $(this).val();

            if(!isNaN(parseFloat(montant))){
                montant = parseFloat(montant);
            }
            else{
                if(!isNaN(parseInt(montant))){
                    montant = parseInt(montant);
                }
            }
            montantRepartit = montantRepartit+ montant;
        });

        var montantRestant= parseFloat(montantRecu-montantRepartit);

        if(montantRestant == 0)
        {
            $(this).find('.submitRepartionForm').show();
        }
        else
        {
            var $montantRestant = $(this).find('.montantRestant');
            $montantRestant.text(montantRestant.toFixed(2));
            if(montantRestant>0){
                $montantRestant.addClass('positive');
                $montantRestant.removeClass('negative');
            }
            else{
                $montantRestant.addClass('negative');
                $montantRestant.removeClass('positive');
            }
        }

    });

    $('.submitRepartionForm').click(function(){

    });

});

