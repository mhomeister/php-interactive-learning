<?php
// Lektion 10: Würfel-Duell! 🎲
// Hier kommt alles zusammen: Variablen, Rechnen, if und Funktionen.

function wuerfle()
{
    return rand(1, 6);
}

$lars = wuerfle() + wuerfle();
$computer = wuerfle() + wuerfle();

echo "<h2>🎲 Würfel-Duell</h2>";
echo "Lars würfelt: $lars<br>";
echo "Computer würfelt: $computer<br><br>";

if ($lars > $computer) {
    echo "<strong>Lars gewinnt! 🏆</strong>";
} elseif ($lars < $computer) {
    echo "<strong>Der Computer gewinnt! 🤖</strong>";
} else {
    echo "<strong>Unentschieden! 🤝</strong>";
}
