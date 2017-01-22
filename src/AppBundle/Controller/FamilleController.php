<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Famille;
use AppBundle\Form\Famille\FamilleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FamilleController
 * @package AppBundle\Controller
 * @Route("/intranet/famille")
 */
class FamilleController extends Controller {


    /**
     * @param $famille Famille la famille
     * @return Response la vue
     *
     * @ParamConverter("famille", class="AppBundle:Famille")
     * @Route("/show/{famille}")
     * @Template("AppBundle:Famille:page_show.html.twig")
     */
    public function showAction(Famille $famille) {

        $familleForm = $this->createForm(new FamilleType, $famille);

        return array(
            'listing'       => $this->get('listing'),
            'famille'       => $famille,
            'familleForm'   => $familleForm->createView()
        );
    }

}
