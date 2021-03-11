<?php
include_once("./common.php");

class TourRepository
{
    private $dbHost;
    private $user;
    private $pwd;
    private $dbName;

    public function __construct($dbHost, $user, $pwd, $dbName)
    {
        $this->dbHost = $dbHost;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->dbName = $dbName;
    }

    public function getTourSteps($tourName)
    {
        $pdo = new PDO("mysql:host={$this->dbHost};dbname={$this->dbName};charset=utf8", $this->user, $this->pwd, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]);
        $stmt = $pdo->prepare("SELECT * FROM ToursSteps WHERE tourName = ?");
        $stmt->execute([$tourName]);
        return array_map([$this, "convertRawStep"], $stmt->fetchAll());
    }

    private function convertToBool($value)
    {
        return isset($value) && $value > 0;
    }

    private function convertRawStep($step)
    {
        $step->isFirstStep = $this->convertToBool($step->isFirstStep);
        $step->isLastStep = !$step->isFirstStep && $step->nextStepId == null;
        $step->withCancelBtn = $this->convertToBool($step->withCancelBtn);
        $step->withPreviousStepBtn = $this->convertToBool($step->withPreviousStepBtn);

        if ($step->urlToOpenOnNext != null) {
            $step->urlToOpenOnNext = buildPlatformUrl($step->urlToOpenOnNext, null, $step->urlOpenMode);
        }
        return $step;
    }
}
