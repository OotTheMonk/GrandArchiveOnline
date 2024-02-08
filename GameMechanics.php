<?php

function PlayerInfluence($player) {
  $hand = &GetHand($player);
  $memory = &GetMemory($player);
  return count($hand)/HandPieces() + count($memory)/MemoryPieces();
}

?>