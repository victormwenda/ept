<?php

include_once "PHPExcel.php";

class Application_Service_ExcelProcessor {
    public function readParticipantImport($fileLocation)
    {
        $inputFileType = PHPExcel_IOFactory::identify($fileLocation);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($fileLocation);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $returnArray = array();
        $columnIndexes = array();
        for ($row = 1; $row <= $highestRow; $row++){
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if ($row == 1) {
                $ptIdIndex = array_search("PT ID", $rowData[0]);
                $labNameIndex = array_search("Lab Name", $rowData[0]);
                $countryIndex = array_search("Country", $rowData[0]);
                if ($ptIdIndex === false || $labNameIndex === false || $countryIndex === false) {
                    throw new Exception('Required columns are not present in the first row of the sheet of this document');
                }
                $columnIndexes["PT ID"] = $ptIdIndex;
                $columnIndexes["Lab Name"] = $labNameIndex;
                $columnIndexes["Country"] = $countryIndex;
                $regionIndex = array_search("Region", $rowData[0]);
                if ($regionIndex !== false) {
                    $columnIndexes["Region"] = $regionIndex;
                }
                $usernameIndex = array_search("Username", $rowData[0]);
                if ($usernameIndex !== false) {
                    $columnIndexes["Username"] = $usernameIndex;
                }
                $passwordIndex = array_search("Password", $rowData[0]);
                if ($passwordIndex !== false) {
                    $columnIndexes["Password"] = $passwordIndex;
                }
                $activeIndex = array_search("Active", $rowData[0]);
                if ($activeIndex !== false) {
                    $columnIndexes["Active"] = $activeIndex;
                }
                $phoneNumberIndex = array_search("Phone Number", $rowData[0]);
                if ($phoneNumberIndex !== false) {
                    $columnIndexes["Phone Number"] = $phoneNumberIndex;
                }
            } else if ($row > 2) {
                $participant = array(
                    "PT ID" => $rowData[0][$columnIndexes["PT ID"]],
                    "Lab Name" => $rowData[0][$columnIndexes["Lab Name"]],
                    "Country" => $rowData[0][$columnIndexes["Country"]]
                );
                if (isset($columnIndexes["Region"])) {
                    $participant["Region"] = $rowData[0][$columnIndexes["Region"]];
                }
                if (isset($columnIndexes["Username"])) {
                    $participant["Username"] = $rowData[0][$columnIndexes["Username"]];
                }
                if (isset($columnIndexes["Password"])) {
                    $participant["Password"] = $rowData[0][$columnIndexes["Password"]];
                }
                if (isset($columnIndexes["Active"])) {
                    $participant["Active"] = $rowData[0][$columnIndexes["Active"]];
                }
                if (isset($columnIndexes["Phone Number"])) {
                    $participant["Phone Number"] = $rowData[0][$columnIndexes["Phone Number"]];
                }
                $blankRecord = $this->array_every(array_values($participant), function($value) {
                    return !$value;
                });
                if (!$blankRecord) {
                    $returnArray[] = $participant;
                }
            }
        }
        if (count($returnArray) === 0) {
            throw new Exception('The first sheet of this documents contains no records');
        }
        return $returnArray;
    }

    public function readPtccImport($fileLocation)
    {
        $inputFileType = PHPExcel_IOFactory::identify($fileLocation);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($fileLocation);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $returnArray = array();
        $columnIndexes = array();
        for ($row = 1; $row <= $highestRow; $row++){
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            if ($row == 1) {
                $countryIndex = array_search("Country", $rowData[0]);
                $firstNameIndex = array_search("First Name", $rowData[0]);
                $lastNameIndex = array_search("Last Name", $rowData[0]);
                $emailAddressIndex = array_search("Email Address", $rowData[0]);
                $passwordIndex = array_search("Password", $rowData[0]);
                if ($countryIndex === false || $firstNameIndex === false || $lastNameIndex === false || $emailAddressIndex === false || $passwordIndex === false) {
                    throw new Exception('Required columns are not present in the first row of the sheet of this document');
                }
                $columnIndexes["Country"] = $countryIndex;
                $columnIndexes["First Name"] = $firstNameIndex;
                $columnIndexes["Last Name"] = $lastNameIndex;
                $columnIndexes["Email Address"] = $emailAddressIndex;
                $columnIndexes["Password"] = $passwordIndex;
                $phoneNumberIndex = array_search("Phone Number", $rowData[0]);
                if ($phoneNumberIndex !== false) {
                    $columnIndexes["Phone Number"] = $phoneNumberIndex;
                }
                $activeIndex = array_search("Active", $rowData[0]);
                if ($activeIndex !== false) {
                    $columnIndexes["Active"] = $activeIndex;
                }
            } else if ($row > 2) {
                $ptcc = array(
                    "Country" => $rowData[0][$columnIndexes["Country"]],
                    "First Name" => $rowData[0][$columnIndexes["First Name"]],
                    "Last Name" => $rowData[0][$columnIndexes["Last Name"]],
                    "Email Address" => $rowData[0][$columnIndexes["Email Address"]],
                    "Password" => $rowData[0][$columnIndexes["Password"]]
                );
                if (isset($columnIndexes["Phone Number"])) {
                    $ptcc["Phone Number"] = $rowData[0][$columnIndexes["Phone Number"]];
                }
                if (isset($columnIndexes["Active"])) {
                    $ptcc["Active"] = $rowData[0][$columnIndexes["Active"]];
                }
                $blankRecord = $this->array_every(array_values($ptcc),function($value) {
                    return !$value;
                });
                if (!$blankRecord) {
                    $returnArray[] = $ptcc;
                }
            }
        }
        if (count($returnArray) === 0) {
            throw new Exception('The first sheet of this documents contains no records');
        }
        return $returnArray;
    }

    private function array_every(array $arr, callable $predicate) {
        foreach ($arr as $e) {
            if (!call_user_func($predicate, $e)) {
                return false;
            }
        }
        return true;
    }
}
