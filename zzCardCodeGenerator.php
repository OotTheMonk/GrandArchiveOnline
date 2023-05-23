<?php

  include './zzImageConverter.php';
  include './Libraries/Trie.php';

  $hasMoreData = true;
  $page = 1;
  $typeTrie = [];
  $classTrie = [];
  $nameTrie = [];
  $elementTrie = [];
  $memoryCostTrie = [];
  $reserveCostTrie = [];
  $levelTrie = [];
  $powerTrie = [];
  $lifeTrie = [];
  $durabilityTrie = [];
  $speedTrie = [];
  $floatingMemoryTrie = [];
  $interceptTrie = [];
  while($hasMoreData)
  {
    $jsonUrl = "https://api.gatcg.com/cards/search?name=&page=" . $page;
    $curl = curl_init();
    $headers = array(
      "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_URL, $jsonUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $cardData = curl_exec($curl);
    curl_close($curl);


    $response = json_decode($cardData);


    for($i=0; $i<count($response->data); ++$i)
    {
      $card = $response->data[$i];
      AddToTrie($typeTrie, $card->uuid, 0, implode(",", $card->types));
      AddToTrie($classTrie, $card->uuid, 0, implode(",", $card->classes));

      AddToTrie($elementTrie, $card->uuid, 0, $card->element);
      AddToTrie($nameTrie, $card->uuid, 0, $card->name);
      AddToTrie($memoryCostTrie, $card->uuid, 0, ($card->cost_memory == null ? -1 : $card->cost_memory));
      AddToTrie($reserveCostTrie, $card->uuid, 0, ($card->cost_reserve == null ? -1 : $card->cost_reserve));
      AddToTrie($levelTrie, $card->uuid, 0, ($card->level == null ? -1 : $card->level));
      AddToTrie($powerTrie, $card->uuid, 0, ($card->power == null ? -1 : $card->power));
      AddToTrie($lifeTrie, $card->uuid, 0, ($card->life == null ? -1 : $card->life));
      AddToTrie($durabilityTrie, $card->uuid, 0, ($card->durability == null ? -1 : $card->durability));
      AddToTrie($speedTrie, $card->uuid, 0, ($card->speed == null ? -1 : $card->speed));
      if(str_contains($card->effect, "Floating Memory")) AddToTrie($floatingMemoryTrie, $card->uuid, 0, $card->name);

      CheckImage($card->uuid);
      echo($card->name . " " . $card->element . " " . $card->uuid . " " . $card->speed . "<BR>");
    }

    echo("Page: " . $response->page . "<BR>");
    ++$page;
    $hasMoreData = $response->has_more;
  }
/*
subtypes - array
*/


  if(!is_dir("./GeneratedCode")) mkdir("./GeneratedCode", 777, true);

  $filename = "./GeneratedCode/GeneratedCardDictionaries.php";
  $handler = fopen($filename, "w");

  fwrite($handler, "<?php\r\n");

  GenerateFunction($typeTrie, $handler, "CardTypes", true, "");
  GenerateFunction($classTrie, $handler, "CardClasses", true, "");
  GenerateFunction($elementTrie, $handler, "CardElement", true, "");
  GenerateFunction($nameTrie, $handler, "CardName", true, "");
  GenerateFunction($memoryCostTrie, $handler, "CardMemoryCost", false, -1);
  GenerateFunction($reserveCostTrie, $handler, "CardReserveCost", false, -1);
  GenerateFunction($levelTrie, $handler, "CardLevel", false, -1);
  GenerateFunction($powerTrie, $handler, "CardPower", false, -1);
  GenerateFunction($lifeTrie, $handler, "CardLife", false, -1);
  GenerateFunction($durabilityTrie, $handler, "CardDurability", false, -1);
  GenerateFunction($speedTrie, $handler, "CardSpeed", false, -1);
  GenerateFunction($floatingMemoryTrie, $handler, "HasFloatingMemory", false, false, 1);

  fwrite($handler, "?>");

  fclose($handler);

  function GenerateFunction($cardArray, $handler, $functionName, $isString, $defaultValue, $dataType=0)
  {
    fwrite($handler, "function " . $functionName . "(\$cardID) {\r\n");
    TraverseTrie($cardArray, "", $handler, $isString, $defaultValue, $dataType);
    fwrite($handler, "}\r\n\r\n");
  }
/*
  function GenerateFunction(&$cardArray, $handler, $functionName, $propertyName, $defaultValue="", $sparse=false)
  {
    echo("<BR>" . $functionName . "<BR>");
    fwrite($handler, "function Generated" . $functionName . "(\$cardID) {\r\n");
    $originalSets = ["WTR", "ARC", "CRU", "MON", "ELE", "EVR", "UPR", "DYN", "OUT", "DVR", "RVD"];
    $isString = true;
    if($propertyName == "attack" || $propertyName == "block" || $propertyName == "pitch" || $propertyName == "cost" || $propertyName == "health") $isString = false;
    fwrite($handler, "if(strlen(\$cardID) < 6) return " . ($isString ? "\"\"" : "0") . ";\r\n");
    fwrite($handler, "if(is_int(\$cardID)) return " . ($isString ? "\"\"" : "0") . ";\r\n");
    if($sparse) fwrite($handler, "switch(\$cardID) {\r\n");
    $trie = [];
    $cardsSeen = [];
    for($i=0; $i<count($cardArray); ++$i)
    {
      $cardRarity = "NA";
      $cardPrintings = [];
      if($cardArray[$i]->name == "Nitro Mechanoid") continue;//This is due to the data set not yet differentiating faces
      for($j=0; $j<count($cardArray[$i]->printings); ++$j)
      {
        $cardRarity = $cardArray[$i]->printings[$j]->rarity;
        $cardID = $cardArray[$i]->printings[$j]->id;
        $set = substr($cardID, 0, 3);
        if(!in_array($set, $originalSets)) continue;
        if(($set == "DVR" || $set == "RVD"))
        {
          $found = false;
          for($k=$j+1; $k<count($cardArray[$i]->printings); ++$k)
          {
            $cardID2 = $cardArray[$i]->printings[$k]->id;
            $set2 = substr($cardID2, 0, 3);
            if($set2 == "RVD" || $set2 == "DVR") continue;
            if(in_array($set, $originalSets)) { $found = true; break; }
          }
          if($found) continue;
        }
        $duplicate = false;
        for($k=0; $k<count($cardPrintings); ++$k)
        {
          if($cardPrintings[$k] == $cardID) $duplicate = true;
        }
        for($k=0; $k<count($cardsSeen); ++$k)
        {
          if($cardsSeen[$k] == $cardID) $duplicate = true;
        }
        if($duplicate) continue;
        array_push($cardPrintings, $cardID);
        array_push($cardsSeen, $cardID);
        if($propertyName == "type") $data = MapType($cardArray[$i]);
        else if($propertyName == "attack") $data = $cardArray[$i]->power;
        else if($propertyName == "block")
        {
          $data = $cardArray[$i]->defense;
          if($data == "") $data = -1;
        }
        else if($propertyName == "name") $data = $cardArray[$i]->name;
        else if($propertyName == "pitch")
        {
          $data = $cardArray[$i]->pitch;
          if($data == "") $data = 0;
        }
        else if($propertyName == "cost")
        {
          $data = $cardArray[$i]->cost;
          if($data == "") $data = -1;
        }
        else if($propertyName == "health")
        {
          $data = $cardArray[$i]->health;
          CheckImage($cardID);
        }
        else if($propertyName == "rarity")
        {
          $data = $cardRarity;
        }
        else if($propertyName == "rarity")
        {
          $data = $cardRarity;
        }
        else if($propertyName == "subtype")
        {
          $data = "";
          for($k=0; $k<count($cardArray[$i]->types); ++$k)
          {
            $type = $cardArray[$i]->types[$k];
            if(!IsCardType($type) && !IsClass($type) && !IsTalent($type) && !IsHandedness($type))
            {
              if($data != "") $data .= ",";
              $data .= $type;
            }
          }
        }
        if(($isString == false && !is_numeric($data) && $data != "") || $data == "-" || $data == "*" || $data == "X") echo("Exception with property name " . $propertyName . " data " . $data . " card " . $cardID . "<BR>");
        if($data != "-" && $data != "" && $data != "*" && $data != $defaultValue)
        {
          if($sparse) fwrite($handler, "case \"" . $cardID . "\": return " . ($isString ? "\"$data\"" : $data) . ";\r\n");
          else AddToTrie($trie, $cardID, 0, $data);
        }
      }
    }
    if($sparse) fwrite($handler, "default: return " . ($isString ? "\"$defaultValue\"" : $defaultValue) . ";}\r\n");
    else TraverseTrie($trie, "", $handler, $isString, $defaultValue);

    fwrite($handler, "}\r\n\r\n");
  }
  */

  function MapType($card)
  {
    $hasAction = false; $hasAttack = false;
    for($i=0; $i<count($card->types); ++$i)
    {
      if($card->types[$i] == "Action") $hasAction = true;
      else if($card->types[$i] == "Attack") $hasAttack = true;
      else if($card->types[$i] == "Defense") $hasDefense = true;
      else if($card->types[$i] == "Defense Reaction") return "DR";
      else if($card->types[$i] == "Attack Reaction") return "AR";
      else if($card->types[$i] == "Instant") return "I";
      else if($card->types[$i] == "Weapon") return "W";
      else if($card->types[$i] == "Hero") return "C";
      else if($card->types[$i] == "Equipment") return "E";
      else if($card->types[$i] == "Token") return "T";
      else if($card->types[$i] == "Resource") return "R";
      else if($card->types[$i] == "Mentor") return "M";
    }
    if($hasAction && $hasAttack) return "AA";
    else if($hasAction) return "A";
    else
    {
      echo("No type found for " . $card->name);
    }
    return "-";
  }

  function IsCardType($term)
  {
    switch($term)
    {
      case "Action": case "Attack": case "Defense Reaction": case "Attack Reaction":
      case "Instant": case "Weapon": case "Hero": case "Equipment": case "Token":
      case "Resource": case "Mentor": return true;
      default: return false;
    }
  }

  function IsClass($term)
  {
    switch($term)
    {
      case "Generic": case "Warrior": case "Ninja": case "Brute": case "Guardian":
      case "Wizard": case "Mechanologist": case "Ranger": case "Runeblade":
      case "Illusionist": case "Assassin": return true;
      case "Shapeshifter": case "Merchant": case "Arbiter": return true;
      default: return false;
    }
  }

  function IsTalent($term)
  {
    switch($term)
    {
      case "Elemental": case "Light": case "Shadow": case "Draconic": return true;
      case "Ice": case "Lightning": case "Earth": return true;
      default: return false;
    }
  }

  function IsHandedness($term)
  {
    switch($term)
    {
      case "1H": case "2H": return true;
      default: return false;
    }
  }


?>
