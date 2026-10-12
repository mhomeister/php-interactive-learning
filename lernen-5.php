<?php
// Lektion 5: Vergleichen
// =  heißt: speichern (etwas in die Box legen)
// == heißt: vergleichen (ist das gleich?)

$alter = 16;   // EIN = : die 16 kommt in die Box $alter

// Ein Vergleich ist eine Frage. Die Antwort ist immer
// true (ja) oder false (nein), also ein bool!

var_dump($alter == 16);   // ist gleich?
echo "<br>";
var_dump($alter > 18);    // größer als?
echo "<br>";
var_dump($alter < 18);    // kleiner als?
echo "<br>";
var_dump($alter >= 16);   // größer oder gleich?
echo "<br>";
var_dump($alter <= 10);   // kleiner oder gleich?
