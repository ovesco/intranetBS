<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use PHPExcel_IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * @package AppBundle\Controller
 * @Route("/intranet/export/excel")
 */
class ExcelController extends Controller
{
    /**
     * @Route("/list/{type}/{id}", name="excel.export.list")
     * @return Response
     */
    public function exportList($type, $id)
    {
        $renderer = null;

        switch($type) {
            case 'groupe':
                $renderer   = $this->get('excel.renderer.groupe')->setIds(array($id))->setRecursive(true);
                break;
            case 'famille':
                $renderer   = $this->get('excel.renderer.famille');
                break;
            case 'dynamic':
                $renderer   = $this->get('excel.renderer.dynamic');
                break;
            default:
                throw new HttpException(401, "No excel renderer available for type $type");
        }

        $response   = new StreamedResponse(function() use ($renderer) {

            $objWriter = PHPExcel_IOFactory::createWriter($renderer->render(), 'Excel5');
            $objWriter->save('php://output');
        });

        $response->headers->add(array(
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition'   => 'attachment; filename="netbs_' . date('H.i.s') . '.xls"'
        ));

        return $response;
    }

    /**
     * Réalise un export complet du mieux possible de la liste de données fournie
     * @param Request $request
     */
    public function exportComplete(Request $request) {


    }
}
