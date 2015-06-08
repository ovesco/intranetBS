/**
 *
 * @param id
 */
function showPayement(id)
{
    var url = Routing.generate('interne_fiances_payement_show',{'payement':id});

    getModal(null,url);
}