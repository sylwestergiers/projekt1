<?php 

//tablica przesłana Ajaxem
$paramsArr = !empty($_GET['param1']) ? $_GET['param1'] : [];

// tablica objektów klasy Cell
$cellsArray = [];

// id kmórki startowej
$startCellId = '';

// id kmórki końcowej
$lastCellId = '';

// lista zamknięta
// id danej komórki, id rodzica (poprzednia komórka)
$closeList = [];

// lista otwarta
// id danej komórki, przebyta droga (G), heurystyka (droga do końca (H)), waga kroku (F = G + H), id rodzica (poprzednia komórka)
$openList = [];

// wypełnij tablicę objektami, deklaracja pierwszej i ostatniej komórki
for($i = 1; $i < 21; $i++) {
    for($j = 1; $j < 21; $j++)  {
        $id = [
            'i' => $i,
            'j' => $j
        ];
        $status = $paramsArr[$i][$j];
        // przebyta droga G
        $g = 0;
        // id rodzica
        $parentID = 0;
        // dla komórki startowej
        if($status == 1){
            $startCellId = $id;
        }
        // dla komórki końcowej
        if($status == 2){
            $lastCellId = $id;
        }
        $cellsArray[$i][$j] = aStar::createObjCell($id, $status, $g, $parentID);
    }
}


// KROKI DLA KOMÓRKI STARTOWEJ

// wyciągam objekt komórki startowej
$bestStep = $cellsArray[$startCellId['i']][$startCellId['j']]; 

// dodaję objekt komórki startowej do listy zamkniętej
$closeList = aStar::addToCloseList($bestStep, $closeList);

// kolejne kroki wykonywane są w pętli do momentu znaleziania na liscie otwartej elementu końcowego

$limit = true;
while($limit){
    // sprawdzam możliwości i dodaje je do listy otwartej
    $openList = aStar::checkPossibilities($bestStep, $openList, $closeList, $cellsArray, $lastCellId);
    // sprawdzam czy lista otwarta zawiera punkt koncowy
    $isOpenList = aStar::checkOpenList($lastCellId['i'], $lastCellId['j'], $openList);
    // szukam najlepszej możliwości na liście otwartej
    $bestStep = aStar::findBestStep($openList, $cellsArray, $closeList, $lastCellId);
    // dodaje najlepszą opcje na listę zamkniętą
    $closeList = aStar::addToCloseList($bestStep, $closeList);
    // usuwam najlepszą opcje z listy otwartej
    $openList = aStar::removeFromOpenList($bestStep, $openList);
    if($isOpenList || empty($openList)) {
        $limit = false;
    }
}


// pojedyńcza komórka tablicy 
class Cell {
    // id komórki
    // i,j
    public $cellID = [];
    // status komórki
    // emptyCell = 0; firstCell = 1; lastCell = 2; obstacleCell = 3;
    public $cellStatus;
    // przebyta droga;
    public $g;
    // id komórki rodzica (komórki, z której przybyliśmy)
    // i,j
    public $parentCellID = [];
}

// metody statyczne A*
class aStar {
    // stwórz nowy obj klasy Cell
    public static function createObjCell($id, $status, $g, $parentID) {
        $cellObj = new Cell;
        $cellObj->cellID = $id;
        $cellObj->cellStatus = $status;
        $cellObj->g = $g;
        $cellObj->parentCellID = $parentID;
        return $cellObj;
    }
    
    // oblicz heurystykę (metoda Manhatan)
    public static function getParamH($currentCellID, $lastCellId) {
        $h = 0;
        $lastI = $currentCellID['i'];
        $lastJ = $currentCellID['j'];
        $currentI = $lastCellId['i'];
        $currentJ = $lastCellId['j'];
        
        $h = abs($lastI - $currentI) + abs($lastJ - $currentJ);
        return $h;
    }
    
