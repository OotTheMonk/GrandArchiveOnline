<?php

include "Constants.php";
include "GeneratedCode/GeneratedCardDictionaries.php";

function CardType($cardID)
{
  if(!$cardID) return "";
  if(CardTypeContains($cardID, "ATTACK", $currentPlayer)
}

function CardSubType($cardID)
{
  if(!$cardID) return "";
  return CardSubTypes($cardID);
}

function CharacterHealth($cardID)
{
  $set = CardSet($cardID);
  if($set != "ROG" && $set != "DUM") {
    $number = intval(substr($cardID, 3));
    if($number < 400) return GeneratedCharacterHealth($cardID);
  }
  switch($cardID) {
    case "DUMMY": return 1000;
    case "ROGUE001": return 6;
    case "ROGUE003": return 8;
    case "ROGUE004": return 14;
    case "ROGUE008": return 20;
    case "ROGUE006": return 14;
    case "ROGUE009": return 10;
    case "ROGUE010": return 14;
    case "ROGUE013": return 14;
    case "ROGUE014": return 6;
    case "ROGUE015": return 13;
    case "ROGUE016": return 8;
    case "ROGUE017": return 20;
    case "ROGUE018": return 10;
    case "ROGUE019": return 18;
    case "ROGUE020": return 6;
    case "ROGUE021": return 8;
    case "ROGUE022": return 10;
    case "ROGUE023": return 12;
    case "ROGUE024": return 15;
    case "ROGUE025": return 20;
    case "ROGUE026": return 99;
    case "ROGUE027": return 6;
    case "ROGUE028": return 14;
    case "ROGUE029": return 16;
    case "ROGUE030": return 14;
    case "ROGUE031": return 16;
    default: return 20;
  }
}

function CharacterIntellect($cardID)
{
  $cardID = ShiyanaCharacter($cardID);
  switch($cardID) {
    case "CRU099": return 3;
    case "ROGUE001": return 3;
    case "ROGUE003": return 3;
    case "ROGUE004": return 3;
    case "ROGUE008": return 4;
    case "ROGUE006": return 3;
    case "ROGUE009": return 3;
    case "ROGUE010": return 4;
    case "ROGUE013": return 4;
    case "ROGUE014": return 3;
    case "ROGUE015": return 0;
    case "ROGUE016": return 3;
    case "ROGUE017": return 0;
    case "ROGUE018": return 4;
    case "ROGUE019": return 1;
    case "ROGUE020": return 3;
    case "ROGUE021": return 1;
    case "ROGUE022": return 3;
    case "ROGUE023": return 3;
    case "ROGUE024": return 3;
    case "ROGUE025": return 4;
    case "ROGUE026": return 3;
    case "ROGUE027": return 3;
    case "ROGUE028": return 4;
    case "ROGUE029": return 4;
    case "ROGUE030": return 4;
    case "ROGUE031": return 4;
    default: return 4;
  }
}

function CardSet($cardID)
{
  if(!$cardID) return "";
  return substr($cardID, 0, 3);
}

function CardClass($cardID)
{
  return CardClasses($cardID);
}

function CardTalent($cardID)
{
  $set = substr($cardID, 0, 3);
  if($set == "MON") return MONCardTalent($cardID);
  else if($set == "ELE") return ELECardTalent($cardID);
  else if($set == "UPR") return UPRCardTalent($cardID);
  else if($set == "DYN") return DYNCardTalent($cardID);
  else if($set == "ROG") return ROGUECardTalent($cardID);
  return "NONE";
}

function HasEfficiency($cardID)
{
  switch($cardID)
  {
    case "UfQh069mc3": return true;
    default: return false;
  }
}

//Minimum cost of the card
function CardCost($cardID)
{
  $cardID = ShiyanaCharacter($cardID);
  $set = CardSet($cardID);
  $class = CardClass($cardID);
  switch($cardID)
  {
    case "ARC009": return 0;
    case "MON231": return 0;
    case "EVR022": return 3;
    case "EVR124": return 0;
    case "UPR109": return 0;
    default: break;
  }
  if($set != "ROG" && $set != "DUM") {
    $number = intval(substr($cardID, 3));
    if($number < 400) return GeneratedCardCost($cardID);
  }
  if($set == "ROG") return ROGUECardCost($cardID);
}

function AbilityCost($cardID)
{
  global $currentPlayer;
  if(CardTypeContains($cardID, "ALLY", $currentPlayer)) return 0;
  return 0;
}

function DynamicCost($cardID)
{
  global $currentPlayer;
  switch($cardID) {
    case "WTR051": case "WTR052": case "WTR053": return "2,6";
    case "ARC009": return "0,2,4,6,8,10,12";
    case "MON231": return "0,2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40";
    case "EVR022": return "3,4,5,6,7,8,9,10,11,12";
    case "EVR124": return GetIndices(SearchCount(SearchAura(($currentPlayer == 1 ? 2 : 1), "", "", 0)) + 1);
    case "UPR109": return "0,2,4,6,8,10,12,14,16,18,20";
    default: return "";
  }
}

function PitchValue($cardID)
{
  if(!$cardID) return "";
  $set = CardSet($cardID);
  if($set != "ROG" && $set != "DUM") {
    $number = intval(substr($cardID, 3));
    if($number < 400) return GeneratedPitchValue($cardID);
  }
  if($set == "ROG") return ROGUEPitchValue($cardID);
}

function BlockValue($cardID)
{
  global $defPlayer;
  if(!$cardID) return "";
  $set = CardSet($cardID);
  if($cardID == "MON191") return SearchPitchForNumCosts($defPlayer) * 2;
  else if($cardID == "EVR138") return FractalReplicationStats("Block");
  if($set != "ROG" && $set != "DUM") {
    $number = intval(substr($cardID, 3));
    if($number < 400) return GeneratedBlockValue($cardID);
  }
  $class = CardClass($cardID);
  if($set == "ROG") return ROGUEBlockValue($cardID);
  switch($cardID) {
    case "MON400": case "MON401": case "MON402": return 0;
    case "DYN492a": return -1;
    case "DYN492b": return 5;
    case "DUMMYDISHONORED": return -1;
    default: return 3;
  }
}

function AttackValue($cardID)
{
  global $combatChainState, $CCS_NumBoosted, $mainPlayer, $currentPlayer;
  if(!$cardID) return "";
  return CardPower($cardID);
}

function HasGoAgain($cardID)
{
  return true;
}

function GetAbilityType($cardID, $index = -1, $from="-")
{
  global $currentPlayer;
  if(CardTypeContains($cardID, "ALLY", $currentPlayer)) return "AA";
  switch($cardID)
  {
    case "LROrzTmh55"://Fire Resonance Bauble
    case "2gv7DC0KID"://Grand Crusader's Ring
    case "bHGUNMFLg9"://Wind Resonance Bauble
    case "dSSRtNnPtw"://Water Resonance Bauble
    case "Z9TCpaMJTc"://Bauble of Abundance
    case "yDARN8eV6B"://Tome of Knowledge
    default: return "";
  }
}

function GetAbilityTypes($cardID)
{
  switch($cardID) {
    case "ARC003": case "CRU101": return "A,AA";
    case "OUT093": return "I,I";
    default: return "";
  }
}

function GetAbilityNames($cardID, $index = -1)
{
  global $currentPlayer;
  switch ($cardID) {
    case "ARC003": case "CRU101":
      $character = &GetPlayerCharacter($currentPlayer);
      if($index == -1) return "";
      $rv = "Add_a_steam_counter";
      if($character[$index + 2] > 0) $rv .= ",Attack";
      return $rv;
    case "OUT093": return "Load,Aim";
    default: return "";
  }
}

function GetAbilityIndex($cardID, $index, $abilityName)
{
  $names = explode(",", GetAbilityNames($cardID, $index));
  for($i = 0; $i < count($names); ++$i) {
    if($abilityName == $names[$i]) return $i;
  }
  return 0;
}

function GetResolvedAbilityType($cardID, $from="-")
{
  global $currentPlayer, $CS_AbilityIndex;
  if($from == "HAND") return "";
  $abilityIndex = GetClassState($currentPlayer, $CS_AbilityIndex);
  $abilityTypes = GetAbilityTypes($cardID);
  if($abilityTypes == "" || $abilityIndex == "-") return GetAbilityType($cardID, -1, $from);
  $abilityTypes = explode(",", $abilityTypes);
  return $abilityTypes[$abilityIndex];
}

function GetResolvedAbilityName($cardID, $from="-")
{
  global $currentPlayer, $CS_AbilityIndex;
  $abilityIndex = GetClassState($currentPlayer, $CS_AbilityIndex);
  $abilityNames = GetAbilityNames($cardID);
  if($abilityNames == "" || $abilityIndex == "-") return "";
  $abilityNames = explode(",", $abilityNames);
  return $abilityNames[$abilityIndex];
}

function IsPlayable($cardID, $phase, $from, $index = -1, &$restriction = null, $player = "")
{
  global $currentPlayer, $CS_NumActionsPlayed, $combatChainState, $CCS_BaseAttackDefenseMax, $CS_NumNonAttackCards, $CS_NumAttackCards;
  global $CCS_ResourceCostDefenseMin, $CCS_CardTypeDefenseRequirement, $actionPoints, $mainPlayer, $defPlayer;
  global $combatChain;
  if($from == "ARS" || $from == "CHAR" || $from == "BANISH") return false;
  if($player == "") $player = $currentPlayer;
  if($from == "PLAY")
  {
    $pride = AllyPride($cardID);
    if($pride >= 0 && CharacterLevel($player) < $pride) return false;
  }
  if($phase == "M" && $from == "HAND") return true;
  if($phase == "P" && $from == "HAND") return true;
  $cardType = CardType($cardID);
  $isStaticType = IsStaticType($cardType, $from, $cardID);
  if($isStaticType) $cardType = GetAbilityType($cardID, $index, $from);
  if($phase == "M" && ($cardType == "A" || $cardType == "AA" || $cardType == "I")) return true;
  if($phase == "INST" && $cardType == "I") return true;
  return false;

}

//Preserve
function GoesWhereAfterResolving($cardID, $from = null, $player = "", $playedFrom="")
{
  global $currentPlayer, $CS_NumWizardNonAttack, $CS_NumBoosted, $mainPlayer;
  if($player == "") $player = $currentPlayer;
  $otherPlayer = $player == 2 ? 1 : 2;
  if(CardTypeContains($cardID, "ALLY", $currentPlayer)) return "ALLY";
  switch($cardID) {
    case "2Ojrn7buPe": return "MATERIAL";//Tera Sight
    case "PLljzdiMmq": return "MATERIAL";//Invoke Dominance
    default: return "GY";
  }
}

function CanPlayInstant($phase)
{
  if($phase == "M") return true;
  if($phase == "A") return true;
  if($phase == "D") return true;
  if($phase == "INSTANT") return true;
  return false;
}

function IsPitchRestricted($cardID, &$restriction, $from = "", $index = -1)
{
  global $playerID;
  if(SearchCurrentTurnEffects("ELE035-3", $playerID) && CardCost($cardID) == 0) { $restriction = "ELE035"; return true; }
  $pitchValue = PitchValue($cardID);
  if($pitchValue == 1 && SearchCurrentTurnEffects("OUT101-1", $playerID)) { $restriction = "OUT101"; return true; }
  else if($pitchValue == 2 && SearchCurrentTurnEffects("OUT101-2", $playerID)) { $restriction = "OUT101"; return true; }
  else if($pitchValue == 3 && SearchCurrentTurnEffects("OUT101-3", $playerID)) { $restriction = "OUT101"; return true; }
  return false;
}

function IsPlayRestricted($cardID, &$restriction, $from = "", $index = -1, $player = "")
{
  global $currentPlayer, $mainPlayer;
  if($player == "") $player = $currentPlayer;
  $otherPlayer = ($currentPlayer == 1 ? 2 : 1);
  switch($cardID) {

    default: return false;
  }
}

function IsDefenseReactionPlayable($cardID, $from)
{
  global $combatChain, $mainPlayer;
  if(CurrentEffectPreventsDefenseReaction($from)) return false;
  return true;
}

function IsAction($cardID)
{
  $cardType = CardType($cardID);
  if($cardType == "A" || $cardType == "AA") return true;
  $abilityType = GetAbilityType($cardID);
  if($abilityType == "A" || $abilityType == "AA") return true;
  return false;
}

function GoesOnCombatChain($phase, $cardID, $from)
{
  global $layers;
  if($phase != "B" && $from == "EQUIP" || $from == "PLAY") $cardType = GetResolvedAbilityType($cardID, $from);
  else if($phase == "M" && $cardID == "MON192" && $from == "BANISH") $cardType = GetResolvedAbilityType($cardID, $from);
  else $cardType = CardType($cardID);
  if($cardType == "I") return false; //Instants as yet never go on the combat chain
  if($phase == "B" && count($layers) == 0) return true; //Anything you play during these combat phases would go on the chain
  if(($phase == "A" || $phase == "D") && $cardType == "A") return false; //Non-attacks played as instants never go on combat chain
  if($cardType == "AR") return true;
  if($cardType == "DR") return true;
  if(($phase == "M" || $phase == "ATTACKWITHIT") && $cardType == "AA") return true; //If it's an attack action, it goes on the chain
  return false;
}

function IsStaticType($cardType, $from = "", $cardID = "")
{
  if($cardType == "C" || $cardType == "E" || $cardType == "W") return true;
  if($from == "PLAY") return true;
  if($cardID != "" && $from == "BANISH" && AbilityPlayableFromBanish($cardID)) return true;
  return false;
}

function HasBladeBreak($cardID)
{
  global $defPlayer;
  switch($cardID) {
    case "WTR079": case "WTR150": case "WTR155": case "WTR156": case "WTR157": case "WTR158": return true;
    case "ARC041": return true;
    case "CRU122": return true;
    case "MON060": return true;
    case "ELE144": case "ELE204": case "ELE213": case "ELE224": return true;
    case "EVR037": case "EVR086": return true;
    case "DVR003": case "DVR006": return true;
    case "RVD003": return true;
    case "UPR136": case "UPR158": case "UPR182": return true;
    case "DYN045": case "DYN152": case "DYN171": return true;
    case "OUT049": case "OUT094": case "OUT099": case "OUT139": case "OUT140": case "OUT141": case "OUT157": case "OUT158": return true;
    case "MON241": case "MON242": case "MON243": case "MON244": return SearchCurrentTurnEffects($cardID, $defPlayer); //Ironhide
    case "OUT174": return SearchCurrentTurnEffects($cardID . "-BB", $defPlayer); //Vambrace of determination
    default: return false;
  }
}

function HasBattleworn($cardID)
{
  switch($cardID) {
    case "WTR004": case "WTR005": case "WTR041": case "WTR042": case "WTR080": case "WTR116": case "WTR117": return true;
    case "ARC004": case "ARC078": case "ARC150": return true;
    case "CRU053": return true;
    case "MON107": case "MON108": case "MON122": case "MON230": return true;
    case "EVR001": case "EVR053": return true;
    case "DVR005": return true;
    case "DYN006": case "DYN026": case "DYN046": case "DYN089": case "DYN117": case "DYN118": return true;
    case "OUT011": return true;
    default: return false;
  }
}

function HasTemper($cardID)
{
  switch($cardID) {
    case "CRU025": case "CRU081": case "CRU141": return true;
    case "EVR018": return true;
    case "EVR020": return true;
    case "UPR084": return true;
    case "DYN027": case "DYN492b": return true;
    default: return false;
  }
}

function RequiresDiscard($cardID)
{
  switch($cardID) {
    case "WTR006": case "WTR007": case "WTR008": case "WTR011": case "WTR012": case "WTR013": case "WTR014": case "WTR015":
    case "WTR016": case "WTR020": case "WTR021": case "WTR022": case "WTR029": case "WTR030": case "WTR031": case "WTR035":
    case "WTR036": case "WTR037":
    case "CRU010": case "CRU011": case "CRU012": case "CRU019": case "CRU020": case "CRU021":
    case "DYN007": case "DYN016": case "DYN017": case "DYN018": case "DYN019": case "DYN020": case "DYN021":
      return true;
    default: return false;
  }
}

function ETASteamCounters($cardID)
{
  switch ($cardID) {
    case "ARC017": return 1;
    case "ARC007": case "ARC019": return 2;
    case "ARC036": return 3;
    case "ARC035": return 4;
    case "ARC037": return 5;
    case "CRU104": return 0;
    case "CRU105": return 0;
    case "EVR069": return 1;
    case "EVR071": return 1;
    case "EVR072": return 3;
    case "DYN093": return 5;
    case "DYN110": return 3;
    case "DYN111": return 2;
    case "DYN112": return 1;
    default: return 0;
  }
}

function AbilityHasGoAgain($cardID)
{
  return true;
}

function DoesEffectGrantDominate($cardID)
{
  global $combatChainState, $CCS_AttackFused;
  $cardID = ShiyanaCharacter($cardID);
  switch ($cardID) {
    case "WTR038": case "WTR039": case "WTR197":
    case "ARC011": case "ARC012": case "ARC013": case "ARC019": case "ARC038": case "ARC039":
    case "CRU013": case "CRU014": case "CRU015":
    case "CRU038": case "CRU039": case "CRU040": case "CRU094-2": case "CRU095-2": case "CRU096-2":
    case "CRU106": case "CRU107": case "CRU108":
    case "MON109": case "MON129": case "MON130": case "MON131": case "MON132": case "MON133": case "MON134":
    case "MON195": case "MON196": case "MON197": case "MON223": case "MON224": case "MON225":
    case "MON278": case "MON279": case "MON280": case "MON406":
    case "ELE005": case "ELE016": case "ELE017": case "ELE018": case "ELE033-2": case "ELE056": case "ELE057": case "ELE058":
    case "ELE092-DOMATK": case "ELE097": case "ELE098": case "ELE099": case "ELE166": case "ELE167": case "ELE168": case "ELE205":
    case "EVR017": case "EVR019": case "UPR091":
    case "DYN028":
    case "ROGUE710-DO":
      return true;
    case "ELE154": case "ELE155": case "ELE156": return $combatChainState[$CCS_AttackFused] == 1;
    default: return false;
  }
}

function CharacterNumUsesPerTurn($cardID)
{
  switch ($cardID) {
    case "WTR038": case "WTR039": return 999;
    case "ELE034": return 2;
    case "UPR183": return 999;
    case "DYN001": case "DYN193": return 999;
    case "DYN492a": return 999;
    case "OUT093": return 2;
    default: return 1;
  }
}

//Active (2 = Always Active, 1 = Yes, 0 = No)
function CharacterDefaultActiveState($cardID)
{
  switch ($cardID) {
    case "WTR117": return 1;
    case "ARC152": return 1;
    case "CRU053": case "CRU161": return 1;
    case "MON122": return 1;
    case "ELE173": case "ELE174": return 1;
    case "MON061": case "MON090": case "MON188": case "MON400": case "MON401": case "MON402": return 1;
    case "DYN236": case "DYN237": case "DYN238": case "DYN239": return 1;
    case "EVR037": return 1;
    case "UPR004": case "UPR047": case "UPR125": case "UPR184": case "UPR185": case "UPR186": return 0;
    case "DYN006": return 1;
    default: return 2;
  }
}

//Hold priority for triggers (2 = Always hold, 1 = Hold, 0 = Don't Hold)
function AuraDefaultHoldTriggerState($cardID)
{
  switch ($cardID) {
    case "WTR046": case "WTR047": case "WTR054": case "WTR055": case "WTR056": case "WTR069": case "WTR070": case "WTR071":
    case "WTR072": case "WTR073": case "WTR074": case "WTR075": return 0;
    case "ARC112": return 1;
    case "CRU028": case "CRU029": case "CRU030": case "CRU031": case "CRU038": case "CRU039": case "CRU040":
     case "CRU075": case "CRU144": return 0;
    case "MON186": return 0;
    case "ELE025": case "ELE026": case "ELE027": case "ELE028": case "ELE029": case "ELE030":
    case "ELE206": case "ELE207": case "ELE208": case "ELE109": case "ELE110": case "ELE111": return 0;
    case "EVR107": case "EVR108": case "EVR109": case "EVR131": case "EVR132": case "EVR133": return 0;
    case "UPR190": case "UPR218": case "UPR219": case "UPR220": return 0;
    case "DYN217": return 0;
    default: return 2;
  }
}

function ItemDefaultHoldTriggerState($cardID)
{
  switch($cardID) {
    case "ARC007": case "ARC035":
    case "MON302":
    case "EVR069": case "EVR071":
      return 0;
    default: return 2;
  }
}

function IsCharacterActive($player, $index)
{
  $character = &GetPlayerCharacter($player);
  return $character[$index + 9] == "1";
}

function HasReprise($cardID)
{
  switch($cardID) {
    case "WTR118": case "WTR120": case "WTR121": case "WTR123": case "WTR124": case "WTR125": case "WTR132":
    case "WTR133": case "WTR134": case "WTR135": case "WTR136": case "WTR137": case "WTR138": case "WTR139": case "WTR140":
    case "CRU083": case "CRU088": case "CRU089": case "CRU090":
      return true;
    default: return false;
  }
}

//Is it active AS OF THIS MOMENT?
function RepriseActive()
{
  global $currentPlayer, $mainPlayer;
  if($currentPlayer == $mainPlayer) return CachedNumDefendedFromHand() > 0;
  else return 0;
}

function HasCombo($cardID)
{
  switch ($cardID) {
    case "WTR081": case "WTR083": case "WTR084": case "WTR085": case "WTR086": case "WTR087":
    case "WTR088": case "WTR089": case "WTR090": case "WTR091": case "WTR095": case "WTR096":
    case "WTR097": case "WTR104": case "WTR105": case "WTR106": case "WTR110": case "WTR111": case "WTR112":
      return true;
    case "CRU054": case "CRU055": case "CRU056": case "CRU057": case "CRU058": case "CRU059":
    case "CRU060": case "CRU061": case "CRU062":
      return true;
    case "EVR038": case "EVR040": case "EVR041": case "EVR042": case "EVR043":
      return true;
    case "DYN047":
    case "DYN056": case "DYN057": case "DYN058":
    case "DYN059": case "DYN060": case "DYN061":
      return true;
    case "OUT050":
    case "OUT051":
    case "OUT056": case "OUT057": case "OUT058":
    case "OUT059": case "OUT060": case "OUT061":
    case "OUT062": case "OUT063": case "OUT064":
    case "OUT065": case "OUT066": case "OUT067":
    case "OUT074": case "OUT075": case "OUT076":
    case "OUT080": case "OUT081": case "OUT082":
      return true;
  }
  return false;
}

function ComboActive($cardID = "")
{
  global $combatChainState, $combatChain, $chainLinkSummary, $mainPlayer;
  if(SearchCurrentTurnEffects("OUT183", $mainPlayer)) return false;
  if ($cardID == "" && count($combatChain) > 0) $cardID = $combatChain[0];
  if ($cardID == "") return false;
  if(count($chainLinkSummary) == 0) return false;//No combat active if no previous chain links
  $lastAttackNames = explode(",", $chainLinkSummary[count($chainLinkSummary)-ChainLinkSummaryPieces()+4]);
  for($i=0; $i<count($lastAttackNames); ++$i)
  {
    $lastAttackName = GamestateUnsanitize($lastAttackNames[$i]);
    switch ($cardID) {
      case "WTR081":
        if($lastAttackName == "Mugenshi: RELEASE") return true;
        break;
      case "WTR083":
        if($lastAttackName == "Whelming Gustwave") return true;
        break;
      case "WTR084":
        if($lastAttackName == "Rising Knee Thrust") return true;
        break;
      case "WTR085":
        if($lastAttackName == "Open the Center") return true;
        break;
      case "WTR086": case "WTR087": case "WTR088":
        if($lastAttackName == "Open the Center") return true;
        break;
      case "WTR089": case "WTR090": case "WTR091":
        if($lastAttackName == "Rising Knee Thrust") return true;
        break;
      case "WTR095": case "WTR096": case "WTR097":
        if($lastAttackName == "Head Jab") return true;
        break;
      case "WTR104": case "WTR105": case "WTR106":
        if($lastAttackName == "Leg Tap") return true;
        break;
      case "WTR110": case "WTR111": case "WTR112":
        if($lastAttackName == "Surging Strike") return true;
        break;
      case "CRU054":
        if($lastAttackName == "Crane Dance") return true;
        break;
      case "CRU055":
        if($lastAttackName == "Rushing River" || $lastAttackName == "Flood of Force") return true;
        break;
      case "CRU056":
        if($lastAttackName == "Crane Dance") return true;
        break;
      case "CRU057": case "CRU058": case "CRU059":
        if($lastAttackName == "Soulbead Strike") return true;
        break;
      case "CRU060": case "CRU061": case "CRU062":
        if($lastAttackName == "Torrent of Tempo") return true;
        break;
      case "EVR038":
        if($lastAttackName == "Rushing River" || $lastAttackName == "Flood of Force") return true;
        break;
      case "EVR040":
        if($lastAttackName == "Hundred Winds") return true;
        break;
      case "EVR041": case "EVR042": case "EVR043":
        if($lastAttackName == "Hundred Winds") return true;
        break;
      case "DYN047":
      case "DYN056": case "DYN057": case "DYN058":
      case "DYN059": case "DYN060": case "DYN061":
        if($lastAttackName == "Crouching Tiger") return true;
        break;
      case "OUT050":
        if($lastAttackName == "Spinning Wheel Kick") return true;
        break;
      case "OUT051":
        if($lastAttackName == "Bonds of Ancestry") return true;
        break;
      case "OUT056": case "OUT057": case "OUT058":
        if(str_contains($lastAttackName, "Gustwave")) return true;
        break;
      case "OUT059": case "OUT060": case "OUT061":
        if($lastAttackName == "Head Jab") return true;
        break;
      case "OUT062": case "OUT063": case "OUT064":
        if($lastAttackName == "Twin Twisters" || $lastAttackName == "Spinning Wheel Kick") return true;
        break;
      case "OUT065": case "OUT066": case "OUT067":
        if($lastAttackName == "Twin Twisters") return true;
        break;
      case "OUT074": case "OUT075": case "OUT076":
        if($lastAttackName == "Surging Strike") return true;
        break;
      case "OUT080": case "OUT081": case "OUT082":
        if($lastAttackName == "Head Jab") return true;
        break;
      default: break;
    }
  }
  return false;
}

function HasBloodDebt($cardID)
{
  switch ($cardID) {
    case "MON123"; case "MON124"; case "MON125"; case "MON126": case "MON127": case "MON128"; case "MON129":
    case "MON130": case "MON131"; case "MON135": case "MON136": case "MON137"; case "MON138": case "MON139":
    case "MON140"; case "MON141": case "MON142": case "MON143"; case "MON144": case "MON145": case "MON146";
    case "MON147": case "MON148": case "MON149"; case "MON156": case "MON158": case "MON159": case "MON160":
    case "MON161": case "MON165": case "MON166": case "MON167": case "MON168": case "MON169": case "MON170":
    case "MON171": case "MON172": case "MON173": case "MON174": case "MON175": case "MON176": case "MON177":
    case "MON178": case "MON179": case "MON180": case "MON181": case "MON182": case "MON183": case "MON184":
    case "MON185": case "MON187": case "MON191": case "MON192": case "MON194": case "MON200": case "MON201":
    case "MON202": case "MON203": case "MON204": case "MON205": case "MON209": case "MON210": case "MON211":
      return true;
    default: return false;
  }
}

function PlayableFromBanish($cardID, $mod="")
{
  global $currentPlayer, $CS_NumNonAttackCards, $CS_Num6PowBan;
  $mod = explode("-", $mod)[0];
  if($mod == "TCL" || $mod == "TT" || $mod == "TCC" || $mod == "NT" || $mod == "INST") return true;
  switch($cardID) {

    default: return false;
  }
}

function AbilityPlayableFromBanish($cardID)
{
  global $currentPlayer, $mainPlayer;
  switch($cardID) {
    default: return false;
  }
}

function RequiresDieRoll($cardID, $from, $player)
{
  global $turn;
  if(GetDieRoll($player) > 0) return false;
  if($turn[0] == "B") return false;
  $type = CardType($cardID);
  if($type == "AA" && AttackValue($cardID) >= 6 && (SearchCharacterActive($player, "CRU002") || SearchCurrentTurnEffects("CRU002-SHIYANA", $player))) return true;
  switch ($cardID) {
    case "WTR004": case "WTR005": case "WTR010": return true;
    case "WTR162": return $from == "PLAY";
    case "CRU009": return true;
    case "EVR004": return true;
    case "EVR014": case "EVR015": case "EVR016": return true;
  }
  return false;
}

function SpellVoidAmount($cardID, $player)
{
  if($cardID == "ARC112" && SearchCurrentTurnEffects("DYN171", $player)) return 1;
  switch($cardID) {
    case "ELE173": return 2;
    case "MON061": return 2;
    case "MON090": return 1;
    case "MON188": return 2;
    case "MON302": return 1;
    case "MON400": return 1;
    case "MON401": return 1;
    case "MON402": return 1;
    case "DYN236": case "DYN237": case "DYN238": case "DYN239": return 1;
		case "DYN246": return 1;
    default: return 0;
  }
}

function IsSpecialization($cardID)
{
  switch ($cardID) {
    case "WTR006": case "WTR009": case "WTR043": case "WTR047": case "WTR081": case "WTR083": case "WTR119": case "WTR121":
    case "ARC007": case "ARC009": case "ARC043": case "ARC046": case "ARC080": case "ARC083": case "ARC118": case "ARC121":
    case "CRU000": case "CRU074":
    case "MON005": case "MON007": case "MON035": case "MON189": case "MON190": case "MON198": case "MON199":
    case "ELE004": case "ELE036": case "ELE066":
    case "EVR003": case "EVR039": case "EVR055": case "EVR070":
    case "DVR008": case "RVD008": return true;
    case "UPR090": case "UPR091": case "UPR109": case "UPR126":
    case "DYN121":
    case "OUT013": case "OUT053": case "OUT055": case "OUT097": case "OUT098": case "OUT102": case "OUT103": case "OUT104": return true;
    default:
      return false;
  }
}

function Is1H($cardID)
{
  switch ($cardID) {
    case "WTR078": case "CRU049":
    case "CRU004": case "CRU005":
    case "CRU051": case "CRU052":
    case "CRU079": case "CRU080":
    case "MON105": case "MON106":
    case "ELE003": case "ELE202":
    case "DYN069": case "DYN070":
    case "DYN115": case "DYN116":
    case "OUT005": case "OUT006":
    case "OUT007": case "OUT008":
    case "OUT009": case "OUT010": return true;
    default:
      return false;
  }
}

function AbilityPlayableFromCombatChain($cardID)
{
  switch($cardID) {
    case "MON245":
    case "MON281": case "MON282": case "MON283":
    case "ELE195": case "ELE196": case "ELE197":
    case "EVR157":
      return true;
    default: return false;
  }
}

function CardHasAltArt($cardID)
{
  switch ($cardID) {
    case "WTR002": case "WTR150": case "WTR162":
    case "WTR224":
      return true;
    case "MON155": case "MON215": case "MON216":
    case "MON217": case "MON219": case "MON220":
      return true;
    case "ELE146":
      return true;
    case "UPR006": case "UPR007": case "UPR008":
    case "UPR009": case "UPR010": case "UPR011":
    case "UPR012": case "UPR013": case "UPR014":
    case "UPR015": case "UPR016": case "UPR017":
      return true;
    case "UPR042": case "UPR043": case "UPR169":
      return true;
    case "UPR406": case "UPR407": case "UPR408":
    case "UPR409": case "UPR410": case "UPR411":
    case "UPR412": case "UPR413": case "UPR414":
    case "UPR415": case "UPR416": case "UPR417":
      return true;
    case "DYN234":
      return true;
  default:
      return false;
  }
}

function IsIyslander($character)
{
  switch($character) {
    case 'EVR120': case 'UPR102': case 'UPR103': return true;
    default: return false;
  }
}

function WardAmount($cardID)
{
  switch($cardID)
  {
    case "DYN213": case "DYN214": return 1;
    case "DYN612": return 4;
    default: return 0;
  }
}

function HasWard($cardID)
{
  switch ($cardID) {
    case "MON103":
    case "UPR039": case "UPR040": case "UPR041":
    case "UPR218": case "UPR219": case "UPR220":
    case "DYN213":
    case "DYN214": case "DYN215": case "DYN217":
    case "DYN218": case "DYN219": case "DYN220":
    case "DYN221": case "DYN222": case "DYN223":
    case "DYN612":
      return true;
    default: return false;
  }
}

function HasDominate($cardID)
{
  global $mainPlayer, $combatChainState;
  global $CS_NumAuras, $CCS_NumBoosted;
  switch ($cardID)
  {
    case "WTR095": case "WTR096": case "WTR097": return (ComboActive() ? true : false);
    case "WTR179": case "WTR180": case "WTR181": return true;
    case "ARC080": return true;
    case "MON004": return true;
    case "MON023": case "MON024": case "MON025": return true;
    case "MON246": return SearchDiscard($mainPlayer, "AA") == "";
    case "MON275": case "MON276": case "MON277": return true;
    case "ELE209": case "ELE210": case "ELE211": return HasIncreasedAttack();
    case "EVR027": case "EVR028": case "EVR029": return true;
    case "EVR038": return (ComboActive() ? true : false);
    case "EVR076": case "EVR077": case "EVR078": return $combatChainState[$CCS_NumBoosted] > 0;
    case "EVR110": case "EVR111": case "EVR112": return GetClassState($mainPlayer, $CS_NumAuras) > 0;
    case "OUT027": case "OUT028": case "OUT029": return true;
    default: break;
  }
  return false;
}

function Rarity($cardID)
{
  $set = CardSet($cardID);
  if($set != "ROG" && $set != "DUM")
  {
    $number = intval(substr($cardID, 3));
    if($number < 400) return GeneratedRarity($cardID);
  }
  if ($set == "ROG") {
    return ROGUERarity($cardID);
  }
}
