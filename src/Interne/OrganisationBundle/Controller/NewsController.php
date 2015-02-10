<?php

namespace Interne\OrganisationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Interne\OrganisationBundle\Entity\News;
use Interne\OrganisationBundle\Form\NewsType;
use AppBundle\Entity\Membre;

/**
 * Class NewsController
 * @package Interne\OrganisationBundle\Controller
 * @route("/news")
 */
class NewsController extends Controller
{
    /**
     * @return Response
     * @route("", name="interne_organisation_news_show")
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listeNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging(0,15);

        return $this->render('InterneOrganisationBundle:News:page_news.html.twig',
            array('listeNews' =>$listeNews));
    }

    /**
     * @return Response
     * @route("/get_form", name="interne_organisation_news_get_form", options={"expose"=true})
     * @param Request $request
     */
    public function getFormAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $news = new News();
            $newsForm = $this->createForm(new NewsType(),$news, array('action' => $this->generateUrl('interne_organisation_news_add')));
            return $this->render('InterneOrganisationBundle:News:news_modale_form.html.twig',array('form'=>$newsForm->createView()));
        }
        return new Response();
    }



    /**
     * @Route("/add", name="interne_organisation_news_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addNewsAction(Request $request)
    {
        $news = new News();
        $newsForm = $this->createForm(new NewsType(),$news);

        $newsForm->handleRequest($request);

        if($newsForm->isValid())
        {

            $news->setDate(new \DateTime());
            /** @var Membre $membre */
            $membre = $this->getUser()->getMembre();
            $news->setAuthor($membre);

            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('interne_organisation_news_show'));

    }

    /**
     * @Route("/load_more", name="interne_organisation_news_load_more", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function loadMoreNewsAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $startPoint = $request->request->get('numberOfNews');

            $em = $this->getDoctrine()->getManager();
            $listeNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging($startPoint,15);

            return $this->render('InterneOrganisationBundle:News:listeNews.html.twig',
                array('listeNews' =>$listeNews));
        }
        return new Response();
    }
}
