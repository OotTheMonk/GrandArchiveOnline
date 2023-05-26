<?php

include "CardDictionary.php";
include "CoreLogic.php";

function PummelHit($player = -1, $passable = false, $fromDQ = false)
{
  global $defPlayer;
  if($player == -1) $player = $defPlayer;
  if($fromDQ)
  {
    PrependDecisionQueue("ADDDISCARD", $player, "HAND", 1);
    PrependDecisionQueue("MULTIREMOVEHAND", $player, "-", 1);
    PrependDecisionQueue("CHOOSEHAND", $player, "<-", 1);
    PrependDecisionQueue("SETDQCONTEXT", $player, "Choose a card to discard", 1);
    PrependDecisionQueue("FINDINDICES", $player, "HAND", ($passable ? 1 : 0));
  }
  else {
    AddDecisionQueue("FINDINDICES", $player, "HAND", ($passable ? 1 : 0));
    AddDecisionQueue("SETDQCONTEXT", $player, "Choose a card to discard", 1);
    AddDecisionQueue("CHOOSEHAND", $player, "<-", 1);
    AddDecisionQueue("MULTIREMOVEHAND", $player, "-", 1);
    AddDecisionQueue("ADDDISCARD", $player, "HAND", 1);
  }
}

function HandToTopDeck($player)
{
  AddDecisionQueue("FINDINDICES", $player, "HAND");
  AddDecisionQueue("MAYCHOOSEHAND", $player, "<-", 1);
  AddDecisionQueue("MULTIREMOVEHAND", $player, "-", 1);
  AddDecisionQueue("MULTIADDTOPDECK", $player, "-", 1);
}

function BottomDeck($player="", $mayAbility=false, $shouldDraw=false)
{
  global $currentPlayer;
  if($player == "") $player = $currentPlayer;
  AddDecisionQueue("FINDINDICES", $player, "HAND");
  AddDecisionQueue("SETDQCONTEXT", $player, "Put_a_card_from_your_hand_on_the_bottom_of_your_deck.");
  if($mayAbility) AddDecisionQueue("MAYCHOOSEHAND", $player, "<-", 1);
  else AddDecisionQueue("CHOOSEHAND", $player, "<-", 1);
  AddDecisionQueue("REMOVEMYHAND", $player, "-", 1);
  AddDecisionQueue("ADDBOTDECK", $player, "-", 1);
  AddDecisionQueue("WRITELOG", $player, "A card was put on the bottom of the deck", 1);
  if($shouldDraw) AddDecisionQueue("DRAW", $player, "-", 1);
}

function BottomDeckMultizone($player, $zone1, $zone2)
{
  AddDecisionQueue("MULTIZONEINDICES", $player, $zone1 . "&" . $zone2, 1);
  AddDecisionQueue("SETDQCONTEXT", $player, "Choose a card to sink (or Pass)", 1);
  AddDecisionQueue("MAYCHOOSEMULTIZONE", $player, "<-", 1);
  AddDecisionQueue("MZREMOVE", $player, "-", 1);
  AddDecisionQueue("ADDBOTDECK", $player, "-", 1);
}

function AddCurrentTurnEffectNextAttack($cardID, $player, $from = "", $uniqueID = -1)
{
  global $combatChain;
  if(count($combatChain) > 0) AddCurrentTurnEffectFromCombat($cardID, $player, $uniqueID);
  else AddCurrentTurnEffect($cardID, $player, $from, $uniqueID);
}

function AddCurrentTurnEffect($cardID, $player, $from = "", $uniqueID = -1)
{
  global $currentTurnEffects, $combatChain;
  $card = explode("-", $cardID)[0];
  if(CardType($card) == "A" && count($combatChain) > 0 && IsCombatEffectActive($cardID) && !IsCombatEffectPersistent($cardID) && $from != "PLAY") {
    AddCurrentTurnEffectFromCombat($cardID, $player, $uniqueID);
    return;
  }
  array_push($currentTurnEffects, $cardID);
  array_push($currentTurnEffects, $player);
  array_push($currentTurnEffects, $uniqueID);
  array_push($currentTurnEffects, CurrentTurnEffectUses($cardID));
}

function AddAfterResolveEffect($cardID, $player, $from = "", $uniqueID = -1)
{
  global $afterResolveEffects, $combatChain;
  $card = explode("-", $cardID)[0];
  if(CardType($card) == "A" && count($combatChain) > 0 && !IsCombatEffectPersistent($cardID) && $from != "PLAY") {
    AddCurrentTurnEffectFromCombat($cardID, $player, $uniqueID);
    return;
  }
  array_push($afterResolveEffects, $cardID);
  array_push($afterResolveEffects, $player);
  array_push($afterResolveEffects, $uniqueID);
  array_push($afterResolveEffects, CurrentTurnEffectUses($cardID));
}

function CopyCurrentTurnEffectsFromAfterResolveEffects()
{
  global $currentTurnEffects, $afterResolveEffects;
  for($i = 0; $i < count($afterResolveEffects); $i += CurrentTurnEffectPieces()) {
    array_push($currentTurnEffects, $afterResolveEffects[$i]);
    array_push($currentTurnEffects, $afterResolveEffects[$i+1]);
    array_push($currentTurnEffects, $afterResolveEffects[$i+2]);
    array_push($currentTurnEffects, $afterResolveEffects[$i+3]);
  }
  $afterResolveEffects = [];
}

//This is needed because if you add a current turn effect from combat, it could get deleted as part of the combat resolution
function AddCurrentTurnEffectFromCombat($cardID, $player, $uniqueID = -1)
{
  global $currentTurnEffectsFromCombat;
  array_push($currentTurnEffectsFromCombat, $cardID);
  array_push($currentTurnEffectsFromCombat, $player);
  array_push($currentTurnEffectsFromCombat, $uniqueID);
  array_push($currentTurnEffectsFromCombat, CurrentTurnEffectUses($cardID));
}

function CopyCurrentTurnEffectsFromCombat()
{
  global $currentTurnEffects, $currentTurnEffectsFromCombat;
  for($i = 0; $i < count($currentTurnEffectsFromCombat); $i += CurrentTurnEffectPieces()) {
    array_push($currentTurnEffects, $currentTurnEffectsFromCombat[$i]);
    array_push($currentTurnEffects, $currentTurnEffectsFromCombat[$i+1]);
    array_push($currentTurnEffects, $currentTurnEffectsFromCombat[$i+2]);
    array_push($currentTurnEffects, $currentTurnEffectsFromCombat[$i+3]);
  }
  $currentTurnEffectsFromCombat = [];
}

function RemoveCurrentTurnEffect($index)
{
  global $currentTurnEffects;
  unset($currentTurnEffects[$index+3]);
  unset($currentTurnEffects[$index+2]);
  unset($currentTurnEffects[$index+1]);
  unset($currentTurnEffects[$index]);
  $currentTurnEffects = array_values($currentTurnEffects);
}

function CurrentTurnEffectPieces()
{
  return 4;
}

function CurrentTurnEffectUses($cardID)
{
  switch ($cardID) {
    case "EVR033": return 6;
    case "EVR034": return 5;
    case "EVR035": return 4;
    case "UPR000": return 3;
    case "UPR088": return 4;
    case "UPR221": return 4;
    case "UPR222": return 3;
    case "UPR223": return 2;
    default: return 1;
  }
}

