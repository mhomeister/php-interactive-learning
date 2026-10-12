<?php
// Alle Lektionen für den Spielplatz (index.php).
// Der Startcode jeder Lektion kommt aus der Datei lernen-{nummer}.php.
// 'id' ist ein fester Name, unter dem der Browser den Stand speichert (bleibt gleich, auch wenn sich Nummern ändern).
// Seiten mit 'info' sind reine Leseseiten ohne Code-Editor ('info' ist das Symbol in der Seitenleiste).

$heute = (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('d.m.Y');

return [
    0 => [
        'id' => 'einfuehrung',
        'titel' => 'Was ist PHP?',
        'info' => '👋',
        'label' => 'Einführung',
        'intro' => str_replace('{{HEUTE}}', $heute, <<<'HTML'
            <p>Willkommen! Bevor es losgeht, schauen wir kurz, <strong>wofür</strong> wir PHP überhaupt brauchen.</p>

            <h3>📄 HTML: die Seite steht fest</h3>
            <p>Eine HTML-Seite ist wie ein <strong>ausgedrucktes Plakat</strong>. Sie zeigt jedem Besucher immer genau das Gleiche,
            heute, morgen und in einem Jahr. Das nennt man <strong>statisch</strong>.</p>
            <pre><code>&lt;p&gt;Hallo! Heute ist ein schöner Tag.&lt;/p&gt;</code></pre>

            <h3>⚙️ PHP: die Seite rechnet mit</h3>
            <p>Mit PHP kann die Seite etwas <strong>ausrechnen</strong>, bevor sie im Browser ankommt.
            Wie eine Anzeigetafel am Bahnhof, die sich immer wieder ändert.</p>
            <pre><code>&lt;p&gt;Hallo! Heute ist der &lt;?php echo date("d.m.Y"); ?&gt;&lt;/p&gt;</code></pre>
            <p>Das kommt im Browser an: <strong>Hallo! Heute ist der {{HEUTE}}</strong><br>
            Morgen steht dort automatisch ein anderes Datum, ohne dass jemand die Seite ändert.</p>
            <p>So funktionieren fast alle Webseiten: dein Name nach dem Einloggen, die Summe im Warenkorb, neue Nachrichten.
            All das rechnet PHP (oder eine ähnliche Sprache) aus.</p>

            <h3>🎯 Was wir heute machen</h3>
            <p>Wir lernen die Grundlagen von PHP, Schritt für Schritt. Darauf baut nachher <strong>Laravel</strong> auf.</p>
            <p>So geht jede Lektion: kurz lesen, Code ändern, auf <strong>▶ Ausführen</strong> klicken und schauen, was passiert.
            Kaputt machen kannst du nichts. Mit <strong>↺ Zurücksetzen</strong> ist alles wieder wie vorher. Los geht's! 👉</p>
            HTML),
        'aufgabe' => [],
    ],
    1 => [
        'id' => 'hallo-welt',
        'titel' => 'Hallo Welt!',
        'intro' => <<<'HTML'
            <p>Mit <code>echo</code> zeigt PHP etwas an. Klick einfach mal auf <strong>▶ Ausführen</strong>!</p>
            <p>Wichtig: Jeder Befehl endet mit einem Semikolon <code>;</code> Es sagt PHP: „Dieser Befehl ist fertig, jetzt kommt ein neuer.“</p>
            <p>Deshalb könntest du sogar alles in eine einzige Zeile schreiben. Wir schreiben jeden Befehl nur in eine eigene Zeile,
            damit <strong>wir Menschen</strong> den Code besser lesen können. PHP ist das egal.</p>
            <p><code>&lt;br&gt;</code> steht für <strong>break</strong> (englisch für „Umbruch“) und bedeutet <strong>neue Zeile</strong> (wie die Enter-Taste). Ohne <code>&lt;br&gt;</code> schreibt der Browser alles
            in eine lange Zeile. Manchmal sieht man es auch als <code>&lt;br/&gt;</code>, das ist genau dasselbe.</p>
            HTML,
        'aufgabe' => [
            'Ändere „Hallo Welt!“ in „Hallo Lars!“.',
            'Lösche die Zeile mit <code>&lt;br&gt;</code> und schau, was passiert. Dann wieder einfügen!',
            'Lösche ein <code>;</code> und schau, was passiert. Dann wieder einfügen!',
            'Schreib zwei Befehle in eine Zeile: <code>echo "Eins! "; echo "Zwei!";</code> Funktioniert das?',
        ],
    ],
    2 => [
        'id' => 'variablen',
        'titel' => 'Variablen',
        'intro' => <<<'HTML'
            <p>Eine <strong>Variable</strong> ist eine Box mit Namen. Sie beginnt immer mit <code>$</code>.
            In die Box kannst du <strong>alles</strong> hineinlegen, zum Beispiel:</p>
            <ul>
                <li><strong>string</strong> = Text: <code>$name = "Lars";</code></li>
                <li><strong>int</strong> = ganze Zahl: <code>$alter = 16;</code></li>
                <li><strong>bool</strong> = ja oder nein: <code>$hatHunger = true;</code></li>
            </ul>
            <p><code>var_dump()</code> verrät dir, was in einer Box steckt.</p>
            HTML,
        'aufgabe' => [
            'Ändere „Lars“ in deinen Spitznamen und <code>16</code> in dein Alter.',
            'Ändere <code>true</code> in <code>false</code>. Was zeigt <code>var_dump</code> jetzt?',
            'Schreib dein Alter mal in Anführungszeichen: <code>"16"</code>. Was zeigt <code>var_dump</code> jetzt? 🤔',
        ],
    ],
    3 => [
        'id' => 'texte',
        'titel' => 'Texte zusammenbauen',
        'intro' => <<<'HTML'
            <p>Steht eine Variable in <code>"..."</code>, setzt PHP automatisch ihren Inhalt ein.</p>
            <p>Mit dem Punkt <code>.</code> kannst du Texte auch zusammenkleben.</p>
            HTML,
        'aufgabe' => [
            'Ändere „Pizza“ in dein Lieblingsessen.',
            'Ändere „Lars“ in den Namen von deinem besten Freund.',
        ],
    ],
    4 => [
        'id' => 'rechnen',
        'titel' => 'Rechnen',
        'intro' => <<<'HTML'
            <p>PHP ist ein Taschenrechner: <code>+</code> plus, <code>-</code> minus, <code>*</code> mal, <code>/</code> geteilt.</p>
            HTML,
        'aufgabe' => [
            'Ändere <code>5 + 3</code> in <code>60 * 60</code>. So viele Sekunden hat eine Stunde!',
            'Ändere die Anzahl der Pizzen auf 5.',
        ],
    ],
    5 => [
        'id' => 'vergleichen',
        'titel' => 'Vergleichen',
        'intro' => <<<'HTML'
            <p>Achtung, wichtiger Unterschied: <code>=</code> <strong>speichert</strong> etwas in einer Box,
            <code>==</code> <strong>vergleicht</strong> zwei Dinge.</p>
            <p>Ein Vergleich ist eine Frage. Die Antwort ist immer <code>true</code> (ja) oder <code>false</code> (nein), also ein <strong>bool</strong>.</p>
            <ul>
                <li><code>==</code> ist gleich</li>
                <li><code>&gt;</code> größer als</li>
                <li><code>&lt;</code> kleiner als</li>
                <li><code>&gt;=</code> größer oder gleich</li>
                <li><code>&lt;=</code> kleiner oder gleich</li>
            </ul>
            HTML,
        'aufgabe' => [
            'Ändere <code>16</code> in dein Alter. Welche Antworten ändern sich?',
            'Ändere <code>$alter &gt; 18</code> in <code>$alter &gt; 10</code>. Was kommt jetzt raus?',
            'Lösch ein <code>=</code> bei <code>$alter == 16</code>. Was zeigt <code>var_dump</code> jetzt? 🤔',
        ],
    ],
    6 => [
        'id' => 'wuerfeln',
        'titel' => 'Würfeln und Zwischenspeichern',
        'intro' => <<<'HTML'
            <p><code>rand(1, 6)</code> würfelt eine Zahl von 1 bis 6.</p>
            <p>Das Ergebnis legen wir in die Box <code>$wurf</code>. So können wir es <strong>zwischenspeichern</strong>
            und danach immer wieder benutzen: zum Anzeigen, zum Weiterrechnen und zum Vergleichen.</p>
            <p>Klick mehrmals auf Ausführen. Jedes Mal wird neu gewürfelt!</p>
            HTML,
        'aufgabe' => [
            'Speichere die Anzahl der Würfelseiten vorher in einer eigenen Box: Schreib über die Zeile mit <code>rand</code> eine neue Zeile <code>$seiten = 6;</code> und ändere <code>rand(1, 6)</code> in <code>rand(1, $seiten)</code>.',
            'Ändere jetzt nur <code>$seiten</code> auf <code>20</code>. Schon hast du einen Riesenwürfel! 🎲',
            'Ändere <code>$wurf * 2</code> in <code>rand(1, 6) * 2</code>. Passt „Doppelt so viel“ jetzt noch? 🤔',
        ],
    ],
    7 => [
        'id' => 'if',
        'titel' => 'Entscheiden mit if',
        'intro' => <<<'HTML'
            <p>Mit <code>if</code> trifft PHP Entscheidungen: <em>Wenn</em> die Antwort auf einen Vergleich <code>true</code> ist,
            passiert, was in den <code>{ }</code> steht. <em>Sonst</em> (<code>else</code>) passiert das andere.</p>
            <p>Mit <code>elseif</code> („sonst wenn“) kannst du noch mehr Fälle unterscheiden.</p>
            <p>⚠️ <strong>Ganz wichtig:</strong> <code>=</code> ist <strong>nur</strong> zum Speichern (Zuweisung), <strong>kein</strong> Vergleich!
            In ein <code>if</code> gehört zum Vergleichen immer <code>==</code>.</p>
            HTML,
        'aufgabe' => [
            'Ändere „Gewonnen! 🎉“ in deinen eigenen Jubel-Spruch.',
            'Ändere <code>$wurf &gt;= 5</code> in <code>$wurf &gt;= 4</code>. Wann gibt es jetzt „Super Wurf“?',
            'Ändere <code>$wurf == 6</code> in <code>$wurf = 6</code> (nur ein <code>=</code>). Klick ein paar Mal auf Ausführen. Was ist komisch? 🤔',
        ],
    ],
    8 => [
        'id' => 'wiederholen',
        'titel' => 'Wiederholen',
        'intro' => <<<'HTML'
            <p>Mit einer <strong>Schleife</strong> macht der Computer etwas mehrmals, ohne dass du es mehrmals schreiben musst.</p>
            HTML,
        'aufgabe' => [
            'Ändere die <code>5</code> in eine <code>10</code>. Jetzt gibt es 10 Runden!',
            'Ändere „Das ist Runde“ in „Hallo! Runde“.',
        ],
    ],
    9 => [
        'id' => 'funktionen',
        'titel' => 'Eigene Befehle',
        'intro' => <<<'HTML'
            <p>Mit <code>function</code> baust du deinen eigenen Befehl.</p>
            <ul>
                <li><code>sagHallo()</code> hat <strong>keinen Parameter</strong>: Sie macht immer genau das Gleiche.</li>
                <li><code>begruesse($name)</code> hat <strong>einen Parameter</strong>: Was du in die Klammern schreibst, landet in der Box <code>$name</code>.</li>
            </ul>
            HTML,
        'aufgabe' => [
            'Ruf <code>sagHallo();</code> noch ein drittes Mal auf.',
            'Ändere „Mama“ und „Opa“ in die Namen von deinen Freunden.',
            'Ändere „schön dich zu sehen“ in einen eigenen Gruß.',
        ],
    ],
    10 => [
        'id' => 'wuerfel-duell',
        'titel' => 'Würfel-Duell',
        'intro' => <<<'HTML'
            <p>Dein erstes kleines Spiel! Du und der Computer würfeln je zweimal, wer mehr hat, gewinnt.</p>
            <p>Hier steckt alles drin, was du heute gelernt hast. 🎉</p>
            HTML,
        'aufgabe' => [
            'Spiel ein paar Runden: Klick mehrmals auf Ausführen.',
            'Schummeln erlaubt: Ändere die Zeile mit <code>$lars</code> in <code>$lars = wuerfle() + wuerfle() + wuerfle();</code> 😉',
        ],
    ],
    11 => [
        'id' => 'laravel',
        'titel' => 'Was ist Laravel?',
        'info' => '🚀',
        'label' => 'Ausblick',
        'intro' => <<<'HTML'
            <p>Super, du hast die PHP-Grundlagen geschafft! 🎉 Als Nächstes kommt <strong>Laravel</strong>.</p>

            <h3>🧰 Was ist Laravel?</h3>
            <p>Laravel ist ein <strong>Werkzeugkasten für PHP</strong>. Viele Webseiten brauchen immer wieder die gleichen Dinge:
            Seiten anzeigen, Benutzer einloggen, Daten speichern. Das musst du nicht alles selbst bauen. Laravel bringt die Teile fertig mit,
            wie Lego-Steine, die du nur noch zusammensetzt.</p>

            <h3>🧩 Du kennst schon viel davon</h3>
            <p>Laravel ist ganz normales PHP. Alles von heute findest du dort wieder: Variablen, <code>if</code>, Schleifen und Funktionen.</p>
            <p>So sagt man Laravel: „Wenn jemand die Seite <code>/hallo</code> aufruft, zeig ‚Hallo Lars!‘ an.“</p>
            <pre><code>Route::get('/hallo', function () {
                return 'Hallo Lars!';
            });</code></pre>
            <p>Siehst du die <code>function</code>? Das ist eine Funktion wie in Lektion 9, nur ohne Namen.</p>

            <p>Und so baut man in Laravel eine Seite mit einer Variable:</p>
            <pre><code>&lt;h1&gt;Hallo {{ $name }}!&lt;/h1&gt;</code></pre>
            <p>Das ist fast wie <code>echo "Hallo $name!";</code>, nur mit <code>{{ }}</code> drumherum.</p>

            <h3>🤖 Und dann: Programmieren mit KI</h3>
            <p>Mit <strong>Cursor</strong> schreibt eine KI einen Teil vom Code für dich. Aber du musst verstehen, was sie schreibt,
            damit du Fehler findest und weißt, was du ändern willst. Genau dafür war heute da. 💪</p>
            HTML,
        'aufgabe' => [],
    ],
];
