<?php
// Lektion 7: Entscheiden mit if
// WENN (if) die Antwort auf die Frage true ist, passiert, was in den { } steht.
// SONST (else) passiert das, was bei else steht.

$wurf = rand(1, 6);
echo "Du hast eine $wurf gewürfelt! 🎲<br>";

// In die Klammern von if kommt ein VERGLEICH, also eine Frage:
if ($wurf == 6) {
    echo "Gewonnen! 🎉<br>";
} else {
    echo "Nochmal versuchen!<br>";
}

// Mit elseif ("sonst wenn") kann man noch mehr Fälle unterscheiden.
// PHP prüft von oben nach unten und nimmt den ERSTEN Fall, der passt.
if ($wurf >= 5) {
    echo "Super Wurf! 💪";
} elseif ($wurf >= 3) {
    echo "Ganz okay.";
} else {
    echo "Pech gehabt. 🙈";
}

// ⚠️ ACHTUNG:  =  ist NUR zum Speichern (Zuweisung), KEIN Vergleich!
//    if ($wurf = 6) fragt nicht, sondern legt die 6 in die Box.
//    Dann "gewinnt" man IMMER. Zum Vergleichen immer == benutzen!