function AddNextTurnEffect($cardID, $player, $uniqueID = -1)
{
  global $nextTurnEffects;
  array_push($nextTurnEffects, $cardID);
  array_push($nextTurnEffects, $player);
  array_push($nextTurnEffects, $uniqueID);
  array_push($nextTurnEffects, CurrentTurnEffectUses($cardID));
}

function IsCombatEffectLimited($index)
{
  global $currentTurnEffects, $combatChain, $mainPlayer, $combatChainState, $CCS_WeaponIndex, $CCS_AttackUniqueID;
  if(count($combatChain) == 0 || $currentTurnEffects[$index + 2] == -1) return false;
  $attackSubType = CardSubType($combatChain[0]);
  if(DelimStringContains($attackSubType, "Ally")) {
    $allies = &GetAllies($mainPlayer);
    if(count($allies) < $combatChainState[$CCS_WeaponIndex] + 5) return false;
    if($allies[$combatChainState[$CCS_WeaponIndex] + 5] != $currentTurnEffects[$index + 2]) return true;
  } else {
    return $combatChainState[$CCS_AttackUniqueID] != $currentTurnEffects[$index + 2];
  }
  return false;
}

function PrependLayer($cardID, $player, $parameter, $target = "-", $additionalCosts = "-", $uniqueID = "-")
{
    global $layers;
    array_push($layers, $cardID);
    array_push($layers, $player);
    array_push($layers, $parameter);
    array_push($layers, $target);
    array_push($layers, $additionalCosts);
    array_push($layers, $uniqueID);
    array_push($layers, GetUniqueId());
    return count($layers);//How far it is from the end
}

function AddLayer($cardID, $player, $parameter, $target = "-", $additionalCosts = "-", $uniqueID = "-")
{
  global $layers, $dqState;
  //Layers are on a stack, so you need to push things on in reverse order
  array_unshift($layers, GetUniqueId());
  array_unshift($layers, $uniqueID);
  array_unshift($layers, $additionalCosts);
  array_unshift($layers, $target);
  array_unshift($layers, $parameter);
  array_unshift($layers, $player);
  array_unshift($layers, $cardID);
  if($cardID == "TRIGGER")
  {
    $orderableIndex = intval($dqState[8]);
    if($orderableIndex == -1) $dqState[8] = 0;
    else $dqState[8] += LayerPieces();
  }
  else $dqState[8] = -1;//If it's not a trigger, it's not orderable
  return count($layers);//How far it is from the end
}

function AddDecisionQueue($phase, $player, $parameter, $subsequent = 0, $makeCheckpoint = 0)
{
  global $decisionQueue;
  if(count($decisionQueue) == 0) $insertIndex = 0;
  else {
    $insertIndex = count($decisionQueue) - DecisionQueuePieces();
    if(!IsGamePhase($decisionQueue[$insertIndex])) //Stack must be clear before you can continue with the step
    {
      $insertIndex = count($decisionQueue);
    }
  }

  $parameter = str_replace(" ", "_", $parameter);
  array_splice($decisionQueue, $insertIndex, 0, $phase);
  array_splice($decisionQueue, $insertIndex + 1, 0, $player);
  array_splice($decisionQueue, $insertIndex + 2, 0, $parameter);
  array_splice($decisionQueue, $insertIndex + 3, 0, $subsequent);
  array_splice($decisionQueue, $insertIndex + 4, 0, $makeCheckpoint);
}

function PrependDecisionQueue($phase, $player, $parameter, $subsequent = 0, $makeCheckpoint = 0)
{
  global $decisionQueue;
  $parameter = str_replace(" ", "_", $parameter);
  array_unshift($decisionQueue, $makeCheckpoint);
  array_unshift($decisionQueue, $subsequent);
  array_unshift($decisionQueue, $parameter);
  array_unshift($decisionQueue, $player);
  array_unshift($decisionQueue, $phase);
}

function IsDecisionQueueActive()
{
  global $dqState;
  return $dqState[0] == "1";
}

function ProcessDecisionQueue()
{
  global $turn, $decisionQueue, $dqState;
  if($dqState[0] != "1") {
    $count = count($turn);
    if(count($turn) < 3) $turn[2] = "-";
    $dqState[0] = "1"; //If the decision queue is currently active/processing
    $dqState[1] = $turn[0];
    $dqState[2] = $turn[1];
    $dqState[3] = $turn[2];
    $dqState[4] = "-"; //DQ helptext initial value
    $dqState[5] = "-"; //Decision queue multizone indices
    $dqState[6] = "0"; //Damage dealt
    $dqState[7] = "0"; //Target
  }
  ContinueDecisionQueue("");
}

function CloseDecisionQueue()
{
  global $turn, $decisionQueue, $dqState, $combatChain, $currentPlayer, $mainPlayer;
  $dqState[0] = "0";
  $turn[0] = $dqState[1];
  $turn[1] = $dqState[2];
  $turn[2] = $dqState[3];
  $dqState[4] = "-"; //Clear the context, just in case
  $dqState[5] = "-"; //Clear Decision queue multizone indices
  $dqState[6] = "0"; //Damage dealt
  $dqState[7] = "0"; //Target
  $dqState[8] = "-1"; //Orderable index (what layer after which triggers can be reordered)
  $decisionQueue = [];
  if(($turn[0] == "D" || $turn[0] == "A") && count($combatChain) == 0) {
    $currentPlayer = $mainPlayer;
    $turn[0] = "M";
  }
}

function ShouldHoldPriorityNow($player)
{
  global $layerPriority, $layers;
  if($layerPriority[$player - 1] != "1") return false;
  $currentLayer = $layers[count($layers) - LayerPieces()];
  $layerType = CardType($currentLayer);
  if(HoldPrioritySetting($player) == 3 && $layerType != "AA" && $layerType != "W") return false;
  return true;
}

function SkipHoldingPriorityNow($player)
{
  global $layerPriority;
  $layerPriority[$player - 1] = "0";
}

function IsGamePhase($phase)
{
  switch ($phase) {
    case "RESUMEPAYING":
    case "RESUMEPLAY":
    case "RESOLVECHAINLINK":
    case "RESOLVECOMBATDAMAGE":
    case "PASSTURN":
      return true;
    default: return false;
  }
}

