<?php


//Return 1 if the effect should be removed
function EffectHitEffect($cardID)
{
  global $combatChainState, $CCS_GoesWhereAfterLinkResolves, $defPlayer, $mainPlayer, $CCS_WeaponIndex, $combatChain, $CCS_DamageDealt;
  global $CID_BloodRotPox, $CID_Frailty, $CID_Inertia;
  $attackID = $combatChain[0];
  switch($cardID) {

    default:
      break;
  }
  return 0;
}

function EffectAttackModifier($cardID)
{
  global $mainPlayer;
  $params = explode("-", $cardID);
  $cardID = $params[0];
  if(count($params) > 1) $subparam = $params[1];
  switch($cardID)
  {
    case "dZ960Hnkzv": return SearchCount(SearchAllies($mainPlayer, "", "BEAST")) + SearchCount(SearchAllies($mainPlayer, "", "ANIMAL"));//Vertus, Gaia's Roar
    case "FCbKYZcbNq": return 2;
    case "4hbA9FT56L": return 1;
    case "At1UNRG7F0": return 4;
    case "CvvgJR4fNa": return 3;//Patient Rogue
    case "W1vZwOXfG3": return 2;//Embertail Squirrel
    case "rPpLwLPGaL": return 1;//Phalanx Captain
    case "k71PE3clOI": return 1;//Inspiring Call
    case "Huh1DljE0j": return 1;//Second Wind
    case "IAkuSSnzYB": return 1;//Banner Knight
    case "XMb6pSHFJg": return 2;//Embersong
    case "qyQLlDYBlr": return 1;//Ornamental Greatsword
    default: return 0;
  }
}

function EffectHasBlockModifier($cardID)
{
  switch($cardID)
  {
    default: return false;
  }
}

function EffectBlockModifier($cardID, $index)
{
  global $combatChain, $defPlayer, $mainPlayer;
  switch($cardID) {

    default:
      return 0;
  }
}

function RemoveEffectsOnChainClose()
{

}

function OnAttackEffects($attack)
{
  global $currentTurnEffects, $mainPlayer, $defPlayer;
  $attackType = CardType($attack);
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $mainPlayer) {
      switch($currentTurnEffects[$i]) {

        default:
          break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
}

function CurrentEffectBaseAttackSet($cardID)
{
  global $currentPlayer, $currentTurnEffects;
  $mod = -1;
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    if($currentTurnEffects[$i + 1] == $currentPlayer && IsCombatEffectActive($currentTurnEffects[$i])) {
      switch($currentTurnEffects[$i]) {

        default: break;
      }
    }
  }
  return $mod;
}

function CurrentEffectCostModifiers($cardID, $from)
{
  global $currentTurnEffects, $currentPlayer, $CS_PlayUniqueID;
  $costModifier = 0;
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $currentPlayer) {
      switch($currentTurnEffects[$i]) {
        case "6e7lRnczfL"://Horn of Beastcalling
          if(SubtypeContains($cardID, "BEAST")) { $costModifier -= 3; $remove = true; }
          break;
        case "EBWWwvSxr3"://Channeling Stone
          $costModifier -= 2;
          break;
        default: break;
      }
      if($remove) RemoveCurrentTurnEffect($i);
    }
  }
  return $costModifier;
}

function CurrentEffectPreventDamagePrevention($player, $type, $damage, $source)
{
  global $currentTurnEffects;
  for($i = count($currentTurnEffects) - CurrentTurnEffectPieces(); $i >= 0; $i -= CurrentTurnEffectPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $player) {
      switch ($currentTurnEffects[$i]) {

        default: break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
  return $damage;
}

function CurrentEffectDamagePrevention($player, $type, $damage, $source, $preventable)
{
  global $currentPlayer, $currentTurnEffects;
  for($i = count($currentTurnEffects) - CurrentTurnEffectPieces(); $i >= 0 && $damage > 0; $i -= CurrentTurnEffectPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $player) {
      $effects = explode("-", $currentTurnEffects[$i]);
      switch($effects[0]) {
        case "RUqtU0Lczf"://Spellshield: Arcane
          if($preventable)
          {
            PlayAura("ENLIGHTEN", $player, $damage);
            $damage = 0;
          }
          $remove = true;
          break;
        case "xWJND68I8X"://Water Barrier
          if($preventable) $damage = 1;
          $remove = true;
          break;
        default: break;
      }
      if($remove) RemoveCurrentTurnEffect($i);
    }
  }
  return $damage;
}

function CurrentEffectAttackAbility()
{
  global $currentTurnEffects, $combatChain, $mainPlayer;
  global $CS_PlayIndex;
  if(count($combatChain) == 0) return;
  $attackID = $combatChain[0];
  $attackType = CardType($attackID);
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $mainPlayer) {
      switch ($currentTurnEffects[$i]) {

        default:
          break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
}

function CurrentEffectPlayAbility($cardID, $from)
{
  global $currentTurnEffects, $currentPlayer, $actionPoints, $CS_LastDynCost;

  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $currentPlayer) {
      switch($currentTurnEffects[$i]) {

        default:
          break;
      }
      if($remove) RemoveCurrentTurnEffect($i);
    }
  }
  return false;
}

