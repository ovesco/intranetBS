<?php

namespace AppBundle\Utils\Export\Excel;

use AppBundle\Twig\AppExtension;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListRenderer;

class ExcelGenerator {

    private $twig;

    private $environment;

    public function __construct(AppExtension $twig, \Twig_Environment $environment)
    {
        $this->twig         = $twig;
        $this->environment  = $environment;
    }

    public function getWorkBook() {

        $workBook   = new \PHPExcel();

        $workBook->getProperties()
            ->setCreator('netBS')
            ->setDescription('Généré dynamiquement par le netBS le ' . date('d.m.Y'));

        return $workBook;
    }


    public function exportDetails($items) {

    }

    public function exportList(ListRenderer $list) {

        $name   = $list->getName();
        $wb     = $this->getWorkBook();
        $name   = $name == '' || $name == null ? 'liste sans nom' : $name;
        $row    = 1;

        $wb->getProperties()->setTitle($name);
        $wb->setActiveSheetIndex(0);

        foreach($list->getItems() as $item) {

            $col = 'A';

            /** @var Column $column */
            foreach($list->getColumns() as $column) {

                if($column->getTwigFilters() === null)
                    $val = $column->render($item);
                else
                    $val = $this->twig->apply_filters($this->environment, $item, $column->getTwigFilters());

                $wb->getActiveSheet()
                    ->setCellValue($col . $row, $val);

                $col++;
            }

            $row++;
        }
    }
}