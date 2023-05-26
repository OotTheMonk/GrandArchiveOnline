<?php

function ProcessHitEffect($cardID)
{
  WriteLog("Processing hit effect for " . CardLink($cardID, $cardID));
  global $currentPlayer, $combatChain;
  if(CardType($combatChain[0]) == "AA" && (SearchAuras("CRU028", 1) || SearchAuras("CRU028", 2))) return;
  if(HitEffectsArePrevented()) return;
  $cardID = ShiyanaCharacter($cardID);
  $set = CardSet($cardID);
  $class = CardClass($cardID);
  if($set == "WTR") return WTRHitEffect($cardID);
  else if($set == "ARC") {
    switch ($class) {
      case "MECHANOLOGIST": return ARCMechanologistHitEffect($cardID);
      case "RANGER": return ARCRangerHitEffect($cardID);
      case "RUNEBLADE": return ARCRunebladeHitEffect($cardID);
      case "WIZARD": return ARCWizardHitEffect($cardID);
      case "GENERIC": return ARCGenericHitEffect($cardID);
    }
  }
  else if($set == "CRU") return CRUHitEffect($cardID);
  else if ($set == "MON") {
    switch ($class) {
      case "BRUTE": return MONBruteHitEffect($cardID);
      case "ILLUSIONIST": return MONIllusionistHitEffect($cardID);
      case "RUNEBLADE": return MONRunebladeHitEffect($cardID);
      case "WARRIOR": return MONWarriorHitEffect($cardID);
      case "GENERIC": return MONGenericHitEffect($cardID);
      case "NONE": return MONTalentHitEffect($cardID);
      default: return "";
    }
  }
  else if($set == "ELE") {
    switch ($class) {
      case "GUARDIAN": return ELEGuardianHitEffect($cardID);
      case "RANGER": return ELERangerHitEffect($cardID);
      case "RUNEBLADE": return ELERunebladeHitEffect($cardID);
      default: return ELETalentHitEffect($cardID);
    }
  }
  else if ($set == "EVR") return EVRHitEffect($cardID);
  else if ($set == "UPR") return UPRHitEffect($cardID);
  else if ($set == "DYN") return DYNHitEffect($cardID);
  else if ($set == "OUT") return OUTHitEffect($cardID);
}

function AttackModifier($cardID, $from = "", $resourcesPaid = 0, $repriseActive = -1)
{
  global $mainPlayer, $mainPitch, $CS_Num6PowDisc, $combatChain, $combatChainState, $mainAuras, $CS_CardsBanished;
  global $CS_NumCharged, $CCS_NumBoosted, $defPlayer, $CS_ArcaneDamageTaken;
  global $CS_NumNonAttackCards, $CS_NumPlayedFromBanish, $CS_NumAuras, $CS_AtksWWeapon;
  if($repriseActive == -1) $repriseActive = RepriseActive();
  switch($cardID) {
    case "HWFWO0TB8l": return IsClassBonusActive($mainPlayer, "TAMER") ? 2 : 0;//Tempest Silverback
    case "krgjMyVHRd": return SearchDiscard($mainPlayer, element:"WATER");//Lakeside Serpent
    default: return 0;
  }
}