//Must be called with the my/their context
function ContinueDecisionQueue($lastResult = "")
{
  global $decisionQueue, $turn, $currentPlayer, $mainPlayerGamestateStillBuilt, $makeCheckpoint, $otherPlayer;
  global $layers, $layerPriority, $dqVars, $dqState, $CS_AbilityIndex, $CS_AdditionalCosts, $mainPlayer, $CS_LayerPlayIndex;
  global $CS_ResolvingLayerUniqueID;
  if(count($decisionQueue) == 0 || IsGamePhase($decisionQueue[0])) {
    if($mainPlayerGamestateStillBuilt) UpdateMainPlayerGameState();
    else if(count($decisionQueue) > 0 && $currentPlayer != $decisionQueue[1]) {
      UpdateGameState($currentPlayer);
    }
    if(count($decisionQueue) == 0 && count($layers) > 0) {
      $priorityHeld = 0;
      if($mainPlayer == 1) {
        if(ShouldHoldPriorityNow(1)) {
          AddDecisionQueue("INSTANT", 1, "-");
          $priorityHeld = 1;
          $layerPriority[0] = 0;
        }
        if(ShouldHoldPriorityNow(2)) {
          AddDecisionQueue("INSTANT", 2, "-");
          $priorityHeld = 1;
          $layerPriority[1] = 0;
        }
      } else {
        if(ShouldHoldPriorityNow(2)) {
          AddDecisionQueue("INSTANT", 2, "-");
          $priorityHeld = 1;
          $layerPriority[1] = 0;
        }
        if(ShouldHoldPriorityNow(1)) {
          AddDecisionQueue("INSTANT", 1, "-");
          $priorityHeld = 1;
          $layerPriority[0] = 0;
        }
      }
      if($priorityHeld) {
        ContinueDecisionQueue("");
      } else {
        if(RequiresDieRoll($layers[0], explode("|", $layers[2])[0], $layers[1])) {
          RollDie($layers[1]);
          ContinueDecisionQueue("");
          return;
        }
        CloseDecisionQueue();
        $cardID = array_shift($layers);
        $player = array_shift($layers);
        $parameter = array_shift($layers);
        $target = array_shift($layers);
        $additionalCosts = array_shift($layers);
        $uniqueID = array_shift($layers);
        $layerUniqueID = array_shift($layers);
        SetClassState($player, $CS_ResolvingLayerUniqueID, $layerUniqueID);
        $params = explode("|", $parameter);
        if($currentPlayer != $player) {
          if($mainPlayerGamestateStillBuilt) UpdateMainPlayerGameState();
          else UpdateGameState($currentPlayer);
          $currentPlayer = $player;
          $otherPlayer = $currentPlayer == 1 ? 2 : 1;
          BuildMyGamestate($currentPlayer);
        }
        $layerPriority[0] = ShouldHoldPriority(1);
        $layerPriority[1] = ShouldHoldPriority(2);
        if($cardID == "ENDTURN") EndStep();
        else if($cardID == "ENDSTEP") FinishTurnPass();
        else if($cardID == "RESUMETURN") $turn[0] = "M";
        else if($cardID == "LAYER") ProcessLayer($player, $parameter);
        else if($cardID == "FINALIZECHAINLINK") FinalizeChainLink($parameter);
        else if($cardID == "DEFENDSTEP") { $turn[0] = "A"; $currentPlayer = $mainPlayer; }
        else if($cardID == "TRIGGER") {
          ProcessTrigger($player, $parameter, $uniqueID, $target);
          ProcessDecisionQueue();
        } else {
          SetClassState($player, $CS_AbilityIndex, $params[2]); //This is like a parameter to PlayCardEffect and other functions
          PlayCardEffect($cardID, $params[0], $params[1], $target, $additionalCosts, $params[3], $params[2]);
          ClearDieRoll($player);
        }
      }
    } else if(count($decisionQueue) > 0 && $decisionQueue[0] == "RESUMEPLAY") {
      if($currentPlayer != $decisionQueue[1]) {
        $currentPlayer = $decisionQueue[1];
        $otherPlayer = $currentPlayer == 1 ? 2 : 1;
        BuildMyGamestate($currentPlayer);
      }
      $params = explode("|", $decisionQueue[2]);
      CloseDecisionQueue();
      if($turn[0] == "B" && count($layers) == 0) //If a layer is not created
      {
        PlayCardEffect($params[0], $params[1], $params[2], "-", $params[3], $params[4]);
      } else {
        //params 3 = ability index
        //params 4 = Unique ID
        $additionalCosts = GetClassState($currentPlayer, $CS_AdditionalCosts);
        if($additionalCosts == "") $additionalCosts = "-";
        $layerIndex = count($layers) - GetClassState($currentPlayer, $CS_LayerPlayIndex);
        $layers[$layerIndex + 2] = $params[1] . "|" . $params[2] . "|" . $params[3] . "|" . $params[4];
        $layers[$layerIndex + 4] = $additionalCosts;
        ProcessDecisionQueue();
        return;
      }
    } else if(count($decisionQueue) > 0 && $decisionQueue[0] == "RESUMEPAYING") {
      $player = $decisionQueue[1];
      $params = explode("-", $decisionQueue[2]); //Parameter
      if($lastResult == "") $lastResult = 0;
      CloseDecisionQueue();
      if($currentPlayer != $player) {
        $currentPlayer = $player;
        $otherPlayer = $currentPlayer == 1 ? 2 : 1;
        BuildMyGamestate($currentPlayer);
      }
      PlayCard($params[0], $params[1], $lastResult, $params[2]);
    } else if(count($decisionQueue) > 0 && $decisionQueue[0] == "RESOLVECHAINLINK") {
      CloseDecisionQueue();
      ResolveChainLink();
    } else if(count($decisionQueue) > 0 && $decisionQueue[0] == "RESOLVECOMBATDAMAGE") {
      $parameter = $decisionQueue[2];
      if($parameter != "-") $damageDone = $parameter;
      else $damageDone = $dqState[6];
      CloseDecisionQueue();
      ResolveCombatDamage($damageDone);
    } else if(count($decisionQueue) > 0 && $decisionQueue[0] == "PASSTURN") {
      CloseDecisionQueue();
      PassTurn();
    } else {
      CloseDecisionQueue();
      FinalizeAction();
    }
    return;
  }
  $phase = array_shift($decisionQueue);
  $player = array_shift($decisionQueue);
  $parameter = array_shift($decisionQueue);
  //WriteLog($phase . " " . $player . " " . $parameter . " " . $lastResult);//Uncomment this to visualize decision queue execution
  $parameter = str_replace("{I}", $dqState[5], $parameter);
  if(count($dqVars) > 0) {
    if(str_contains($parameter, "{0}")) $parameter = str_replace("{0}", $dqVars[0], $parameter);
    if(str_contains($parameter, "<0>")) $parameter = str_replace("<0>", CardLink($dqVars[0], $dqVars[0]), $parameter);
    if(str_contains($parameter, "{1}")) $parameter = str_replace("{1}", $dqVars[1], $parameter);
  }
  if(count($dqVars) > 1) $parameter = str_replace("<1>", CardLink($dqVars[1], $dqVars[1]), $parameter);
  $subsequent = array_shift($decisionQueue);
  $makeCheckpoint = array_shift($decisionQueue);
  $turn[0] = $phase;
  $turn[1] = $player;
  $currentPlayer = $player;
  $turn[2] = ($parameter == "<-" ? $lastResult : $parameter);
  $return = "PASS";
  if($subsequent != 1 || is_array($lastResult) || strval($lastResult) != "PASS") $return = DecisionQueueStaticEffect($phase, $player, ($parameter == "<-" ? $lastResult : $parameter), $lastResult);
  if($parameter == "<-" && !is_array($lastResult) && $lastResult == "-1") $return = "PASS"; //Collapse the rest of the queue if this decision point has invalid parameters
  if(is_array($return) || strval($return) != "NOTSTATIC") {
    if($phase != "SETDQCONTEXT") $dqState[4] = "-"; //Clear out context for static states -- context only persists for one choice
    ContinueDecisionQueue($return);
  } else {
    if($mainPlayerGamestateStillBuilt) UpdateMainPlayerGameState();
  }
}

