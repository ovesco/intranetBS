<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 18.01.17
 * Time: 17:42
 */

namespace AppBundle\Utils\ListUtils;


class ListToExcel {

    private $tempDocDir;

    public function __construct($cacheTemporaryDocumentDir)
    {
        $this->tempDocDir = $cacheTemporaryDocumentDir;
    }

    public function generateExcel(ListRenderer $listRenderer)
    {
        $workBook   = new \PHPExcel();

        /*
        $workBook->getProperties()
            ->setCreator('netBS')
            ->setDescription('Généré dynamiquement par le netBS le ' . date('d.m.Y'));
        */

        $workBook->setActiveSheetIndex(0);

        $excel_row = 1;
        $excel_column = 0;
        /** @var Column $column */
        foreach($listRenderer->getColumns() as $column)
        {
            $workBook->getActiveSheet()->setCellValueByColumnAndRow($excel_column,$excel_row,$column->getName());
            $excel_column++;
        }
        $excel_row++;
        $excel_column = 0;

        foreach($listRenderer->getItems() as $item)
        {
            /** @var Column $column */
            foreach($listRenderer->getColumns() as $column)
            {
                $workBook->getActiveSheet()->setCellValueByColumnAndRow($excel_column,$excel_row,$column->render($item));
                $excel_column++;
            }
            $excel_column = 0;
            $excel_row++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($workBook, 'Excel2007');

        $temporaryFile = $this->tempDocDir.'/'.sha1(time()).'.xlsx';

        /*
         * Enregistrement en dure du fichier (obligatoire)
         */
        $objWriter->save($temporaryFile);

        return $temporaryFile;
    }

}