function CurrentEffectPlayOrActivateAbility($cardID, $from)
{
  global $currentTurnEffects, $currentPlayer;

  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $currentPlayer) {
      switch($currentTurnEffects[$i]) {

        default:
          break;
      }
      if($remove) RemoveCurrentTurnEffect($i);
    }
  }
  $currentTurnEffects = array_values($currentTurnEffects); //In case any were removed
  return false;
}

function CurrentEffectGrantsNonAttackActionGoAgain($cardID)
{
  global $currentTurnEffects, $currentPlayer;
  $hasGoAgain = false;
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $currentPlayer) {
      switch($currentTurnEffects[$i]) {

        default:
          break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
  return $hasGoAgain;
}

function CurrentEffectGrantsGoAgain()
{
  global $currentTurnEffects, $mainPlayer, $combatChainState, $CCS_AttackFused;
  for($i = 0; $i < count($currentTurnEffects); $i += CurrentTurnEffectPieces()) {
    if($currentTurnEffects[$i + 1] == $mainPlayer && IsCombatEffectActive($currentTurnEffects[$i]) && !IsCombatEffectLimited($i)) {
      switch ($currentTurnEffects[$i]) {

        default:
          break;
      }
    }
  }
  return false;
}

function CurrentEffectPreventsGoAgain()
{
  global $currentTurnEffects, $mainPlayer;
  for($i = 0; $i < count($currentTurnEffects); $i += CurrentTurnEffectPieces()) {
    if($currentTurnEffects[$i + 1] == $mainPlayer) {
      switch($currentTurnEffects[$i]) {
        default: break;
      }
    }
  }
  return false;
}

function CurrentEffectPreventsDefenseReaction($from)
{
  global $currentTurnEffects, $currentPlayer;
  $reactionPrevented = false;
  for($i = 0; $i < count($currentTurnEffects); $i += CurrentTurnEffectPieces()) {
    if($currentTurnEffects[$i + 1] == $currentPlayer) {
      switch($currentTurnEffects[$i]) {

        default:
          break;
      }
    }
  }
  return $reactionPrevented;
}

function CurrentEffectPreventsDraw($player, $isMainPhase)
{
  global $currentTurnEffects;
  for($i = 0; $i < count($currentTurnEffects); $i += CurrentTurnEffectPieces()) {
    if($currentTurnEffects[$i + 1] == $player) {
      switch ($currentTurnEffects[$i]) {
        default: break;
      }
    }
  }
  return false;
}

function CurrentEffectIntellectModifier()
{
  global $currentTurnEffects, $mainPlayer;
  $intellectModifier = 0;
  for($i = count($currentTurnEffects) - CurrentTurnEffectPieces(); $i >= 0; $i -= CurrentTurnEffectPieces()) {
    if($currentTurnEffects[$i + 1] == $mainPlayer) {
      switch($currentTurnEffects[$i]) {

        default: break;
      }
    }
  }
  return $intellectModifier;
}

function CurrentEffectEndTurnAbilities()
{
  global $currentTurnEffects, $mainPlayer;
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    $cardID = substr($currentTurnEffects[$i], 0, 6);
    if(SearchCurrentTurnEffects($cardID . "-UNDER", $currentTurnEffects[$i + 1])) {
      AddNextTurnEffect($currentTurnEffects[$i], $currentTurnEffects[$i + 1]);
    }
    switch($currentTurnEffects[$i]) {

      default: break;
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
}

function IsCombatEffectActive($cardID)
{
  global $combatChain, $currentPlayer;
  if(count($combatChain) == 0) return;
  $attackID = $combatChain[0];
  switch($cardID)
  {
    case "dZ960Hnkzv": return IsAlly($attackID);
    case "FCbKYZcbNq": return true;
    case "4hbA9FT56L": return IsAlly($attackID);
    case "At1UNRG7F0": return true;//Devastating Blow
    case "CvvgJR4fNa": return true;//Patient Rogue
    case "W1vZwOXfG3": return true;//Embertail Squirrel
    case "rPpLwLPGaL": return true;//Phalanx Captain
    case "k71PE3clOI": return IsAlly($attackID);//Inspiring Call
    case "Huh1DljE0j": return true;//Second Wind
    case "IAkuSSnzYB": return true;//Banner Knight
    case "XMb6pSHFJg": return true;//Embersong
    case "qyQLlDYBlr": return true;//Ornamental Greatsword
    default: return false;
  }
}

function IsCombatEffectPersistent($cardID)
{
  global $currentPlayer;
  $effectArr = explode(",", $cardID);
  $cardID = ShiyanaCharacter($effectArr[0]);
  switch($cardID) {
    case "dZ960Hnkzv": return true;
    case "4hbA9FT56L": return true;
    case "CvvgJR4fNa": return true;//Patient Rogue
    case "W1vZwOXfG3": return true;//Embertail Squirrel
    case "k71PE3clOI": return true;//Inspiring Call
    case "XMb6pSHFJg": return true;//Embersong
    case "qyQLlDYBlr": return true;//Ornamental Greatsword
    default:
      return false;
  }
}

function BeginEndPhaseEffects()
{
  global $currentTurnEffects, $mainPlayer, $EffectContext;
  for($i = 0; $i < count($currentTurnEffects); $i += CurrentTurnPieces()) {
    $EffectContext = $currentTurnEffects[$i];
    switch($currentTurnEffects[$i]) {

      default:
        break;
    }
  }
}

function BeginEndPhaseEffectTriggers()
{
  global $currentTurnEffects, $mainPlayer;
  for($i = 0; $i < count($currentTurnEffects); $i += CurrentTurnPieces()) {
    switch($currentTurnEffects[$i]) {
      case "blq7qXGvWH":
        DiscardHand($mainPlayer);
        WriteLog("Arcane Disposition discarded your hand");
        break;
      default: break;
    }
  }
}

function ActivateAbilityEffects()
{
  global $currentPlayer, $currentTurnEffects;
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $currentPlayer) {
      switch($currentTurnEffects[$i]) {

        default:
          break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
  $currentTurnEffects = array_values($currentTurnEffects);
}

function CurrentEffectNameModifier($effectID, $effectParameter)
{
  $name = "";
  switch($effectID)
  {

    default: break;
  }
  return $name;
}

function CurrentEffectAllyEntersPlay($player, $index)
{
  global $currentTurnEffects;
  $allies = &GetAllies($player);
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $player) {
      switch($currentTurnEffects[$i]) {
        case "RfPP8h16Wv":
          if(SubtypeContains($allies[$index], "BEAST", $player) || SubtypeContains($allies[$index], "ANIMAL", $player))
          {
            ++$allies[$index+2];
            ++$allies[$index+7];
            $remove = 1;
          }
          break;
        default:
          break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
  $currentTurnEffects = array_values($currentTurnEffects);
}

function CurrentEffectLevelModifier($player)
{
  global $currentTurnEffects;
  $levelModifier = 0;
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $player) {
      $arr = explode("-", $currentTurnEffects[$i]);
      $subparam = count($arr) > 1 ? $arr[1] : 0;
      switch($arr[0]) {
        case "MECS7RHRZ8": $levelModifier += 1; break;
        case "XLrHaYV9VB": $levelModifier += 1; break;
        case "9GWxrTMfBz": $levelModifier += 1; break;
        case "gvXQa57cxe": $levelModifier += 1; break;
        case "PLljzdiMmq": $levelModifier += 3; break;
        case "zpkcFs72Ah": $levelModifier += 1; break;
        case "aKgdkLSBza": $levelModifier += 1; break;//Wilderness Harpist
        case "dmfoA7jOjy": $levelModifier += 2; break;//Crystal of Empowerment
        case "Kc5Bktw0yK": $levelModifier += 2; break;//Empowering Harmony
        case "raG5r85ieO": $levelModifier += 1; break;//Piper's Lullaby
        case "j5iQQPd2m5": $levelModifier += $subparam; break;//Crystal of Argus
        case "ybdj1Db9jz": $levelModifier += 2; break;//Seed of Nature
        default:
          break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
  $currentTurnEffects = array_values($currentTurnEffects);
  return $levelModifier;
}

?>
