<?php
namespace Juhelib;
use \PhpOffice\PhpSpreadsheet\IOFactory;
class excel
{
    /**
     * 读取 excel 文件
     * @param $filePath
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function getExcel($filePath)
    {
		$excelReader = IOFactory::createReader(IOFactory::identify($filePath));
        $objPHPExcel = $excelReader->load($filePath);
        $sheetSelected = 0;
        $objPHPExcel->setActiveSheetIndex($sheetSelected);
        $rowCount = $objPHPExcel->getActiveSheet()->getHighestRow();
        $columnCount = $objPHPExcel->getActiveSheet()->getHighestColumn();
        $dataArr = array();
        for ($row = 1; $row <= $rowCount; $row++) {
            for ($column = 'A'; $column <= $columnCount; $column++) {
                $dataArr[$row][] = $objPHPExcel->getActiveSheet()->getCell($column.$row)->getValue();
            }
        }
        return $dataArr;
    }
}
?>