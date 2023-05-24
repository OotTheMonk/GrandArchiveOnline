<?php

function ModalAbilities($player, $card, $lastResult)
{
  global $combatChain, $defPlayer;
  switch($card)
  {

    default: return "";
  }
}

function PlayerTargetedAbility($player, $card, $lastResult)
{
  global $dqVars;
  $target = ($lastResult == "Target_Opponent" ? ($player == 1 ? 2 : 1) : $player);
  switch($card)
  {

    default: return $lastResult;
  }
}

function SpecificCardLogic($player, $card, $lastResult)
{
  global $dqVars, $CS_DamageDealt;
  switch($card)
  {
    case "CREATIVESHOCK":
      if(CardElement($lastResult) == "FIRE") DealArcane(2, 2, "PLAYCARD", "BqDw4Mei4C", true, $player);
      return $lastResult;
    case "SCRYTHESKIES":
      $deck = &GetDeck($player);
      if(count($deck) > 0) AddMemory(array_shift($deck), $player, "DECK", "DOWN");
      return $lastResult;
    default: return "";
  }
}

?>
