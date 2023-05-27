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
      DrawIntoMemory($player);
      return $lastResult;
    case "FAIRYWHISPERS":
      $deck = &GetDeck($player);
      if(count($deck) > 0 && RevealCards($deck[0], $player) && CardElement($deck[0]) == "WIND") { AddHand($player, array_shift($deck)); }
      return $lastResult;
    case "ORBOFREGRET":
      $count = SearchCount($lastResult);
      for($i = 0; $i < $count; ++$i) Draw($player);
      WriteLog(CardLink("BY0E8si926", "BY0E8si926") . " shuffled and drew " . $count . " cards");
      return "1";
      return $lastResult;
    default: return "";
  }
}

?>
