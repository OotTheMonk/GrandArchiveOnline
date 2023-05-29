<?php

include "Constants.php";
include "GeneratedCode/GeneratedCardDictionaries.php";

function CardType($cardID)
{
  if(!$cardID) return "";
  if(CardTypeContains($cardID, "ATTACK")) return "AA";
  return CardSpeed($cardID) == "1" ? "I" : "A";
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
  global $currentPlayer;
  switch($cardID)
  {
    case "UfQh069mc3": return true;
    case "SSu2eQZFJV": return true;
    case "ZgA7cWNKGy": return true;
    case "WsunZX4IlW": return true;//Ravaging Tempest
    case "uTBsOYf15p": return true;//Purging Flames
    case "IyXuaLKjSA": return IsClassBonusActive($currentPlayer, "MAGE");//Frozen Nova
    case "4NkVdSx9ed": return true;//Careful Study
    default: return false;
  }
}

function HasVigor($cardID, $player)
{
  switch($cardID)
  {
    case "JEOxGQppTE"://Windrider Vanguard
      return IsClassBonusActive($player, "WARRIOR") || IsClassBonusActive($player, "GUARDIAN");
    case "3TfIePpuZO": return true;//Trained Hawk
    case "7NMFSRR5V3": return IsClassBonusActive($player, "TAMER");
    default: return false;
  }
}

function HasTrueSight($cardID, $player)
{
  switch($cardID)
  {
    case "3TfIePpuZO": return true;//Trained Hawk
    case "LNSRQ5xW6E": return true;//Stillwater Patrol
    default: return false;
  }
}

function HasStealth($cardID, $player, $index)
{
  $allies = &GetAllies($player);
  switch($cardID)
  {
    case "aKgdkLSBza": return IsClassBonusActive($player, "TAMER");//Wilderness Harpist
    case "CvvgJR4fNa": return $allies[$index+1] == 2 && IsClassBonusActive($player, "ASSASSIN");//Patient Rogue
    case "hHVf5xyjob": return GetClassState($player, $CS_PreparationCounters) >= 3;//Blackmarket Broker
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
    case "UiohpiTtgs"://Chalice of Blood
    case "P7hHZBVScB"://Orb of Glitter
    case "6e7lRnczfL"://Horn of Beastcalling
    case "BY0E8si926"://Orb of Regret
    case "dmfoA7jOjy"://Crystal of Empowerment
    case "IC3OU6vCnF"://Mana Limiter
    case "hLHpI5rHIK"://Bauble of Mending
    case "WAFNy2lY5t"://Melodious Flute
    case "AKA19OwaCh"://Jewel of Englightenment
    case "j5iQQPd2m5"://Crystal of Argus
      return "I";
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
  if($cardType == "I" && ($phase == "INSTANT" || $phase == "A" || $phase == "D")) return true;
  return false;

}

//Preserve
function GoesWhereAfterResolving($cardID, $from = null, $player = "", $playedFrom="")
{
  global $currentPlayer, $mainPlayer;
  if($player == "") $player = $currentPlayer;
  $otherPlayer = $player == 2 ? 1 : 2;
  if(CardTypeContains($cardID, "ALLY", $currentPlayer)) return "ALLY";
  switch($cardID) {
    case "2Ojrn7buPe": return "MATERIAL";//Tera Sight
    case "PLljzdiMmq": return "MATERIAL";//Invoke Dominance
    case "cVRIUJdTW5": return "MATERIAL";//Meadowbloom Dryad
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

    default: return false;
  }
}

function HasBattleworn($cardID)
{
  switch($cardID) {

    default: return false;
  }
}

function HasTemper($cardID)
{
  switch($cardID) {

    default: return false;
  }
}

function RequiresDiscard($cardID)
{
  switch($cardID) {

    default: return false;
  }
}

function ETASteamCounters($cardID)
{
  switch ($cardID) {

    default: return 0;
  }
}

function AbilityHasGoAgain($cardID)
{
  return true;
}

function DoesEffectGrantDominate($cardID)
{
  global $combatChainState;
  switch ($cardID) {

    default: return false;
  }
}

function CharacterNumUsesPerTurn($cardID)
{
  switch ($cardID) {

    default: return 1;
  }
}

//Active (2 = Always Active, 1 = Yes, 0 = No)
function CharacterDefaultActiveState($cardID)
{
  switch ($cardID) {

    default: return 2;
  }
}

//Hold priority for triggers (2 = Always hold, 1 = Hold, 0 = Don't Hold)
function AuraDefaultHoldTriggerState($cardID)
{
  switch ($cardID) {

    default: return 2;
  }
}

function ItemDefaultHoldTriggerState($cardID)
{
  switch($cardID) {

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

    default: return false;
  }
}

//Is it active AS OF THIS MOMENT?
function RepriseActive()
{
  global $currentPlayer, $mainPlayer;
  return 0;
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
  return false;
}

function SpellVoidAmount($cardID, $player)
{
  if($cardID == "ARC112" && SearchCurrentTurnEffects("DYN171", $player)) return 1;
  switch($cardID) {
    default: return 0;
  }
}

function IsSpecialization($cardID)
{
  switch ($cardID) {

    default:
      return false;
  }
}

function Is1H($cardID)
{
  switch ($cardID) {

    default:
      return false;
  }
}

function AbilityPlayableFromCombatChain($cardID)
{
  switch($cardID) {

    default: return false;
  }
}

function CardHasAltArt($cardID)
{
  switch ($cardID) {

  default:
      return false;
  }
}

function IsIyslander($character)
{
  switch($character) {
    default: return false;
  }
}

function WardAmount($cardID)
{
  switch($cardID)
  {
    default: return 0;
  }
}

function HasWard($cardID)
{
  switch ($cardID) {

    default: return false;
  }
}

function HasDominate($cardID)
{
  global $mainPlayer, $combatChainState;
  global $CS_NumAuras, $CCS_NumBoosted;
  switch ($cardID)
  {

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
