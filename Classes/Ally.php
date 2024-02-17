<?php
// Ally Class to handle interactions involving allies

class Ally {

  // Properties
  private $allies = [];
  private $playerID;
  private $index;

  // Constructor
  function __construct($MZIndex) {
    global $currentPlayer;
    $mzArr = explode("-", $MZIndex);
    $player = ($mzArr[0] == "MYALLY" ? $currentPlayer : ($currentPlayer == 1 ? 2 : 1));
    $this->index = $mzArr[1];
    $this->allies = &GetAllies($player);
    $this->playerID = $player;
  }

  // Methods
  function CardID() {
    return $this->allies[$this->index];
  }

  function UniqueID() {
    return $this->allies[$this->index+5];
  }

  function SetDistant() {
    $this->allies[$this->index+9] = 1;
  }

  function IsDistant() {
    return $this->allies[$this->index+9] == 1 ? true : false;
  }

  function OnFoster() {
    $fosterActive = $this->allies[$this->index+10] == 0;
    $this->allies[$this->index+10] = 2;
    return $fosterActive;
  }

  function AddBuffCounter() {
    ++$this->allies[$this->index+2];
    ++$this->allies[$this->index+7];
  }

}

function LastAllyIndex($player) {
  $allies = &GetAllies($player);
  return count($allies) - AllyPieces();
}

?>