    // sprawdź możliwe kroki
    public static function checkPossibilities($obj, $openList, $closeList, $cellsArray, $lastCellId){
        $currentI = $obj->cellID['i'];
        $currentJ = $obj->cellID['j'];
        $currentG = $obj->g;
        $parentID = $obj->cellID;
        
        // sprawdzam 4 możliwe kroki
        // górny sąsiad
        $topI = $currentI -1;
        $topJ = $currentJ;
        // czy komórka mieści się w tablicy
        if((0 < $topI && $topI < 21) && (0 < $topJ && $topJ < 21)) {
            $isClose = self::checkCloseList($topI, $topJ, $closeList);
            $isOpen = self::checkOpenList($topI, $topJ, $openList);
            // elementu nie ma na liście zamkniętej i otwartej
            if(!$isClose && !$isOpen) {
                $topCellObj = $cellsArray[$topI][$topJ];
                // element jest pusty
                if($topCellObj->cellStatus == 0 || $topCellObj->cellStatus == 2){
                    $cellsArray[$topI][$topJ]->parentCellID = $parentID;
                    $cellsArray[$topI][$topJ]->g = $currentG + 1;
                    // dodaje komórkę na listę otwartą
                    $openList = self::addToOpenList($obj, $topCellObj, $openList, $lastCellId);
                }
            }
        }

        // prawy sąsiad
        $rightI = $currentI;
        $rightJ = $currentJ + 1;
        // czy taki element mieści się w tablicy
        if((0 < $rightI && $rightI < 21) && (0 < $rightJ && $rightJ < 21)) { 
            $isClose = self::checkCloseList($rightI, $rightJ, $closeList);
            $isOpen = self::checkOpenList($rightI, $rightJ, $openList);
            // elementu nie ma na liście zamkniętej i otwartej
            if(!$isClose && !$isOpen) {
                $rightCellObj = $cellsArray[$rightI][$rightJ];
                // element jest pusty, lub jest to element końcowy
                if($rightCellObj->cellStatus == 0 || $rightCellObj->cellStatus == 2){
                    $cellsArray[$rightI][$rightJ]->parentCellID = $parentID;
                    $cellsArray[$rightI][$rightJ]->g = $currentG + 1;
                    // dodaje komórkę na listę otwartą
                    $openList = self::addToOpenList($obj, $rightCellObj, $openList, $lastCellId);
                }
            }
        }
        
        // dolny sąsiad
        $bottomI = $currentI + 1;
        $bottomJ = $currentJ;
        // czy taki element mieści się w tablicy
        if((0 < $bottomI && $bottomI < 21) && (0 < $bottomJ && $bottomJ < 21)) { 
            $isClose = self::checkCloseList($bottomI, $bottomJ, $closeList);
            $isOpen = self::checkOpenList($bottomI, $bottomJ, $openList);
            // elementu nie ma na liście zamkniętej i otwartej
            if(!$isClose && !$isOpen) {
                $bottomCellObj = $cellsArray[$bottomI][$bottomJ];
                // element jest pusty, lub jest to element końcowy
                if($bottomCellObj->cellStatus == 0 || $bottomCellObj->cellStatus == 2){
                    $cellsArray[$bottomI][$bottomJ]->parentCellID = $parentID;
                    $cellsArray[$bottomI][$bottomJ]->g = $currentG + 1;
                    // dodaje komórkę na listę otwartą
                    $openList = self::addToOpenList($obj, $bottomCellObj, $openList, $lastCellId);
                }
            }
        }

        // lewy sąsiad
        $leftI = $currentI;
        $leftJ = $currentJ - 1;
        // czy taki element mieści się w tablicy
        if((0 < $leftI && $leftI < 21) && (0 < $leftJ && $leftJ < 21)) { 
            $isClose = self::checkCloseList($leftI, $leftJ, $closeList);
            $isOpen = self::checkOpenList($leftI, $leftJ, $openList);
            // elementu nie ma na liście zamkniętej i otwartej
            if(!$isClose && !$isOpen) {
                $leftCellObj = $cellsArray[$leftI][$leftJ];
                // element jest pusty, lub jest to element końcowy
                if($leftCellObj->cellStatus == 0 || $leftCellObj->cellStatus == 2){
                    $cellsArray[$leftI][$leftJ]->parentCellID = $parentID;
                    $cellsArray[$leftI][$leftJ]->g = $currentG + 1;
                    // dodaje komórkę na listę otwartą
                    $openList = self::addToOpenList($obj, $leftCellObj, $openList, $lastCellId);
                }
            }
        }
        
        return $openList;
    }
    
