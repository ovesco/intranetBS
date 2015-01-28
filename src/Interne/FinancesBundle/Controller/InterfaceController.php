<?php

namespace Interne\FinancesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Interne\FinancesBundle\Form\CreanceAddType;
use Interne\FinancesBundle\Entity\Creance;
use AppBundle\Entity\Membre;
use Interne\FinancesBundle\Entity\Facture;


/**
 * Class InterfaceController
 * @package Interne\FinancesBundle\Controller
 *
 * @Route("/interface")
 */
class InterfaceController extends Controller
{

    /**
     * @Route("/reload_ajax", name="interne_fiances_interface_reload_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function reloadAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $id = $request->request->get('ownerId');
            $type = $request->request->get('ownerType');

            $owner = null;
            $em = $this->getDoctrine()->getManager();
            if($type == 'Membre')
            {

                $owner = $em->getRepository('AppBundle:Membre')->find($id);
            }
            elseif($type == 'Famille')
            {

                $owner = $em->getRepository('AppBundle:Famille')->find($id);

            }

            return $this->render('InterneFinancesBundle:Interface:interface.html.twig',
                array('ownerEntity' => $owner));
        }

    }

    /*
     * Crée un rendu twig custum en fonction de la page qui
     * demande de formulaire (modal) pour l'ajout de créance.
     *
     */
    /**
     * @param $ownerEntity
     * @return Response
     */
    public function creanceModalFormAction($ownerEntity)
    {
        $creance = new Creance();

        $creanceAddForm  = $this->createForm(new CreanceAddType,$creance);

        /*
         * On récupère les infos du membre ou famille pour construire
         * le formulaire custum de la page qui le demande..
         */
        if($ownerEntity == null)
        {
            //si l'entité est null c'est que c'est pour le formulaire de listing
        }
        else if($ownerEntity->isClass('Membre'))
        {
            $creanceAddForm->get('idOwner')->setData($ownerEntity->getId());
            $creanceAddForm->get('classOwner')->setData('Membre');
        }
        else if($ownerEntity->isClass('Famille'))
        {
            $creanceAddForm->get('idOwner')->setData($ownerEntity->getId());
            $creanceAddForm->get('classOwner')->setData('Famille');
        }

        return $this->render('InterneFinancesBundle:Creance:modalFormCreance.html.twig',
            array('ownerEntity' => $ownerEntity, 'creanceForm' => $creanceAddForm->createView() ));

    }



}
