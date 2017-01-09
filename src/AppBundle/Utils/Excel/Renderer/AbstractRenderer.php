<?php

namespace AppBundle\Utils\Excel\Renderer;

use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractRenderer {

    protected $em;

    protected $ids  = [];

    protected $data = [];

    /**
     * Doit retourner un PHPExcel prêt
     * @return \PHPExcel
     */
    abstract public function render();

    public function setIds(array $ids) {

        foreach($ids as $id)
            $this->data[] = $this->em->getRepository($this->getNamespace())->find($id);

        $this->ids = $ids;

        return $this;
    }

    public function __construct(ObjectManager $manager)
    {
        $this->em   = $manager;
    }

    protected static function tryIt($fn) {

        try {
            return $fn();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Génère un PHPExcel vierge et prêt
     * @return \PHPExcel
     */
    protected function workbook() {

        $workBook   = new \PHPExcel();

        $workBook->getProperties()
            ->setCreator('netBS')
            ->setDescription('Généré dynamiquement par le netBS le ' . date('d.m.Y'));

        $workBook->setActiveSheetIndex(0);

        return $workBook;
    }

    abstract protected function getNamespace();
}