    // sprawdź czy element jest na liście zamkniętej
    public static function checkCloseList($i, $j, $closeList){
        $listIndex = $i.'-'.$j;
        if(isset($closeList[$listIndex])) {
            return true;
        }
        else {
            return false;
        }
    }
    
    // sprawdź czy element jest na liście otwartej
    public static function checkOpenList($i, $j, $openList){
        $listIndex = $i.'-'.$j;
        if(isset($openList[$listIndex])) {
            return true;
        }
        else {
            return false;
        }
    }
    
    // dodaj element na listę otwartą
    public static function addToOpenList($parentObj, $currentObj, $openList, $lastCellId){
        $listIndex = $currentObj->cellID['i'].'-'.$currentObj->cellID['j'];
        $h = self::getParamH($currentObj->cellID, $lastCellId);
        $g = $currentObj->g;
        
        $openList[$listIndex] = [
            'cellID' => $currentObj->cellID,
            'G' => $g,
            'H' => $h,
            'F' => $h + $g,
            'parentId' => $parentObj->cellID
        ];
        return $openList;
    }
    
    // usuń element z listy otwartej
    public static function removeFromOpenList($bestStep, $openList){
        $listIndex = $bestStep->cellID['i'].'-'.$bestStep->cellID['j'];
        unset($openList[$listIndex]);
        return $openList;
    }
    
    // dodaj element na listę zamkniętą
    public static function addToCloseList($currentObj, $closeList){
        $listIndex = $currentObj->cellID['i'].'-'.$currentObj->cellID['j'];
        $closeList[$listIndex] = [
            'cellID' => $currentObj->cellID,
            'parentId' => $currentObj->parentCellID
        ];
        return $closeList;
    }
    
    // znajdź na liscie otwartej najlepszy, możliwy krok i dodaj go na listę zamkniętą
    public static function findBestStep($openList, $cellsArray, $closeList, $lastCellId){
        $listIndex = $currentObj->cellID['i'].'-'.$currentObj->cellID['j'];
        $minF = 1000;
        // objekt z najniższym współczynnikiem F
        $bestStep;
        // sprawdź wartość f każdego elementu na liście otwartej
        foreach($openList as $step){
            if($step['F'] <= $minF){
                $minF = $step['F'];
                $bestStep = $cellsArray[$step['cellID']['i']][$step['cellID']['j']]; 
                $bestStep->parentCellID = $step['parentId'];
            }
        }
        return $bestStep;
    }
}

//die(json_encode($closeList)); 
//die(print_r($closeList[]));
// dodaje ostatnią komórkę do tablicy odpowiedzi
$closeIndex = $lastCellId['i'].'-'.$lastCellId['j'];  
$resp[] = $closeIndex;

// sprawdzając parentId doaje kolejne komórki
$respLimit = true;
while($respLimit){
    // dla pierwsze komórki
    if($closeList[$closeIndex]['parentId'] == 0) {
        $respLimit = false;
    }
    else {
        $closeIndex = $closeList[$closeIndex]['parentId']['i'].'-'.$closeList[$closeIndex]['parentId']['j'];
        $resp[] = $closeIndex;
    }
} 


die(json_encode($resp)); 

?>