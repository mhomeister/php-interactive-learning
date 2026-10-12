<?php
// Lektion 6: Würfeln und Zwischenspeichern
// rand(1, 6) gibt eine Zufallszahl von 1 bis 6, wie ein Würfel.

// Wir würfeln nur EINMAL und speichern das Ergebnis in der Box $wurf.
$wurf = rand(1, 6);

// Ab jetzt können wir $wurf so oft benutzen, wie wir wollen.
// Es ist immer dieselbe Zahl, bis wir etwas Neues hineinlegen.
echo "Du hast eine $wurf gewürfelt! 🎲<br>";

$doppelt = $wurf * 2;
echo "Doppelt so viel wäre $doppelt.<br>";

// Ein Vergleich aus Lektion 5: War es eine 6?
echo "War es eine 6? ";
var_dump($wurf == 6);
