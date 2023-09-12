<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */
   
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
