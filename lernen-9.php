<?php
// Lektion 9: Eigene Befehle (Funktionen)
// Eine Funktion ist wie ein Rezept: einmal aufschreiben, oft benutzen.

// 1. Eine Funktion OHNE Parameter: macht immer genau das Gleiche.
function sagHallo()
{
    echo "Hallo! 👋<br>";
}

sagHallo();
sagHallo();
echo "<br>";

// 2. Eine Funktion MIT Parameter: in die Klammern gibst du ihr etwas mit.
function begruesse($name)
{
    echo "Hey $name, schön dich zu sehen!<br>";
}

begruesse("Lars");
begruesse("Mama");
begruesse("Opa");