function ProcessLayer($player, $parameter)
{
  switch ($parameter) {
    case "PHANTASM":
      PhantasmLayer();
      break;
    default: break;
  }
}

function ProcessTrigger($player, $parameter, $uniqueID, $target="-")
{
  global $combatChain, $CS_NumNonAttackCards, $CS_ArcaneDamageDealt, $CS_NumRedPlayed, $CS_DamageTaken, $EffectContext;
  global $CID_BloodRotPox, $CID_Inertia, $CID_Frailty;
  $items = &GetItems($player);
  $character = &GetPlayerCharacter($player);
  $auras = &GetAuras($player);
  $parameter = ShiyanaCharacter($parameter);
  $EffectContext = $parameter;
  switch ($parameter) {
    case "HEAVE":
      Heave();
      break;
    case "WTR000":
      if(IHaveLessHealth()) GainHealth(1, $player);
      break;
    case "WTR001": case "WTR002": case "RVD001":
      WriteLog(CardLink($parameter, $parameter) . " Intimidates");
      Intimidate();
      break;
    case "WTR046":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "WTR047":
      Draw($player);
      WriteLog(CardLink($parameter, $parameter) . " drew a card");
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "WTR054": case "WTR055": case "WTR056":
      if($parameter == "WTR054") $amount = 3;
      else if($parameter == "WTR055") $amount = 2;
      else $amount = 1;
      BlessingOfDeliveranceDestroy($amount);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "WTR069": case "WTR070": case "WTR071":
      WriteLog(CardLink($parameter, $parameter) . " gives the next Guardian attack action card this turn +" . EffectAttackModifier($parameter));
      AddCurrentTurnEffect($parameter, $player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "WTR072": case "WTR073": case "WTR074":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "WTR075":
      AddCurrentTurnEffect($parameter, $player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "WTR076": case "WTR077":
      KatsuHit();
      break;
    case "WTR079":
      WriteLog(CardLink($parameter, $parameter) . " drew a card");
      Draw($player);
      break;
    case "WTR117":
      $index = FindCharacterIndex($player, $parameter);
      AddDecisionQueue("YESNO", $player, "if_you_want_to_destroy_Refraction_Bolters_to_get_Go_Again");
      AddDecisionQueue("NOPASS", $player, "-", 1);
      AddDecisionQueue("PASSPARAMETER", $player, "MYCHAR-$index", 1);
      AddDecisionQueue("MZDESTROY", $player, "-", 1);
      AddDecisionQueue("OP", $player, "GIVEATTACKGOAGAIN", 1);
      AddDecisionQueue("WRITELOG", $player, "Refraction Bolters was destroyed", 1);
      break;
    case "ARC000":
      Opt($parameter, 2);
      break;
    case "ARC007":
      $index = SearchItemsForUniqueID($uniqueID, $player);
      --$items[$index + 1];
      GainResources($player, 2);
      if($items[$index + 1] <= 0) DestroyItemForPlayer($player, $index);
      WriteLog(CardLink($parameter, $parameter) . " produced 2 resources");
      break;
    case "ARC035":
      $index = SearchItemsForUniqueID($uniqueID, $player);
      --$items[$index + 1];
      if($items[$index + 1] <= 0) DestroyItemForPlayer($player, $index);
      break;
    case "ARC075": case "ARC076":
      ViseraiPlayCard($target);
      break;
    case "ARC112":
      DealArcane(1, 1, "RUNECHANT", "ARC112", player:$player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "ARC152":
      $index = FindCharacterIndex($player, $parameter);
      AddDecisionQueue("YESNO", $player, "if_you_want_to_destroy_Vest_of_the_First_Fist_to_gain_2_resources");
      AddDecisionQueue("NOPASS", $player, "");
      AddDecisionQueue("PASSPARAMETER", $player, "MYCHAR-" . $index, 1);
      AddDecisionQueue("MZDESTROY", $player, "-", 1);
      AddDecisionQueue("GAINRESOURCES", $player, 2, 1);
      break;
    case "ARC162":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "CRU000":
      PlayAura("ARC112", $player);
      break;
    case "CRU007":
      AddDecisionQueue("SPECIFICCARD", $player, "BEASTWITHIN");
      break;
    case "CRU008":
      Intimidate();
      break;
    case "CRU028":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "CRU029": case "CRU030": case "CRU031":
      WriteLog(CardLink($parameter, $parameter) . " gives the next Guardian attack action card this turn +" . EffectAttackModifier($parameter));
      AddCurrentTurnEffect($parameter, $player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "CRU038": case "CRU039": case "CRU040":
      WriteLog(CardLink($parameter, $parameter) . " gives the next Guardian attack action card this turn +" . EffectAttackModifier($parameter) . " and dominate");
      AddCurrentTurnEffect($parameter, $player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "CRU051": case "CRU052":
      EvaluateCombatChain($totalAttack, $totalBlock);
      for($i = CombatChainPieces(); $i < count($combatChain); $i += CombatChainPieces()) {
        if($totalBlock > 0 && (intval(BlockValue($combatChain[$i])) + BlockModifier($combatChain[$i], "CC", 0) + $combatChain[$i + 6]) > $totalAttack) {
          DestroyCurrentWeapon();
        }
      }
      break;
    case "CRU053":
      $index = FindCharacterIndex($player, $parameter);
      AddDecisionQueue("YESNO", $player, "if_you_want_to_destroy_Breeze_Rider_Boots_to_give_your_Combo_attacks_Go_Again");
      AddDecisionQueue("NOPASS", $player, "-");
      AddDecisionQueue("PASSPARAMETER", $player, $index, 1);
      AddDecisionQueue("DESTROYCHARACTER", $player, "-", 1);
      AddDecisionQueue("ADDCURRENTEFFECT", $player, $character[$index], 1);
      break;
    case "CRU075":
      $index = SearchAurasForUniqueID($uniqueID, $player);
      if($auras[$index+2] == 0) {
        DestroyAuraUniqueID($player, $uniqueID);
      } else {
        --$auras[$index+2];
      }
      break;
    case "CRU097":
      AddDecisionQueue("MULTIZONEINDICES", $player, "MYCHAR:type=C&THEIRCHAR:type=C");
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose which hero to copy");
      AddDecisionQueue("CHOOSEMULTIZONE", $player, "<-", 1);
      AddDecisionQueue("MZOP", $player, "GETCARDID", 1);
      AddDecisionQueue("APPENDLASTRESULT", $player, "-SHIYANA", 1);
      AddDecisionQueue("ADDCURRENTANDNEXTTURNEFFECT", $player, "<-", 1);
      break;
    case "CRU099":
      PutItemIntoPlay($target);
      break;
    case "CRU142":
      if(GetClassState($player, $CS_NumNonAttackCards) > 0) PlayAura("ARC112", $player);
      if(GetClassState($player, $CS_ArcaneDamageDealt) > 0) PlayAura("ARC112", $player);
      break;
    case "CRU144":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "CRU161":
      AddDecisionQueue("YESNO", $player, "if_you_want_to_pay_1_to_give_+1_arcane_damage");
      AddDecisionQueue("NOPASS", $player, "-", 1, 1);
      AddDecisionQueue("PAYRESOURCES", $player, "1", 1);
      AddDecisionQueue("PASSPARAMETER", $player, "1", 1);
      AddDecisionQueue("BUFFARCANEPREVLAYER", $player, "CRU161", 1);
      AddDecisionQueue("CHARFLAGDESTROY", $player, FindCharacterIndex($player, "CRU161"), 1);
      break;
    case "MON089":
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose how much to pay for " . CardLink($parameter, $parameter));
      AddDecisionQueue("BUTTONINPUT", $player, "0,1");
      AddDecisionQueue("PAYRESOURCES", $player, "<-", 1);
      AddDecisionQueue("LESSTHANPASS", $player, "1", 1);
      AddDecisionQueue("PASSPARAMETER", $player, $target, 1);
      if(!SearchCurrentTurnEffects("MON089", $player)) AddDecisionQueue("ADDCURRENTEFFECT", $player, "MON089", 1);
      break;
    case "MON122":
      $index = FindCharacterIndex($player, $parameter);
      AddDecisionQueue("CHARREADYORPASS", $player, $index);
      AddDecisionQueue("YESNO", $player, "if_you_want_to_destroy_Hooves_of_the_Shadowbeast_to_gain_an_action_point", 1);
      AddDecisionQueue("NOPASS", $player, "-", 1);
      AddDecisionQueue("PASSPARAMETER", $player, $index, 1);
      AddDecisionQueue("DESTROYCHARACTER", $player, "-", 1);
      AddDecisionQueue("GAINACTIONPOINTS", $player, 1, 1);
      break;
    case "MON186":
      SoulShackleStartTurn($player);
      break;
    case "MON241": case "MON242": case "MON243":
    case "MON244": case "RVD005": case "RVD006":
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose how much to pay for " . CardLink($parameter, $parameter));
      AddDecisionQueue("BUTTONINPUT", $player, "0,1");
      AddDecisionQueue("PAYRESOURCES", $player, "<-", 1);
      AddDecisionQueue("LESSTHANPASS", $player, "1", 1);
      AddDecisionQueue("ADDCURRENTEFFECT", $player, $parameter, 1);
      AddDecisionQueue("PASSPARAMETER", $player, 2, 1);
      AddDecisionQueue("COMBATCHAINCHARACTERDEFENSEMODIFIER", $player, $target, 1);
      break;
    case "ELE025": case "ELE026": case "ELE027":
      WriteLog(CardLink($parameter, $parameter) . " gives the next attack action card this turn +" . EffectAttackModifier($parameter));
      AddCurrentTurnEffect($parameter, $player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "ELE028": case "ELE029": case "ELE030":
      WriteLog(CardLink($parameter, $parameter) . " gives the next attack action card this turn +" . EffectAttackModifier($parameter));
      AddCurrentTurnEffect($parameter, $player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "ELE062": case "ELE063":
      PlayAura("ELE110", $player);
      WriteLog(CardLink($parameter, $parameter) . " created an Embodiment of Lightning aura");
      break;
    case "ELE066":
      if(HasIncreasedAttack()) Draw($player);
      break;
    case "ELE004":
      for($i = 1; $i < count($combatChain); $i += CombatChainPieces()) {
        if($combatChain[$i] == $player) {
          PlayAura("ELE111", $player);
        }
      }
      break;
    case "ELE109":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "ELE111":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "ELE174":
      $otherPlayer = ($player == 1 ? 2 : 1);
      $index = FindCharacterIndex($player, $parameter);
      AddDecisionQueue("YESNO", $player, "destroy_mark_of_lightning_to_have_the_attack_deal_1_damage");
      AddDecisionQueue("NOPASS", $player, "-", 1);
      AddDecisionQueue("PASSPARAMETER", $player, $index, 1);
      AddDecisionQueue("DESTROYCHARACTER", $player, "-", 1);
      AddDecisionQueue("DEALDAMAGE", $otherPlayer, 1 . "-" . $combatChain[0] . "-" . "COMBAT", 1);
      break;
    case "ELE175":
      AddDecisionQueue("YESNO", $player, "do_you_want_to_pay_1_to_give_your_action_go_again", 0, 1);
      AddDecisionQueue("NOPASS", $player, "-", 1);
      AddDecisionQueue("PASSPARAMETER", $player, 1, 1);
      AddDecisionQueue("PAYRESOURCES", $player, "<-", 1);
      AddDecisionQueue("GIVEACTIONGOAGAIN", $player, $target, 1);
      break;
    case "ELE203":
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose how much to pay for " . CardLink($parameter, $parameter));
      AddDecisionQueue("BUTTONINPUT", $player, "0,1");
      AddDecisionQueue("PAYRESOURCES", $player, "<-", 1);
      AddDecisionQueue("LESSTHANPASS", $player, "1", 1);
      AddDecisionQueue("ADDCURRENTEFFECT", $player, "ELE203", 1);
      break;
    case "ELE206": case "ELE207": case "ELE208":
      WriteLog(CardLink($parameter, $parameter) . " gives the next Guardian attack action card this turn +" . EffectAttackModifier($parameter));
      AddCurrentTurnEffect($parameter, $player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "ELE215":
      WriteLog(CardLink("ELE215", "ELE215") . " discarded your hand and arsenal");
      DestroyArsenal($target);
      DiscardHand($target);
      break;
    case "EVR018":
      PlayAura("ELE111", $player);
      break;
    case "EVR037":
      $index = FindCharacterIndex($player, $parameter);
      AddDecisionQueue("YESNO", $player, "if_you_want_to_destroy_Mask_of_the_Pouncing_Lynx_to_tutor_a_card");
      AddDecisionQueue("NOPASS", $player, "-");
      AddDecisionQueue("PASSPARAMETER", $player, $index, 1);
      AddDecisionQueue("DESTROYCHARACTER", $player, "-", 1);
      AddDecisionQueue("FINDINDICES", $player, "MASKPOUNCINGLYNX", 1);
      AddDecisionQueue("MAYCHOOSEDECK", $player, "<-", 1);
      AddDecisionQueue("MULTIBANISH", $player, "DECK,TT", 1);
      AddDecisionQueue("SETDQVAR", $player, "0", 1);
      AddDecisionQueue("WRITELOG", $player, "<0> was banished", 1);
      AddDecisionQueue("SHUFFLEDECK", $player, "-", 1);
      break;
    case "EVR069":
      $index = SearchItemsForUniqueID($uniqueID, $player);
      --$items[$index+1];
      if($items[$index+1] < 0) DestroyItemForPlayer($player, $index);
      break;
    case "EVR071":
      $index = SearchItemsForUniqueID($uniqueID, $player);
      --$items[$index+1];
      if($items[$index+1] < 0) DestroyItemForPlayer($player, $index);
      break;
    case "EVR107": case "EVR108": case "EVR109":
      $index = SearchAurasForUniqueID($uniqueID, $player);
      if($index == -1) break;
      $auras = &GetAuras($player);
      if($auras[$index+2] == 0) DestroyAuraUniqueID($player, $uniqueID);
      else {
        --$auras[$index+2];
        PlayAura("ARC112", $player);
      }
      break;
    case "EVR120": case "UPR102": case "UPR103":
      $otherPlayer = ($player == 1 ? 2 : 1);
      PlayAura("ELE111", $otherPlayer);
      WriteLog(CardLink($parameter, $parameter) . " created a Frostbite for playing an ice card");
      break;
    case "EVR131": case "EVR132": case "EVR133":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "RVD015":
      $deck = &GetDeck($player);
      $rv = "";
      if(count($deck) == 0) $rv .= "Your deck is empty. No card is revealed";
      $wasRevealed = RevealCards($deck[0]);
      if($wasRevealed && AttackValue($deck[0]) < 6) {
          WriteLog("The card was put on the bottom of your deck");
          array_push($deck, array_shift($deck));
      }
      break;
    case "UPR095":
      if(GetClassState($player, $CS_DamageTaken) > 0) {
        $discard = &GetDiscard($player);
        $found = -1;
        for($i = 0; $i < count($discard) && $found == -1; $i += DiscardPieces()) {
          if($discard[$i] == "UPR101") $found = $i;
        }
        if($found == -1) WriteLog("No Phoenix Flames in discard");
        else {
          RemoveGraveyard($player, $found);
          AddPlayerHand("UPR101", $player, "GY");
        }
      }
      break;
    case "UPR096":
      if(GetClassState($player, $CS_NumRedPlayed) > 1 && CanRevealCards($player))
      {
        AddDecisionQueue("MULTIZONEINDICES", $player, "MYDECK:cardID=UPR101");
        AddDecisionQueue("MAYCHOOSEMULTIZONE", $player, "<-", 1);
        AddDecisionQueue("MZREMOVE", $player, "-", 1);
        AddDecisionQueue("ADDHAND", $player, "-", 1);
        AddDecisionQueue("SHUFFLEDECK", $player, "-", 1);
      }
      return "";
    case "UPR140":
      $index = SearchAurasForUniqueID($uniqueID, $player);
      if($index == -1) break;
      $auras = &GetAuras($player);
      --$auras[$index + 2];
      PayOrDiscard($target, 2, true);
      if($auras[$index + 2] == 0) {
        WriteLog(CardLink($auras[$index], $auras[$index]) . " is destroyed");
        DestroyAura($player, $index);
      }
      break;
    case "UPR141": case "UPR142": case "UPR143":
      if($parameter == "UPR141") $numFrostbite = 4;
      else if($parameter == "UPR142") $numFrostbite = 3;
      else $numFrostbite = 2;
      PlayAura("ELE111", $target, $numFrostbite);
      break;
    case "UPR182":
      BottomDeckMultizone($player, "MYHAND", "MYARS");
      AddDecisionQueue("DRAW", $player, "-", 1);
      break;
    case "UPR190":
      DestroyAuraUniqueID($player, $uniqueID);
      WriteLog(CardLink($parameter, $parameter) . " is destroyed");
      break;
    case "UPR191": case "UPR192": case "UPR193":
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose how much to pay for " . CardLink($parameter, $parameter));
      AddDecisionQueue("BUTTONINPUT", $player, "0,2");
      AddDecisionQueue("PAYRESOURCES", $player, "<-", 1);
      AddDecisionQueue("LESSTHANPASS", $player, "2", 1);
      AddDecisionQueue("PASSPARAMETER", $player, $target, 1);
      AddDecisionQueue("COMBATCHAINPOWERMODIFIER", $player, "2", 1);
      break;
    case "UPR194": case "UPR195": case "UPR196":
      if(PlayerHasLessHealth($player)) GainHealth(1, $player);
      break;
    case "UPR203": case "UPR204": case "UPR205":
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose how much to pay for " . CardLink($parameter, $parameter));
      AddDecisionQueue("BUTTONINPUT", $player, "0,1");
      AddDecisionQueue("PAYRESOURCES", $player, "<-", 1);
      AddDecisionQueue("LESSTHANPASS", $player, "1", 1);
      AddDecisionQueue("PASSPARAMETER", $player, $target, 1);
      AddDecisionQueue("COMBATCHAINDEFENSEMODIFIER", $player, "2", 1);
      break;
    case "UPR218": case "UPR219": case "UPR220":
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "DYN006":
      $index = FindCharacterIndex($player, $parameter);
      AddDecisionQueue("CHARREADYORPASS", $player, $index);
      AddDecisionQueue("YESNO", $player, "if_you_want_to_destroy_Beaten_Trackers_to_gain_an_action_point", 1);
      AddDecisionQueue("NOPASS", $player, "-", 1);
      AddDecisionQueue("PASSPARAMETER", $player, $index, 1);
      AddDecisionQueue("DESTROYCHARACTER", $player, "-", 1);
      AddDecisionQueue("GAINACTIONPOINTS", $player, 1, 1);
      AddDecisionQueue("WRITELOG", $player, "Player_" . $player . "_gained_an_action_point_from_" . CardLink("DYN006", "DYN006"), 1);
      break;
    case "DYN008":
      GainResources($player, 1);
      break;
    case "DYN009":
      $deck = &GetDeck($player);
      $rv = "";
      if(count($deck) == 0) $rv .= "Your deck is empty. No card is revealed.";
      $wasRevealed = RevealCards($deck[0]);
      if($wasRevealed && AttackValue($deck[0]) >= 6) {
        Draw($player);
        WriteLog(CardLink($parameter, $parameter) . " drew a card");
      }
      break;
		case "DYN010": case "DYN011": case "DYN012":
      $discard = &GetDiscard($player);
      $found = -1;
      for($i = 0; $i < count($discard) && $found == -1; $i += DiscardPieces()) {
        if($discard[$i] == $parameter) $found = $i;
      }
      RemoveGraveyard($player, $found);
      AddBottomDeck($parameter, $player, "GY");
      WriteLog(CardLink($parameter, $parameter) . " was put at the bottom of the deck");
      break;
    case "DYN093":
      $targetIndex = SearchItemsForUniqueID($target, $player);
      AddDecisionQueue("YESNO", $player, "if_you_want_to_move_a_steam_counter_to_" . CardLink($items[$targetIndex], $items[$targetIndex]));
      AddDecisionQueue("NOPASS", $player, "-");
      AddDecisionQueue("PASSPARAMETER", $player, $uniqueID . "," . $target, 1);
      AddDecisionQueue("SPECIFICCARD", $player, "PLASMAMAINLINE", 1);
      break;
    case "DYN094":
      $otherPlayer = ($player == 1 ? 2 : 1);
      $index = GetItemIndex($parameter, $player);
      AddDecisionQueue("YESNO", $player, "Do_you_want_to_destroy_" . CardLink($parameter, $parameter) . "_and_a_defending_equipment?");
      AddDecisionQueue("NOPASS", $player, "-");
      AddDecisionQueue("PASSPARAMETER", $player, "MYITEMS-$index", 1);
      AddDecisionQueue("MZDESTROY", $player, "-", 1);
      AddDecisionQueue("FINDINDICES", $otherPlayer, "EQUIPONCC", 1);
      AddDecisionQueue("CHOOSETHEIRCHARACTER", $player, "<-", 1);
      AddDecisionQueue("DESTROYCHARACTER", $otherPlayer, "-", 1);
      break;
		case "DYN101": case "DYN102": case "DYN103":
      AddDecisionQueue("MULTIZONEINDICES", $player, "MYITEMS:cardID=ARC036;cardID=DYN111;cardID=DYN112");
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose a Hyper Driver to get a steam counter", 1);
      AddDecisionQueue("CHOOSEMULTIZONE", $player, "<-", 1);
      AddDecisionQueue("MZADDSTEAMCOUNTER", $player, "-", 1);
      break;
    case "ARC036": case "DYN110":
    case "DYN111": case "DYN112":
      $index = SearchItemsForUniqueID($uniqueID, $player);
      if($items[$index+2] == 2) {
        --$items[$index+1];
        $items[$index+2] = 1;
        GainResources($player, 1);
        if($items[$index+1] <= 0) DestroyItemForPlayer($player, $index);
      }
      break;
    case "DYN113": case "DYN114":
        $otherPlayer = ($player == 1 ? 2 : 1);
        AddDecisionQueue("DECKCARDS", $otherPlayer, "0", 1);
        AddDecisionQueue("SETDQVAR", $player, "0", 1);
        AddDecisionQueue("SETDQCONTEXT", $player, "Choose if you want to sink <0>" , 1);
        AddDecisionQueue("YESNO", $player, "if_you_want_to_sink_the_opponent's_card", 1);
        AddDecisionQueue("NOPASS", $player, $parameter, 1);
        AddDecisionQueue("WRITELOG", $player, "Arakni sunk the top card", 1);
        AddDecisionQueue("FINDINDICES", $otherPlayer, "TOPDECK", 1);
        AddDecisionQueue("MULTIREMOVEDECK", $otherPlayer, "<-", 1);
        AddDecisionQueue("ADDBOTDECK", $otherPlayer, "-", 1);
        AddDecisionQueue("ELSE", $player, "-");
        AddDecisionQueue("WRITELOG", $player, "Arakni left the top card there", 1);
      break;
    case "DYN152":
      $otherPlayer = ($player == 1 ? 2 : 1);
      $deck = &GetDeck($player);
      if(count($deck) == 0) WriteLog("Your deck is empty. No card is revealed.");
      $wasRevealed = RevealCards($deck[0]);
      if($wasRevealed) {
        if(CardSubType($deck[0]) == "Arrow") {
          if(IsAllyAttacking()) {
            $allyIndex = "THEIRALLY-" . GetAllyIndex($combatChain[0], $otherPlayer);
            AddDecisionQueue("PASSPARAMETER", $player, $allyIndex, 1);
          }
          else AddDecisionQueue("MULTIZONEINDICES", $player, "THEIRCHAR:type=C", 1);
          AddDecisionQueue("SETDQCONTEXT", $player, "Choose a target to deal 1 damage");
          AddDecisionQueue("CHOOSEMULTIZONE", $player, "<-", 1);
          AddDecisionQueue("MZDAMAGE", $player, "1,DAMAGE," . $parameter, 1);
          WriteLog(CardLink($parameter, $parameter) . " deals 1 damage");
        }
        else {
          WriteLog("The card was put on the bottom of your deck");
          array_push($deck, array_shift($deck));
        }
      }
      break;
    case "DYN153":
      $deck = &GetDeck($player);
      if(count($deck) == 0) break;
      if(!ArsenalFull($player)) {
        $card = array_shift($deck);
        AddArsenal($card, $player, "DECK", "UP");
      }
      break;
    case "DYN214":
      PlayAura("MON104", $player);
      break;
    case "DYN217":
      Draw($player);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "DYN244":
      Draw($player, false);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case "OUT000":
      $rand = GetRandom(1, 3);
      $otherPlayer = ($player == 1 ? 2 : 1);
      switch($rand) {
        case 1: $auraCreated = "OUT236"; break;
        case 2: $auraCreated = "OUT235"; break;
        case 3: $auraCreated = "OUT234"; break;
        default: break;
      }
      WriteLog("Plague Hive created a " . CardLink($auraCreated, $auraCreated));
      PlayAura($auraCreated, $otherPlayer);
      break;
    case "OUT091": case "OUT092":
      SuperReload();
      break;
    case "OUT097":
      $arsenal = &GetArsenal($player);
      AddDecisionQueue("YESNO", $player, "if you want to pay 1 to put an aim counter on the arrow");
      AddDecisionQueue("NOPASS", $player, "-");
      AddDecisionQueue("PAYRESOURCES", $player, "1", 1);
      AddDecisionQueue("PASSPARAMETER", $player, count($arsenal)-ArsenalPieces(), 1);
      AddDecisionQueue("ADDAIMCOUNTER", $player, "-", 1);
      break;
    case "OUT099":
      LookAtTopCard($player, "OUT099");
      break;
    case "OUT174":
      AddDecisionQueue("SETDQCONTEXT", $player, "Choose how much to pay for " . CardLink($parameter, $parameter));
      AddDecisionQueue("BUTTONINPUT", $player, "0,1");
      AddDecisionQueue("PAYRESOURCES", $player, "<-", 1);
      AddDecisionQueue("LESSTHANPASS", $player, "1", 1);
      AddDecisionQueue("ADDCURRENTEFFECT", $player, $parameter . "-BB", 1);
      AddDecisionQueue("PASSPARAMETER", $player, 1, 1);
      AddDecisionQueue("COMBATCHAINCHARACTERDEFENSEMODIFIER", $player, $target, 1);
      break;
    case $CID_BloodRotPox:
      AddDecisionQueue("YESNO", $player, "if_you_want_to_pay_3_to_avoid_taking_2_damage", 0, 1);
      AddDecisionQueue("NOPASS", $player, "-", 1);
      AddDecisionQueue("PASSPARAMETER", $player, "3", 1);
      AddDecisionQueue("PAYRESOURCES", $player, "3", 1);
      AddDecisionQueue("ELSE", $player, "-");
      AddDecisionQueue("WRITELOG", $player, "Took 2 damage from Bloodrot Pox.", 1);
      AddDecisionQueue("TAKEDAMAGE", $player, 2, 1);
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case $CID_Inertia:
      $deck = &GetDeck($player);
      $arsenal = &GetArsenal($player);
      while(count($arsenal) > 0) {
        array_push($deck, $arsenal[0]);
        RemoveArsenal($player, 0);
      }
      $hand = &GetHand($player);
      while(count($hand) > 0) {
        array_push($deck, $hand[0]);
        RemoveHand($player, 0);
      }
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    case $CID_Frailty:
      DestroyAuraUniqueID($player, $uniqueID);
      break;
    default:
      break;
  }
}

function GetDQHelpText()
{
  global $dqState;
  if(count($dqState) < 5) return "-";
  return $dqState[4];
}

function FinalizeAction()
{
  global $currentPlayer, $mainPlayer, $actionPoints, $turn, $combatChain, $defPlayer, $makeBlockBackup, $mainPlayerGamestateStillBuilt;
  if(!$mainPlayerGamestateStillBuilt) UpdateGameState(1);
  BuildMainPlayerGamestate();
  if($turn[0] == "M") {
    if(count($combatChain) > 0) //Means we initiated a chain link
    {
      $turn[0] = "B";
      $currentPlayer = $defPlayer;
      $turn[2] = "";
      $makeBlockBackup = 1;
    } else {
      if($actionPoints > 0 || ShouldHoldPriority($mainPlayer)) {
        $turn[0] = "M";
        $currentPlayer = $mainPlayer;
        $turn[2] = "";
      } else {
        $currentPlayer = $mainPlayer;
        BeginTurnPass();
      }
    }
  } else if($turn[0] == "A") {
    $currentPlayer = $mainPlayer;
    $turn[2] = "";
  } else if($turn[0] == "D") {
    $turn[0] = "A";
    $currentPlayer = $mainPlayer;
    $turn[2] = "";
  } else if($turn[0] == "B") {
    $turn[0] = "B";
  }
  return 0;
}

function IsReactionPhase()
{
  global $turn, $dqState;
  if($turn[0] == "A" || $turn[0] == "D") return true;
  if(count($dqState) >= 2 && ($dqState[1] == "A" || $dqState[1] == "D")) return true;
  return false;
}

//Return whether priority should be held for the player by default/settings
function ShouldHoldPriority($player, $layerCard = "")
{
  global $mainPlayer;
  $prioritySetting = HoldPrioritySetting($player);
  if($prioritySetting == 0 || $prioritySetting == 1) return 1;
  if(($prioritySetting == 2 || $prioritySetting == 3) && $player != $mainPlayer) return 1;
  return 0;
}

function GiveAttackGoAgain()
{
  global $combatChainState, $CCS_CurrentAttackGainedGoAgain;
  $combatChainState[$CCS_CurrentAttackGainedGoAgain] = 1;
}

function TopDeckToArsenal($player)
{
  $deck = &GetDeck($player);
  if(ArsenalFull($player) || count($deck) == 0) return;
  AddArsenal(array_shift($deck), $player, "DECK", "DOWN");
  WriteLog("The top card of player " . $player . "'s deck was put in their arsenal");
}

function DiscardHand($player)
{
  $hand = &GetHand($player);
  for($i = count($hand)-HandPieces(); $i>=0; $i-=HandPieces()) {
    DiscardCard($player, $i);
  }
}

function Opt($cardID, $amount)
{
  global $currentPlayer;
  PlayerOpt($currentPlayer, $amount);
}

function PlayerOpt($player, $amount, $optKeyword = true)
{
  AddDecisionQueue("FINDINDICES", $player, "DECKTOPXREMOVE," . $amount);
  AddDecisionQueue("OPT", $player, "<-", 1);
}

function DiscardRandom($player = "", $source = "")
{
  global $currentPlayer;
  if($player == "") $player = $currentPlayer;
  $hand = &GetHand($player);
  if(count($hand) == 0) return "";
  $index = GetRandom() % count($hand);
  $discarded = $hand[$index];
  unset($hand[$index]);
  $hand = array_values($hand);
  AddGraveyard($discarded, $player, "HAND");
  WriteLog(CardLink($discarded, $discarded) . " was randomly discarded");
  CardDiscarded($player, $discarded, $source);
  DiscardedAtRandomEffects($player, $discarded, $source);
  return $discarded;
}

function DiscardedAtRandomEffects($player, $discarded, $source) {
  switch($discarded) {
    case "DYN008": AddLayer("TRIGGER", $player, $discarded); break;
    case "DYN010": case "DYN011": case "DYN012": AddLayer("TRIGGER", $player, $discarded); break;
    default: break;
  }
}

function DiscardCard($player, $index)
{
  $hand = &GetHand($player);
  $discarded = RemoveHand($player, $index);
  AddGraveyard($discarded, $player, "HAND");
  CardDiscarded($player, $discarded, $source);
  return $discarded;
}

function CardDiscarded($player, $discarded, $source = "")
{
  global $CS_Num6PowDisc, $mainPlayer;
  AddEvent("DISCARD", $discarded);
  if(AttackValue($discarded) >= 6) {
    $character = &GetPlayerCharacter($player);
    $characterID = ShiyanaCharacter($character[0]);
    if(($characterID == "WTR001" || $characterID == "WTR002" || $characterID == "RVD001") && $character[1] == 2 && $player == $mainPlayer) { //Rhinar
      AddLayer("TRIGGER", $mainPlayer, $character[0]);
    }
    $index = FindCharacterIndex($player, "DYN006");
    if($index >= 0 && IsEquipUsable($player, $index) && IsCharacterActive($player, $index) && $player == $mainPlayer) {
      AddLayer("TRIGGER", $player, $character[$index]);
    }
    if(SearchCurrentTurnEffects("DYN009", $player)) {
      $discard = &GetDiscard($player);
      $found = -1;
      for($i = 0; $i < count($discard) && $found == -1; $i += DiscardPieces()) {
        if($discard[$i] == $discarded) $found = $i;
      }
      RemoveGraveyard($player, $found);
      BanishCardForPlayer($discarded, $player, "GY", "-", $player);
      AddLayer("TRIGGER", $player, "DYN009");
    }
    IncrementClassState($player, $CS_Num6PowDisc);
  }
  if($discarded == "CRU008" && $source != "" && ClassContains($source, "BRUTE", $mainPlayer) && CardType($source) == "AA") {
    WriteLog(CardLink("CRU008", "CRU008") . " intimidated because it was discarded by a Brute attack action card.");
    AddLayer("TRIGGER", $mainPlayer, $discarded);
  }
}

function Intimidate()
{
  global $defPlayer;
  $hand = &GetHand($defPlayer);
  if(count($hand) == 0) {
    WriteLog("Intimidate did nothing because there are no cards in hand");
    return;
  }
  $index = GetRandom() % count($hand);
  BanishCardForPlayer($hand[$index], $defPlayer, "HAND", "INT");
  RemoveHand($defPlayer, $index);
  WriteLog("Intimidate banished a card");
}

function DestroyFrozenArsenal($player)
{
  $arsenal = &GetArsenal($player);
  for($i = 0; $i < count($arsenal); $i += ArsenalPieces()) {
    if($arsenal[$i + 4] == "1") {
      DestroyArsenal($player);
    }
  }
}

function CanGainAttack()
{
  global $combatChain, $mainPlayer;
  if(SearchCurrentTurnEffects("OUT102", $mainPlayer)) return false;
  return !SearchCurrentTurnEffects("CRU035", $mainPlayer) || CardType($combatChain[0]) != "AA";
}

function IsWeaponGreaterThanTwiceBasePower()
{
  global $combatChainState, $CCS_CachedTotalAttack, $combatChain;
  return count($combatChain) > 0 && CardType($combatChain[0]) == "W" && CachedTotalAttack() > (AttackValue($combatChain[0]) * 2);
}

function HasEnergyCounters($array, $index)
{
  switch($array[$index]) {
    case "WTR150": case "UPR166": return $array[$index+2] > 0;
    default: return false;
  }
}