function BlockModifier($cardID, $from, $resourcesPaid)
{
  global $defPlayer, $CS_CardsBanished, $mainPlayer, $CS_ArcaneDamageTaken, $combatChain, $chainLinks;
  $blockModifier = 0;
  $cardType = CardType($cardID);
  if($cardType == "AA") $blockModifier += CountCurrentTurnEffects("ARC160-1", $defPlayer);
  if($cardType == "AA") $blockModifier += CountCurrentTurnEffects("EVR186", $defPlayer);
  if($cardType == "AA") $blockModifier += CountCurrentTurnEffects("ROGUE802", $defPlayer);
  if($cardType == "E" && (SearchCurrentTurnEffects("DYN095", $mainPlayer) || SearchCurrentTurnEffects("DYN096", $mainPlayer) || SearchCurrentTurnEffects("DYN097", $mainPlayer))) $blockModifier -= 1;
  if(SearchCurrentTurnEffects("ELE114", $defPlayer) && ($cardType == "AA" || $cardType == "A") && (TalentContains($cardID, "ICE", $defPlayer) || TalentContains($cardID, "EARTH", $defPlayer) || TalentContains($cardID, "ELEMENTAL", $defPlayer))) $blockModifier += 1;
  $defAuras = &GetAuras($defPlayer);
  for($i = 0; $i < count($defAuras); $i += AuraPieces()) {
    if($defAuras[$i] == "WTR072" && CardCost($cardID) >= 3) $blockModifier += 4;
    if($defAuras[$i] == "WTR073" && CardCost($cardID) >= 3) $blockModifier += 3;
    if($defAuras[$i] == "WTR074" && CardCost($cardID) >= 3) $blockModifier += 2;
    if($defAuras[$i] == "WTR046" && $cardType == "E") $blockModifier += 1;
    if($defAuras[$i] == "ELE109" && $cardType == "A") $blockModifier += 1;
  }
  switch($cardID) {
    case "WTR212": case "WTR213": case "WTR214":
      $blockModifier += $from == "ARS" ? 1 : 0;
      break;
    case "WTR051": case "WTR052": case "WTR053":
      $blockModifier += ($resourcesPaid >= 6 ? 3 : 0);
      break;
    case "ARC150":
      $blockModifier += (DefHasLessHealth() ? 1 : 0);
      break;
    case "CRU187":
      $blockModifier += ($from == "ARS" ? 2 : 0);
      break;
    case "MON075": case "MON076": case "MON077":
      return GetClassState($mainPlayer, $CS_CardsBanished) >= 3 ? 2 : 0;
    case "MON290": case "MON291": case "MON292":
      return count($defAuras) >= 1 ? 1 : 0;
    case "ELE227": case "ELE228": case "ELE229":
      return GetClassState($mainPlayer, $CS_ArcaneDamageTaken) > 0 ? 1 : 0;
    case "EVR050": case "EVR051": case "EVR052":
      return (CardCost($combatChain[0]) == 0 && CardType($combatChain[0]) == "AA" ? 2 : 0);
    case "DYN045":
      $blockModifier += (count($chainLinks) >= 3 ? 4 : 0);
      break;
    case "DYN036": case "DYN037": case "DYN038":
      $blockModifier += SearchCharacter($defPlayer, subtype: "Off-Hand", class: "GUARDIAN") != "" ? 4 : 0;
      break;
    default: break;
  }
  return $blockModifier;
}

function PlayBlockModifier($cardID)
{
  switch($cardID) {
    case "CRU189": return 4;
    case "CRU190": return 3;
    case "CRU191": return 2;
    case "ELE125": return 4;
    case "ELE126": return 3;
    case "ELE127": return 2;
    default: return 0;
  }
}

