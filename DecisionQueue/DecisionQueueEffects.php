<?php

function ModalAbilities($player, $card, $lastResult)
{
  global $combatChain, $defPlayer;
  switch($card)
  {
    case "AQUEOUSENCHANTING":
      WriteLog($lastResult);
      if($lastResult == "1_Attack") AddCurrentTurnEffect("fMv7tIOZwLAttack", $player);
      else GiveAlliesHealthBonus($player, 1);
      return $lastResult;
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
    case "CORHAZICOURIER":
      if(CardElement($lastResult) == "FIRE") DealArcane(1, 2, "PLAYCARD", "YqQsXwEvv5", true, $player);
      return $lastResult;
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
    case "REFURBISH":
      if($lastResult == "m31WVJ9F04") WriteLog("<span style='color:DarkGreen'>There, it doesn't look like it was stuck in a dusty old rock anymore</span>");
      return $lastResult;
    case "DEFLECTINGEDGE":
      global $CS_DamagePrevention;
      if($lastResult == "MYCHAR-0") IncrementClassState($player, $CS_DamagePrevention, 3);
      else
      {
        $allies = &GetAllies($player);
        $mzArr = explode("-", $lastResult);
        $allies[$mzArr[1]+6] += 3;
      }
      return $lastResult;
    case "ERUPTINGRHAPSODY":
      PrependDecisionQueue("SPECIFICCARD", $player, "ERUPTINGRHAPSODY", 1);
      PrependDecisionQueue("ADDCURRENTEFFECT", $player, "dBAdWMoPEz", 1);
      PrependDecisionQueue("MZREMOVE", $player, "-", 1);
      PrependDecisionQueue("MZADDZONE", $player, "MYBANISH,DECK,TT", 1);
      PrependDecisionQueue("MAYCHOOSEMULTIZONE", $player, "<-", 1);
      PrependDecisionQueue("MULTIZONEINDICES", $player, "MYDISCARD:element=FIRE");
      return $lastResult;
    case "ERUPTINGRHAPSODYHARMONIZE":
      for($i=0; $i<CharacterLevel($player); ++$i) DealArcane(1, 2, "PLAYCARD", "dBAdWMoPEz");
      return $lastResult;
    default: return "";
  }
}

?>
