<?php

namespace Interne\MatBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use Interne\MatBundle\Entity\Loan;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @RouteResource("Loan")
 */
class LoanController extends Controller
{

    /**
     * @return mixed
     */
    public function getLoansAction()
    {
        $em = $this->getDoctrine()->getManager();

        $loans = $em->getRepository('MatBundle:Loan');

        $view = $this->view($loans, 200)
            ->setTemplate("MyBundle:Users:getUsers.html.twig")
            ->setTemplateVar('users');

        return $this->handleView($view);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function addLoanAction(Request $request)
    {
        $loan = new Loan();
        $loanForm = $this->createForm(new AddLoanType, $loan);

        $loanForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($loanForm->isValid()) {
            $em->persist($loan);
        } else {

        }

        return new JsonResponse(true);
    }

    /**
     * @param $loan
     * @return JsonResponse
     */
    public function removeLoanAction($loan)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($loan);
        $em->flush();

        return new JsonResponse(true);
    }
}
