<?php
require_once '../library/PHPExcel/IOFactory.php';

class Reports_ShipmentController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('generate-forms', 'html')
                    ->initContext();
        $this->_helper->layout()->pageName = 'Generate Forms';
    }

    public function generateFormsAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_hasParam('sid')) {
            $shipmentId = (int) base64_decode($this->_getParam('sid'));
            $shipmentService = new Application_Service_Shipments();
            $templateData = $shipmentService->getDetailsForSubmissionForms($shipmentId);
            $shipmentCode = $templateData['shipment']['shipment_code'];

            $excelDocumentReader = PHPExcel_IOFactory::load('./templates/ept_tb_submission_form.xlsx');
            $excelDocumentWriter = PHPExcel_IOFactory::createWriter($excelDocumentReader, 'Excel2007');
            $numberOfRowsInTemplate = 52;
            $numberOfColumnsInTemplate = 22;
            $participantIndex = 0;
            $sheet = $excelDocumentReader->getActiveSheet();
            $copiedRows = $this->copyRangeValues($sheet,0,$numberOfRowsInTemplate,$numberOfColumnsInTemplate);
            $cellsToMerge = $this->copyMergedCellSpecsInRange($sheet, 0, $numberOfRowsInTemplate);
            foreach ($templateData["participant"] as $participant) {
                if ($participantIndex > 0 && $participantIndex < 3) {
                    /*
                    $this->copyPasteRows(
                        $sheet,
                        0,
                        $numberOfRowsInTemplate * $participantIndex,
                        $numberOfRowsInTemplate,
                        $numberOfColumnsInTemplate,
                        $participant,
                        $templateData
                    );
                    */
                    $this->pasteRows(
                        $sheet,
                        $copiedRows,
                        $cellsToMerge,
                        $participant,
                        $templateData,
                        $numberOfRowsInTemplate * $participantIndex,
                        $numberOfRowsInTemplate,
                        $numberOfColumnsInTemplate,
                        0
                    );
                }
                $participantIndex++;
            }
            if (count($templateData["participant"]) > 0) {
                /*
                $this->copyPasteRows(
                    $sheet,
                    0,
                    0,
                    $numberOfRowsInTemplate,
                    $numberOfColumnsInTemplate,
                    $templateData["participant"][0],
                    $templateData
                );
                */
                $this->pasteRows(
                    $sheet,
                    $copiedRows,
                    $cellsToMerge,
                    $templateData["participant"][0],
                    $templateData,
                    0,
                    $numberOfRowsInTemplate,
                    $numberOfColumnsInTemplate,
                    0
                );
            }

            $response = $this->getResponse();
            $response->setHeader('Content-Disposition', 'attachment; filename="' . preg_replace('/[^A-Za-z0-9.]/', '-', $shipmentCode) . '_submission_forms.xlsx"');
            $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $excelDocumentWriter->save('php://output');
        }
    }

    private function copyPasteRows(PHPExcel_Worksheet $sheet,$srcRow,$dstRow,$height,$width,$participant,$templateData) {
        for ($row = 0; $row < $height; $row++) {
            for ($col = 0; $col < $width; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $srcRow + $row);
                $style = $sheet->getStyleByColumnAndRow($col, $srcRow + $row);
                $dstCell = PHPExcel_Cell::stringFromColumnIndex($col) . (string)($dstRow + $row);
                $cellValue = str_replace(
                    "shipment.shipment_code",
                    $templateData["shipment"]["shipment_code"],
                    $cell->getValue()
                );
                $cellValue = str_replace(
                    "country[participant.country].country_name",
                    $templateData["country"][$participant["country"]]["country_name"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "shipment.lastdate_response",
                    $templateData["shipment"]["lastdate_response"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.participant_name",
                    $participant["participant_name"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.pt_id",
                    $participant["pt_id"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.username",
                    $participant["username"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.password",
                    $participant["password"],
                    $cellValue
                );
                $sampleIndex = 0;
                foreach ($templateData["sample"] as $sample) {
                    $cellValue = str_replace(
                        "sample[" . $sampleIndex . "].sample_label",
                        $sample["sample_label"],
                        $cellValue
                    );
                    $sampleIndex++;
                }
                $cellValue = str_replace(
                    "country[participant.country].pecc_details",
                    $templateData["country"][$participant["country"]]["pecc_details"],
                    $cellValue
                );

                $sheet->setCellValue($dstCell, $cellValue);
                $sheet->duplicateStyle($style, $dstCell);
            }

            $h = $sheet->getRowDimension($srcRow + $row)->getRowHeight();
            $sheet->getRowDimension($dstRow + $row)->setRowHeight($h);
        }

        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $col_s = preg_replace("/[0-9]*/", "", $mc[0]);
            $col_e = preg_replace("/[0-9]*/", "", $mc[1]);
            $row_s = ((int)preg_replace("/[A-Z]*/", "", $mc[0])) - $srcRow;
            $row_e = ((int)preg_replace("/[A-Z]*/", "", $mc[1])) - $srcRow;

            if (0 <= $row_s && $row_s < $height) {
                $merge = $col_s . (string)($dstRow + $row_s) . ":" . $col_e . (string)($dstRow + $row_e);
                $sheet->mergeCells($merge);
            }
        }
    }

    private function copyRangeValues(PHPExcel_Worksheet $sheet,$srcRow,$height,$width) {
        $copiedRows = array();
        for ($row = 0; $row < $height; $row++) {
            $copiedRows[$row] = array(
                "cells" => array(),
                "height" => $sheet->getRowDimension($srcRow + $row)->getRowHeight()
            );
            for ($col = 0; $col < $width; $col++) {
                $copiedRows[$row]["cells"][$col] = array(
                    "cell" => $sheet->getCellByColumnAndRow($col, $srcRow + $row)
                );
                $copiedRows[$row]["cells"][$col]["value"] = $copiedRows[$row]["cells"][$col]["cell"]->getValue();
            }
        }
        return $copiedRows;
    }

    private function copyMergedCellSpecsInRange(PHPExcel_Worksheet $sheet,$srcRow,$height) {
        $mergedCells = array();
        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $col_s = preg_replace("/[0-9]*/", "", $mc[0]);
            $col_e = preg_replace("/[0-9]*/", "", $mc[1]);
            $row_s = ((int)preg_replace("/[A-Z]*/", "", $mc[0])) - $srcRow;
            $row_e = ((int)preg_replace("/[A-Z]*/", "", $mc[1])) - $srcRow;

            if (0 <= $row_s && $row_s < $height) {
                array_push(
                    $mergedCells,
                    array(
                        "fromCol" => $col_s,
                        "fromRow" => $row_s,
                        "toCol" => $col_e,
                        "toRow" => $row_e
                    )
                );
            }
        }
        return $mergedCells;
    }

    private function pasteRows(
        PHPExcel_Worksheet $sheet,
        $copiedCells,
        $mergedCells,
        $participant,
        $templateData,
        $dstRow,
        $height,
        $width,
        $srcRow
    ) {
        for ($row = 0; $row < $height; $row++) {
            for ($col = 0; $col < $width; $col++) {
                $dstCell = PHPExcel_Cell::stringFromColumnIndex($col) . (string)($dstRow + $row);
                $cellValue = str_replace(
                    "shipment.shipment_code",
                    $templateData["shipment"]["shipment_code"],
                    $copiedCells[$row]["cells"][$col]["value"]
                );
                $cellValue = str_replace(
                    "country[participant.country].country_name",
                    $templateData["country"][$participant["country"]]["country_name"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "shipment.lastdate_response",
                    $templateData["shipment"]["lastdate_response"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.participant_name",
                    $participant["participant_name"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.pt_id",
                    $participant["pt_id"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.username",
                    $participant["username"],
                    $cellValue
                );
                $cellValue = str_replace(
                    "participant.password",
                    $participant["password"],
                    $cellValue
                );
                $sampleIndex = 0;
                foreach ($templateData["sample"] as $sample) {
                    $cellValue = str_replace(
                        "sample[" . $sampleIndex . "].sample_label",
                        $sample["sample_label"],
                        $cellValue
                    );
                    $sampleIndex++;
                }
                $cellValue = str_replace(
                    "country[participant.country].pecc_details",
                    $templateData["country"][$participant["country"]]["pecc_details"],
                    $cellValue
                );

                $sheet->setCellValue($dstCell, $cellValue);
                $style = $sheet->getStyleByColumnAndRow($col, $srcRow + $row);
                $sheet->duplicateStyle($style, $dstCell);
            }
            $sheet->getRowDimension($dstRow + $row)->setRowHeight($copiedCells[$row]["height"]);
        }

        foreach ($mergedCells as $mergeCell) {
            $merge = $mergeCell["fromCol"] . (string)($dstRow + $mergeCell["fromRow"]) . ":" . $mergeCell["toCol"] . (string)($dstRow + $mergeCell["toRow"]);
            $sheet->mergeCells($merge);
        }
    }
}





