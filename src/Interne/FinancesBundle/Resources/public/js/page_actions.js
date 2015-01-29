
/**
 *
 * @param id
 */
function deleteFactureFromPage(id)
{
    if(deleteFacture(id))
    {
        //suppresion réussie
        reloadPage();
        alerte.send('Facture supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }
}

/**
 *
 * @param idArray
 */
function deleteListeFactureFromPage(idArray)
{

    var success = true;
    idArray.forEach(function(id){

        success = success && deleteFacture(id);

    });
    if(success)
    {
        //suppresion réussie
        reloadPage();
        alerte.send('Facture supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }

}

/**
 *
 * @param id
 */
function deleteCreanceFromPage(id){

    if(deleteCreance(id))
    {
        //suppresion réussie
        reloadPage();
        alerte.send('Créance supprimée','info',2000);
    }
    else
    {
        //erreur
        alerte.send('Erreur lors de la suppresion','danger');
    }

}

/**
 *
 * @param listeCreances
 */
function createFactureFromPage(listeCreances) {

    if(createFactureWithListeCreances(listeCreances))
    {
        reloadPage();
        alerte.send('Facture crée','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}

/**
 *
 * @param idFacture
 * @param idForm
 */
function addRappelFromPage(idFacture,idForm)
{
    if(addRappel(idFacture,idForm))
    {
        reloadPage();
        alerte.send('Rappel ajouté','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }
}

/**
 *
 * @param idForm
 */
function addRappelToListeFromPage(idForm)
{
    /**
     * On récupère le tableau d'id enregistré
     */
    var idArray = getTemporaryStorage();
    if(addRappelToListeOfFacture(idArray,idForm))
    {
        reloadPage();
        alerte.send('Rappels Ajoutés','info',2000);
    }
    else
    {
        alerte.send('Erreur','danger');
    }


}

/**
 *
 * @param idForm
 */
function addCreanceFromPage(idForm)
{
    if(addCreance(idForm))
    {
        reloadPage();
        alerte.send('Creance ajoutée','info',2000);
    }
    else
    {
        alerte.send('Erreur lors de la création de la créance','danger');
    }
}