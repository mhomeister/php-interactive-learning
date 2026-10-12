<?php
// Lektion 2: Variablen
// Eine Variable ist eine Box mit Namen. Sie beginnt immer mit $
// In so eine Box kannst du alles hineinlegen:

$name = "Lars";      // string = Text (immer in "Anführungszeichen")
$alter = 16;         // int    = ganze Zahl (ohne Anführungszeichen)
$hatHunger = true;   // bool   = ja oder nein (true oder false)

echo "Name: $name<br>";
echo "Alter: $alter<br><br>";

// var_dump() zeigt dir, was in der Box ist UND welche Art es ist:
var_dump($name);
echo "<br>";
var_dump($alter);
echo "<br>";
var_dump($hatHunger);
echo "<br><br>";

// Den Inhalt einer Box kann man jederzeit austauschen:
$name = "Superheld";
echo "Neuer Name: $name";
