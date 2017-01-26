<?php

class Application_Service_EvaluationScoring {
    const CONCERN_CT_MAX_VALUE = 42.00;
    const PASS_SCORE_PERCENT = 100.00;
    const CONCERN_SCORE_PERCENT = 50.00;
    const FAIL_SCORE_PERCENT = 0.00;

    public function calculateTbSamplePassStatus($refMtbDetected, $resMtbDetected, $refRifResistance, $resRifResistance,
                                                $probeD, $probeC, $probeE, $probeB, $spc, $probeA) {
        $calculatedScore = "fail";
        if ($resMtbDetected == $refMtbDetected &&
            $resRifResistance == $refRifResistance) {
            $calculatedScore = "pass";
            $ctValues = array(
                floatval($probeD),
                floatval($probeC),
                floatval($probeE),
                floatval($probeB),
                floatval($spc),
                floatval($probeA)
            );
            if(max($ctValues) > self::CONCERN_CT_MAX_VALUE) {
                $calculatedScore = "concern";
            }
        }
        return $calculatedScore;
    }

    public function calculateTbSampleScore($passStatus, $sampleScore) {
        switch ($passStatus) {
            case "pass":
                return self::PASS_SCORE_PERCENT * ($sampleScore / 100.00);
            case "concern":
                return self::CONCERN_SCORE_PERCENT * ($sampleScore / 100.00);
            case "fail":
                return self::FAIL_SCORE_PERCENT * ($sampleScore / 100.00);
            default:
                return self::FAIL_SCORE_PERCENT * ($sampleScore / 100.00);
        }
    }

    const REHYDRATION_EXPIRY_HOURS = 48; // 2 days
    const FRIED_SAMPLE_HOURS = 336; // 14 Days
    const EXPIRY_FROM_DATE_OF_SHIPMENT_HOURS = 720; // 30 Days
    const MAX_DOCUMENTATION_SCORE = 20;
    const DEDUCTION_POINTS = 2;

    public function calculateTbDocumentationScore($shipmentDate, $expiryDate, $receiptDate, $rehydrationDate, $testDate,
                                                  $supervisorApproval, $supervisorName, $responseDeadlineDate) {
        $documentationScore = self::MAX_DOCUMENTATION_SCORE;
        $inferredTestDate = $responseDeadlineDate;
        if ($this->isBlankDate($testDate)) {
            $documentationScore -= self::DEDUCTION_POINTS;
        } else {
            $inferredTestDate = $testDate;
        }
        if ($this->isBlankDate($expiryDate)) {
            $documentationScore -= self::DEDUCTION_POINTS;
        } else if (new DateTime($expiryDate) < new DateTime($inferredTestDate)) {
            // Mark as zero if user tried to run the sample using an expired panel
            return 0;
        }
        if ($this->isBlankDate($receiptDate)) {
            $documentationScore -= self::DEDUCTION_POINTS;
        }
        if ($this->isBlankDate($rehydrationDate)) {
            $documentationScore -= self::DEDUCTION_POINTS;
        }
        if ($this->isNullOrEmpty($supervisorApproval) || $supervisorApproval == 'no') {
            $documentationScore -= self::DEDUCTION_POINTS;
        }
        if ($this->isNullOrEmpty($supervisorName)) {
            $documentationScore -= self::DEDUCTION_POINTS;
        }
        if ($this->dateDiffInHours($inferredTestDate, $rehydrationDate) > self::REHYDRATION_EXPIRY_HOURS) {
            $documentationScore -= DEDUCTION_POINTS;
        }
        if ($this->dateDiffInHours($inferredTestDate, $receiptDate) > self::FRIED_SAMPLE_HOURS) {
            $documentationScore -= DEDUCTION_POINTS;
        }
        if ($this->dateDiffInHours($inferredTestDate, $shipmentDate) > self::EXPIRY_FROM_DATE_OF_SHIPMENT_HOURS) {
            $documentationScore -= DEDUCTION_POINTS;
        }
        return $documentationScore;
    }

    private function isBlankDate ($dateValue) {
        return $this->isNullOrEmpty($dateValue) || $dateValue == '0000-00-00';
    }

    private function isNullOrEmpty ($stringValue) {
        return !isset($stringValue) || $stringValue == '';
    }

    private function dateDiffInHours ($laterDate, $earlierDate) {
        $datLaterDate = is_string($laterDate) ? new DateTime($laterDate) : $laterDate;
        $datEarlierDate = is_string($earlierDate) ? new DateTime($earlierDate) : $earlierDate;
        $dateDiff = $datLaterDate->diff($datEarlierDate);
        $hoursBetweenDates = $dateDiff->h;
        return $hoursBetweenDates + ($dateDiff->days * 24);
    }

    const FAIL_IF_POINTS_DEDUCTED = 20;

    public function calculateSubmissionPassStatus($shipmentScore, $documentationScore, $maxShipmentScore, $samplePassStatuses) {
        if ((self::MAX_DOCUMENTATION_SCORE) + $maxShipmentScore - $shipmentScore - $documentationScore > self::FAIL_IF_POINTS_DEDUCTED) {
            return 'fail';
        }
        if (in_array('fail', $samplePassStatuses)) {
            return 'fail';
        }
        if (in_array('concern', $samplePassStatuses)) {
            return 'concern';
        }
        return 'pass';
    }
}
