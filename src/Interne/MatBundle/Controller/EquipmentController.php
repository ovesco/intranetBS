<?php

namespace Interne\MatBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use Interne\MatBundle\Entity\Equipment;
use Interne\MatBundle\Form\AddEquipmentType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @RouteResource("Equipment")
 */
class EquipmentController extends FOSRestController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cgetAction()
    {
        $em = $this->getDoctrine()->getManager();

        $equipments = $em->getRepository('MatBundle:Equipment');

        $view = $this->view($equipments, 200)
            ->setTemplate("MyBundle:Users:getUsers.html.twig")
            ->setTemplateVar('users');

        return $this->handleView($view);
    } // "get_equipments"     [GET] /equipments

    /**
     * @param $request
     * @return JsonResponse
     */
    public function addEquipmentAction(Request $request)
    {
        $equipment = new Equipment();
        $equipmentForm = $this->createForm(new AddEquipmentType(), $equipment);

        $equipmentForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($equipmentForm->isValid()) {
            $em->persist($equipment);
        } else {

        }

        return new JsonResponse(true);
    }

    /**
     * @param $equipment
     * @return JsonResponse
     */
    public function removeEquipmentAction($equipment)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($equipment);
        $em->flush();

        return new JsonResponse(true);
    }

}
