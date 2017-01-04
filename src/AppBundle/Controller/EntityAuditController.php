<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\EntityAudit\AuditAnalizer;


/**
 * Class EntityAuditController
 * @package AppBundle\Controller
 * @Route("/intranet/audit")
 */
class EntityAuditController extends Controller
{
    /**
     * @Route("/membre")
     * @param $membreId integer
     * @return Response
     */
    public function membreAction($membreId)
    {
        /** @var AuditAnalizer $analizer */
        $analizer = $this->get('app.audit.analizer');

        $versions = $analizer->findVersionsMembre($membreId);

        return $this->render("AppBundle:EntityAudit:versions_table.html.twig", array('versions'=>$versions));
    }

    /**
     * @Route("/famille")
     * @param $familleId integer
     * @return Response
     */
    public function familleAction($familleId)
    {
        /** @var AuditAnalizer $analizer */
        $analizer = $this->get('app.audit.analizer');

        $versions = $analizer->findVersionsFamille($familleId);

        return $this->render("AppBundle:EntityAudit:versions_table.html.twig", array('versions'=>$versions));
    }
}