function OnDefenseReactionResolveEffects()
{
  global $currentTurnEffects, $defPlayer, $combatChain;
  switch($combatChain[0])
  {
    case "CRU051": case "CRU052":
      EvaluateCombatChain($totalAttack, $totalBlock);
      for($i = CombatChainPieces(); $i < count($combatChain); $i += CombatChainPieces()) {
        if($totalBlock > 0 && (intval(BlockValue($combatChain[$i])) + BlockModifier($combatChain[$i], "CC", 0) + $combatChain[$i + 6]) > $totalAttack) {
          AddLayer("TRIGGER", $mainPlayer, $combatChain[0]);
        }
      }
      break;
      default: break;
  }
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $defPlayer) {
      switch($currentTurnEffects[$i]) {
        case "OUT005": case "OUT006":
          $count = ModifyBlockForType("DR", -1); //AR is handled in OnBlockResolveEffects
          $remove = $count > 0;
          break;
        default: break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
}

function OnBlockResolveEffects()
{
  global $combatChain, $CS_DamageTaken, $defPlayer, $mainPlayer, $currentTurnEffects;
  //This is when blocking fully resolves, so everything on the chain from here is a blocking card except the first
  for($i = CombatChainPieces(); $i < count($combatChain); $i += CombatChainPieces()) {
    if(SearchCurrentTurnEffects("ARC160-1", $defPlayer) && CardType($combatChain[$i]) == "AA") CombatChainPowerModifier($i, 1);
    if(SearchCurrentTurnEffects("ROGUE802", $defPlayer) && CardType($combatChain[$i]) == "AA") CombatChainPowerModifier($i, 1);
    if(SearchAurasForCard("ELE117", $defPlayer) && CardType($combatChain[$i]) == "AA") CombatChainPowerModifier($i, 3);
    ProcessPhantasmOnBlock($i);
  }
  switch($combatChain[0]) {
    case "CRU051": case "CRU052":
      EvaluateCombatChain($totalAttack, $totalBlock);
      for($i = CombatChainPieces(); $i < count($combatChain); $i += CombatChainPieces()) {
        if($totalBlock > 0 && (intval(BlockValue($combatChain[$i])) + BlockModifier($combatChain[$i], "CC", 0) + $combatChain[$i + 6]) > $totalAttack) {
          AddLayer("TRIGGER", $mainPlayer, $combatChain[0]);
        }
      }
      break;
    case "ELE004":
      if(SearchCurrentTurnEffects($combatChain[0], $defPlayer)) {
        AddLayer("TRIGGER", $defPlayer, $combatChain[0]);
      }
      break;
    case "OUT185":
      for($i=0; $i<NumActionsBlocking(); ++$i)
      {
        AddDecisionQueue("MULTIZONEINDICES", $mainPlayer, "MYDISCARD:type=A;maxCost=" . CachedTotalAttack() . "&MYDISCARD:type=AA;maxCost=" . CachedTotalAttack());
        AddDecisionQueue("SETDQCONTEXT", $mainPlayer, "Choose an action card to put on top of your deck");
        AddDecisionQueue("MAYCHOOSEMULTIZONE", $mainPlayer, "<-", 1);
        AddDecisionQueue("MZREMOVE", $mainPlayer, "-", 1);
        AddDecisionQueue("MULTIADDTOPDECK", $mainPlayer, "-", 1);
      }
      break;
    default: break;
  }
  $blockedFromHand = 0;
  for($i = CombatChainPieces(); $i < count($combatChain); $i += CombatChainPieces()) {
    if($combatChain[$i+2] == "HAND") ++$blockedFromHand;
    switch($combatChain[$i]) {
      case "EVR018":
        if(!IsAllyAttacking()) {
          WriteLog(CardLink($combatChain[$i], $combatChain[$i]) . " trigger creates a layer.");
          AddLayer("TRIGGER", $mainPlayer, $combatChain[$i]);
        }
        else {
          WriteLog("<span style='color:red;'>No frostbite is created because there is no attacking hero when allies attack.</span>");
        }
        break;
      case "MON241": case "MON242": case "MON243": case "MON244": case "RVD005": case "RVD006": //Ironhide
      case "RVD015": //Pack Call
      case "ELE203": //Rampart of the Ram's Head
      case "MON089": //Phantasmal Footsteps
      case "UPR095": //Flameborn Retribution
      case "UPR182": //Crown of Providence
      case "UPR191": case "UPR192": case "UPR193": // Flex
      case "UPR194": case "UPR195": case "UPR196": //Fyendal's Fighting Spirit
      case "UPR203": case "UPR204": case "UPR205": //Brothers in Arms
      case "DYN152": //Hornet's Sting
      case "OUT099": //Wayfinder's Crest
      case "OUT174": //Vambrace of Determination
        AddLayer("TRIGGER", $defPlayer, $combatChain[$i], $i);
        break;
      default:
        break;
    }
  }
  if($blockedFromHand > 0 && SearchCharacterActive($mainPlayer, "ELE174", true) && (TalentContains($combatChain[0], "LIGHTNING", $mainPlayer) || TalentContains($combatChain[0], "ELEMENTAL", $mainPlayer)))
  {
    AddLayer("TRIGGER", $mainPlayer, "ELE174");
  }
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $defPlayer) {
      switch($currentTurnEffects[$i]) {
        case "DYN115": case "DYN116":
          $count = ModifyBlockForType("AA", 0);
          $remove = $count > 0;
          break;
        case "OUT005": case "OUT006":
          $count = ModifyBlockForType("AR", 0); //DR could not possibly be blocking at this time, see OnDefenseReactionResolveEffects
          $remove = $count > 0;
          break;
        case "OUT007": case "OUT008":
          $count = ModifyBlockForType("A", 0);
          $remove = $count > 0;
          break;
        case "OUT009": case "OUT010":
          $count = ModifyBlockForType("E", 0);
          $remove = $count > 0;
          break;
        default: break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
}

function BeginningReactionStepEffects()
{
  global $combatChain, $mainPlayer, $defPlayer;
  switch($combatChain[0])
  {
    case "OUT050":
      if(ComboActive())
      {
        $blockingCards = GetChainLinkCards($defPlayer);
        if($blockingCards != "")
        {
          $blockArr = explode(",", $blockingCards);
          $index = $blockArr[GetRandom(0, count($blockArr) - 1)];
          AddDecisionQueue("PASSPARAMETER", $defPlayer, $index, 1);
          AddDecisionQueue("REMOVECOMBATCHAIN", $defPlayer, "-", 1);
          AddDecisionQueue("MULTIBANISH", $defPlayer, "CC,-", 1);
        }
      }
  }
}

function ModifyBlockForType($type, $amount)
{
  global $combatChain, $defPlayer;
  $count = 0;
  for($i=CombatChainPieces(); $i<count($combatChain); $i+=CombatChainPieces())
  {
    if($combatChain[$i+1] != $defPlayer) continue;
    if(CardType($combatChain[$i]) != $type) continue;
    ++$count;
    $combatChain[$i+6] += $amount;
  }
  return $count;
}

function OnBlockEffects($index, $from)
{
  global $currentTurnEffects, $combatChain, $currentPlayer, $combatChainState, $CCS_WeaponIndex, $mainPlayer;
  $cardType = CardType($combatChain[$index]);
  $otherPlayer = ($currentPlayer == 1 ? 2 : 1);
  for($i = count($currentTurnEffects) - CurrentTurnPieces(); $i >= 0; $i -= CurrentTurnPieces()) {
    $remove = false;
    if($currentTurnEffects[$i + 1] == $currentPlayer) {
      switch($currentTurnEffects[$i]) {
        case "WTR092": case "WTR093": case "WTR094":
          if(HasCombo($combatChain[$index])) {
            $combatChain[$index + 6] += 2;
          }
          $remove = true;
          break;
        case "ELE004":
          if($cardType == "DR") {
            PlayAura("ELE111", $currentPlayer);
          }
          break;
        case "DYN042": case "DYN043": case "DYN044":
          if(ClassContains($combatChain[$index], "GUARDIAN", $currentPlayer) && CardSubType($combatChain[$index]) == "Off-Hand")
          {
            if($currentTurnEffects[$i] == "DYN042") $amount = 6;
            else if($currentTurnEffects[$i] == "DYN043") $amount = 5;
            else $amount = 4;
            $combatChain[$index + 6] += $amount;
            $remove = true;
          }
          break;
        case "DYN115": case "DYN116":
          if($cardType == "AA") $combatChain[$index + 6] -= 1;
          break;
        case "OUT005": case "OUT006":
          if($cardType == "AR") $combatChain[$index + 6] -= 1;
          break;
        case "OUT007": case "OUT008":
          if($cardType == "A") $combatChain[$index + 6] -= 1;
          break;
        case "OUT009": case "OUT010":
          if($cardType == "E") $combatChain[$index + 6] -= 1;
          break;
        default:
          break;
      }
    } else if($currentTurnEffects[$i + 1] == $otherPlayer) {
      switch($currentTurnEffects[$i]) {
        case "MON113": case "MON114": case "MON115":
          if($cardType == "AA" && NumAttacksBlocking() == 1) {
              AddCharacterEffect($otherPlayer, $combatChainState[$CCS_WeaponIndex], $currentTurnEffects[$i]);
              WriteLog(CardLink($currentTurnEffects[$i], $currentTurnEffects[$i]) . " gives your weapon +1 for the rest of the turn.");
          }
          break;
        default:
          break;
      }
    }
    if($remove) RemoveCurrentTurnEffect($i);
  }
  $currentTurnEffects = array_values($currentTurnEffects);
  switch($combatChain[0]) {
    case "CRU079": case "CRU080":
      if($cardType == "AA" && NumAttacksBlocking() == 1) {
        AddCharacterEffect($otherPlayer, $combatChainState[$CCS_WeaponIndex], $combatChain[0]);
        WriteLog(CardLink($combatChain[0], $combatChain[0]) . " got +1 for the rest of the turn.");
      }
      break;
    default:
      break;
  }
}

function CombatChainCloseAbilities($player, $cardID, $chainLink)
{
  global $chainLinkSummary, $mainPlayer, $defPlayer, $chainLinks;
  switch($cardID) {
    case "EVR002":
      if($chainLinkSummary[$chainLink*ChainLinkSummaryPieces()] == 0 && $chainLinks[$chainLink][0] == $cardID) {
        PlayAura("WTR225", $defPlayer);
      }
      break;
    case "UPR189":
      if($chainLinkSummary[$chainLink*ChainLinkSummaryPieces()+1] <= 2) {
        Draw($player);
        WriteLog(CardLink($cardID, $cardID) . " drew a card");
      }
      break;
    case "DYN121":
      if($player == $mainPlayer) PlayerLoseHealth($mainPlayer, GetHealth($mainPlayer));
      break;
    default:
      break;
  }
}

function NumNonEquipmentDefended()
{
  global $combatChain, $defPlayer;
  $number = 0;
  for($i = 0; $i < count($combatChain); $i += CombatChainPieces()) {
    $cardType = CardType($combatChain[$i]);
    if($combatChain[$i + 1] == $defPlayer && $cardType != "E" && $cardType != "C") ++$number;
  }
  return $number;
}

function NumCardsDefended()
{
  global $combatChain, $defPlayer;
  $number = 0;
  for($i = 0; $i < count($combatChain); $i += CombatChainPieces()) {
    if($combatChain[$i + 1] == $defPlayer) ++$number;
  }
  return $number;
}

function CombatChainPlayAbility($cardID)
{
  global $combatChain, $defPlayer;
  for($i = 0; $i < count($combatChain); $i += CombatChainPieces()) {
    switch($combatChain[$i]) {
      case "EVR122":
        if(ClassContains($cardID, "WIZARD", $defPlayer)) {
          $combatChain[$i + 6] += 2;
          WriteLog(CardLink($combatChain[$i], $combatChain[$i]) . " gets +2 defense");
        }
        break;
      default: break;
    }
  }
}

function IsDominateActive()
{
  global $currentTurnEffects, $mainPlayer, $CCS_WeaponIndex, $combatChain, $combatChainState;
  global $CS_NumAuras, $CCS_NumBoosted, $chainLinks, $chainLinkSummary;
  if(count($combatChain) == 0) return false;
  if(SearchCurrentTurnEffectsForCycle("EVR097", "EVR098", "EVR099", $mainPlayer)) return false;
  $characterEffects = GetCharacterEffects($mainPlayer);
  for($i = 0; $i < count($currentTurnEffects); $i += CurrentTurnEffectPieces()) {
    if($currentTurnEffects[$i + 1] == $mainPlayer && IsCombatEffectActive($currentTurnEffects[$i]) && !IsCombatEffectLimited($i) && DoesEffectGrantDominate($currentTurnEffects[$i])) return true;
  }
  for($i = 0; $i < count($characterEffects); $i += CharacterEffectPieces()) {
    if($characterEffects[$i] == $combatChainState[$CCS_WeaponIndex]) {
      switch($characterEffects[$i + 1]) {
        case "WTR122": return true;
        default: break;
      }
    }
  }
  switch($combatChain[0]) {
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
    case "EVR138":
      $hasDominate = false;
      for($i = 0; $i < count($chainLinks); ++$i)
      {
        for($j = 0; $j < count($chainLinks[$i]); $j += ChainLinksPieces())
        {
          $isIllusionist = ClassContains($chainLinks[$i][$j], "ILLUSIONIST", $mainPlayer) || ($j == 0 && DelimStringContains($chainLinkSummary[$i*ChainLinkSummaryPieces()+3], "ILLUSIONIST"));
          if($chainLinks[$i][$j+2] == "1" && $chainLinks[$i][$j] != "EVR138" && $isIllusionist && CardType($chainLinks[$i][$j]) == "AA")
          {
              if(!$hasDominate) $hasDominate = HasDominate($chainLinks[$i][$j]);
          }
        }
      }
      return $hasDominate;
    case "OUT027": case "OUT028": case "OUT029": return true;
    default: break;
  }
  return false;
}

function IsOverpowerActive()
{
  global $combatChain, $mainPlayer;
  if(count($combatChain) == 0) return false;
  switch($combatChain[0]) {
    case "DYN068": return SearchCurrentTurnEffects("DYN068", $mainPlayer);
    case "DYN088": return true;
    case "DYN227": case "DYN228": case "DYN229": return SearchCurrentTurnEffects("DYN227", $mainPlayer);
    case "DYN492a": return true;
    default: break;
  }
  return false;
}


?>
