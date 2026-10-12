<?php
// PHP-Spielplatz: Lektion lesen, Code bearbeiten, ausführen, KI um Hilfe fragen.

$lektionen = require __DIR__.'/lektionen.php';

foreach ($lektionen as $nummer => &$lektion) {
    $lektion['code'] = empty($lektion['info']) ? file_get_contents(__DIR__."/lernen-{$nummer}.php") : '';
}
unset($lektion);

$kiAktiv = file_exists(__DIR__.'/config.php') && ((require __DIR__.'/config.php')['openai_api_key'] ?? '') !== '';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP-Spielplatz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
    <style>
        :root {
            --bg: #f4f2ee;
            --karte: #ffffff;
            --text: #1f1d1a;
            --leise: #6b665e;
            --rand: #e2ded6;
            --akzent: #4f5bd5;
            --akzent-hell: #eceefc;
            --gruen: #1f8a4c;
            --gruen-hell: #e6f4ec;
            --rot: #c0392b;
            --rot-hell: #fbeceb;
            --gelb: #9a6b00;
            --gelb-hell: #fdf4dc;
        }
        * { box-sizing: border-box; }
        [hidden] { display: none !important; }
        body {
            margin: 0;
            font: 16px/1.55 system-ui, -apple-system, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        code { font: 0.9em ui-monospace, "SF Mono", Menlo, monospace; background: var(--akzent-hell); padding: 1px 5px; border-radius: 4px; }
        a { color: var(--akzent); }

        .layout { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }

        aside { background: #1f1d2b; color: #d8d6e6; padding: 20px 14px; }
        aside h1 { font-size: 20px; margin: 0 0 4px 8px; color: #fff; }
        aside .fortschritt { margin: 0 0 18px 8px; font-size: 13px; color: #9e9bb5; }
        aside ol { list-style: none; margin: 0; padding: 0; }
        aside button {
            display: flex; gap: 10px; align-items: center; width: 100%;
            text-align: left; background: none; border: 0; color: inherit; font: inherit; font-size: 15px;
            padding: 8px 10px; border-radius: 8px; cursor: pointer;
        }
        aside button:hover { background: #2c2a3d; }
        aside button.aktiv { background: var(--akzent); color: #fff; }
        aside .nr {
            flex: none; width: 24px; height: 24px; border-radius: 50%; display: grid; place-items: center;
            font-size: 12px; font-weight: 600; background: #34324a;
        }
        aside button.erledigt .nr { background: var(--gruen); color: #fff; }
        aside hr { border: 0; border-top: 1px solid #34324a; margin: 12px 8px; }

        main { padding: 24px 28px 40px; display: flex; flex-direction: column; gap: 18px; min-width: 0; }
        .karte { background: var(--karte); border: 1px solid var(--rand); border-radius: 12px; padding: 18px 22px; }

        .kopf { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .kopf h2 { margin: 0; font-size: 26px; }
        .kopf .label { color: var(--leise); font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .einfuehrung { display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px; margin-top: 10px; }
        .einfuehrung p { margin: 0 0 10px; }
        .einfuehrung.info { grid-template-columns: 1fr; max-width: 760px; }
        .einfuehrung h3 { margin: 22px 0 6px; font-size: 18px; }
        .einfuehrung pre { background: #282a36; color: #f8f8f2; padding: 12px 14px; border-radius: 8px; overflow: auto; font: 14px/1.5 ui-monospace, Menlo, monospace; }
        .einfuehrung pre code { background: none; padding: 0; color: inherit; }
        .aufgabe { background: var(--gelb-hell); border-radius: 10px; padding: 12px 16px; }
        .aufgabe h3 { margin: 0 0 6px; font-size: 15px; }
        .aufgabe ol { margin: 0; padding-left: 20px; }
        .aufgabe li { margin-bottom: 4px; }

        .arbeitsbereich { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; min-height: 520px; }
        .panel { display: flex; flex-direction: column; background: var(--karte); border: 1px solid var(--rand); border-radius: 12px; overflow: hidden; min-width: 0; }
        .panel-kopf { display: flex; align-items: center; gap: 8px; padding: 10px 12px; border-bottom: 1px solid var(--rand); flex-wrap: wrap; }
        .panel-kopf .titel { font-weight: 600; margin-right: auto; }
        #editor { flex: 1; min-height: 0; position: relative; }
        #editor .CodeMirror { position: absolute; inset: 0; height: auto; font-size: 14px; }
        .fehlerzeile { background: rgba(255, 85, 85, .25) !important; }
        .warnzeile { background: rgba(241, 250, 140, .12) !important; }

        button.knopf {
            font: inherit; font-size: 14px; font-weight: 600; border-radius: 8px; padding: 7px 14px; cursor: pointer;
            border: 1px solid var(--rand); background: #fff; color: var(--text);
        }
        button.knopf:hover { border-color: var(--akzent); }
        button.knopf.haupt { background: var(--gruen); border-color: var(--gruen); color: #fff; }
        button.knopf.haupt:hover { filter: brightness(1.1); }
        button.knopf.ki { background: var(--akzent); border-color: var(--akzent); color: #fff; }
        button.knopf:disabled { opacity: .5; cursor: default; }
        .tipp-taste { font-size: 12px; color: var(--leise); }

        .tabs button { font: inherit; font-size: 13px; border: 0; background: none; padding: 4px 10px; border-radius: 6px; cursor: pointer; color: var(--leise); }
        .tabs button.aktiv { background: var(--akzent-hell); color: var(--akzent); font-weight: 600; }
        .status { font-size: 13px; color: var(--leise); }

        .meldungen { display: flex; flex-direction: column; gap: 8px; padding: 0 12px; }
        .meldungen:not(:empty) { padding: 12px; border-bottom: 1px solid var(--rand); }
        .meldung { border-radius: 8px; padding: 10px 12px; font-size: 14px; }
        .meldung.fehler { background: var(--rot-hell); border-left: 4px solid var(--rot); }
        .meldung.warnung { background: var(--gelb-hell); border-left: 4px solid var(--gelb); }
        .meldung strong { display: block; }
        .meldung .original { font-family: ui-monospace, Menlo, monospace; font-size: 12.5px; color: var(--leise); margin-top: 4px; word-break: break-word; }
        .meldung .knopf { margin-top: 8px; }

        .ausgabe { flex: 1; position: relative; min-height: 200px; }
        .ausgabe iframe, .ausgabe pre { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; margin: 0; }
        .ausgabe pre { padding: 14px; overflow: auto; font: 13px/1.5 ui-monospace, Menlo, monospace; white-space: pre-wrap; background: #fafaf8; }
        .leer { position: absolute; inset: 0; display: grid; place-items: center; color: var(--leise); text-align: center; padding: 20px; }

        .ki-kopf { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .ki-kopf h3 { margin: 0 auto 0 0; font-size: 18px; }
        .ki-frage { display: flex; gap: 8px; margin-top: 12px; }
        .ki-frage input { flex: 1; font: inherit; padding: 8px 12px; border: 1px solid var(--rand); border-radius: 8px; min-width: 0; }
        .ki-antwort { margin-top: 14px; padding: 14px 16px; background: var(--akzent-hell); border-radius: 10px; }
        .ki-antwort:empty { display: none; }
        .ki-antwort p { margin: 0 0 10px; }
        .ki-antwort p:last-child { margin-bottom: 0; }
        .ki-antwort pre { background: #282a36; color: #f8f8f2; padding: 10px 12px; border-radius: 8px; overflow: auto; font-size: 13px; }
        .ki-antwort pre code { background: none; padding: 0; color: inherit; }
        .ki-hinweis { color: var(--leise); font-size: 14px; margin: 8px 0 0; }

        .navigation { display: flex; justify-content: space-between; gap: 12px; }

        @media (max-width: 1000px) {
            .layout { grid-template-columns: 1fr; }
            aside ol { display: flex; flex-wrap: wrap; gap: 4px; }
            aside button { width: auto; }
            aside button .text { display: none; }
            main { padding: 16px; }
            .einfuehrung, .arbeitsbereich { grid-template-columns: 1fr; }
            .arbeitsbereich .panel { min-height: 420px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside>
        <h1>🐘 PHP lernen</h1>
        <p class="fortschritt" id="fortschritt"></p>
        <ol id="liste"></ol>
    </aside>

    <main>
        <section class="karte">
            <div class="kopf">
                <div>
                    <div class="label" id="lektion-label"></div>
                    <h2 id="lektion-titel"></h2>
                </div>
                <button class="knopf" id="erledigt-knopf"></button>
            </div>
            <div class="einfuehrung" id="einfuehrung">
                <div id="lektion-intro"></div>
                <div class="aufgabe" id="aufgabe-box">
                    <h3>✏️ Deine Aufgabe</h3>
                    <ol id="lektion-aufgabe"></ol>
                </div>
            </div>
        </section>

        <section class="arbeitsbereich" id="arbeitsbereich">
            <div class="panel">
                <div class="panel-kopf">
                    <span class="titel">Dein Code</span>
                    <button class="knopf" id="zuruecksetzen" title="Den ursprünglichen Code der Lektion wiederherstellen">↺ Zurücksetzen</button>
                    <button class="knopf haupt" id="ausfuehren">▶ Ausführen</button>
                </div>
                <div id="editor"></div>
            </div>

            <div class="panel">
                <div class="panel-kopf">
                    <span class="titel">Ergebnis</span>
                    <span class="status" id="status"></span>
                    <div class="tabs">
                        <button class="aktiv" data-ansicht="browser">Browser</button>
                        <button data-ansicht="quelltext">Quelltext</button>
                    </div>
                </div>
                <div class="meldungen" id="meldungen"></div>
                <div class="ausgabe">
                    <iframe id="vorschau" sandbox title="Ausgabe"></iframe>
                    <pre id="quelltext" hidden></pre>
                    <div class="leer" id="leer">Klicke auf <strong>▶ Ausführen</strong><br><span class="tipp-taste">oder drücke Strg/⌘ + Enter</span></div>
                </div>
            </div>
        </section>

        <section class="karte" id="ki-bereich">
            <div class="ki-kopf">
                <h3>🤖 KI-Tutor</h3>
                <button class="knopf ki" data-modus="pruefen">Aufgabe prüfen</button>
                <button class="knopf ki" data-modus="fehler">Fehler erklären</button>
            </div>
            <form class="ki-frage" id="ki-formular">
                <input id="ki-eingabe" placeholder="Frag etwas, z.B. „Warum brauche ich hier ein $?“" autocomplete="off">
                <button class="knopf ki" type="submit">Fragen</button>
            </form>
            <?php if (! $kiAktiv): ?>
                <p class="ki-hinweis">Der KI-Tutor ist noch nicht eingerichtet: Kopiere <code>config.example.php</code> nach <code>config.php</code> und trage einen OpenAI-API-Key ein.</p>
            <?php endif; ?>
            <div class="ki-antwort" id="ki-antwort"></div>
        </section>

        <nav class="navigation">
            <button class="knopf" id="zurueck">← Vorherige Lektion</button>
            <button class="knopf" id="weiter">Nächste Lektion →</button>
        </nav>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/clike/clike.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
<script>
const LEKTIONEN = <?php echo json_encode($lektionen, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>;
const NUMMERN = Object.keys(LEKTIONEN).map(Number);
const UEBUNGEN = NUMMERN.filter((nr) => !LEKTIONEN[nr].info);

// Fortschritt im Browser merken (localStorage kann in privaten Fenstern fehlen)
const speicher = {
    lesen(schluessel, standard) {
        try { const wert = localStorage.getItem('php-lernen-v4:' + schluessel); return wert === null ? standard : JSON.parse(wert); }
        catch { return standard; }
    },
    schreiben(schluessel, wert) {
        try { localStorage.setItem('php-lernen-v4:' + schluessel, JSON.stringify(wert)); } catch {}
    },
};

// Einmalig: alten Stand übernehmen, als noch nach Lektionsnummern gespeichert wurde
const ALTE_REIHENFOLGE = {
    v3: ['einfuehrung', 'hallo-welt', 'variablen', 'texte', 'rechnen', 'vergleichen', 'wuerfeln', 'wiederholen', 'funktionen', 'wuerfel-duell', 'laravel'],
    v2: ['einfuehrung', 'hallo-welt', 'variablen', 'texte', 'rechnen', 'wuerfeln', 'wiederholen', 'funktionen', 'wuerfel-duell', 'laravel'],
};
try {
    const version = ['v3', 'v2'].find((v) => localStorage.getItem(`php-lernen-${v}:aktuell`) !== null);
    if (localStorage.getItem('php-lernen-v4:aktuell') === null && version) {
        const ids = ALTE_REIHENFOLGE[version];
        const alt = (schluessel) => JSON.parse(localStorage.getItem(`php-lernen-${version}:${schluessel}`));
        ids.forEach((id, nr) => {
            const code = localStorage.getItem(`php-lernen-${version}:code:${nr}`);
            if (code !== null) speicher.schreiben('code:' + id, JSON.parse(code));
        });
        speicher.schreiben('erledigt', (alt('erledigt') || []).map((nr) => ids[nr]));
        speicher.schreiben('aktuell', ids[alt('aktuell')] ?? ids[0]);
    }
} catch {}

const nummerVon = (id) => NUMMERN.find((nr) => LEKTIONEN[nr].id === id);
let aktuell = nummerVon(speicher.lesen('aktuell')) ?? NUMMERN[0];
let erledigt = speicher.lesen('erledigt', []).map(nummerVon).filter((nr) => nr !== undefined);
let letztesErgebnis = null;
let markierteZeilen = [];

const $ = (id) => document.getElementById(id);

const editor = CodeMirror($('editor'), {
    mode: 'application/x-httpd-php',
    theme: 'dracula',
    lineNumbers: true,
    indentUnit: 4,
    matchBrackets: true,
    autoCloseBrackets: true,
    lineWrapping: true,
    extraKeys: {
        'Ctrl-Enter': ausfuehren,
        'Cmd-Enter': ausfuehren,
        Tab: (cm) => cm.replaceSelection('    '),
    },
});

let speicherTimer;
editor.on('change', () => {
    clearTimeout(speicherTimer);
    speicherTimer = setTimeout(() => speicher.schreiben('code:' + LEKTIONEN[aktuell].id, editor.getValue()), 300);
});

function zeigeListe() {
    $('liste').innerHTML = NUMMERN.map((nr) => {
        const info = LEKTIONEN[nr].info;
        // Trennstrich zwischen Info-Seiten und Lektionen
        const trenner = nr !== NUMMERN[0] && Boolean(info) !== Boolean(LEKTIONEN[nr - 1]?.info) ? '<li><hr></li>' : '';
        return `${trenner}
        <li><button data-nr="${nr}" class="${nr === aktuell ? 'aktiv' : ''} ${erledigt.includes(nr) ? 'erledigt' : ''}">
            <span class="nr">${info ? info : (erledigt.includes(nr) ? '✓' : nr)}</span>
            <span class="text">${LEKTIONEN[nr].titel}</span>
        </button></li>`;
    }).join('');
    const geschafft = erledigt.filter((nr) => UEBUNGEN.includes(nr)).length;
    $('fortschritt').textContent = `${geschafft} von ${UEBUNGEN.length} Lektionen erledigt`;
}

function zeigeLektion(nr) {
    aktuell = nr;
    speicher.schreiben('aktuell', LEKTIONEN[nr].id);
    const lektion = LEKTIONEN[nr];

    const info = Boolean(lektion.info);
    $('lektion-label').textContent = info ? lektion.label : `Lektion ${nr} von ${UEBUNGEN.length}`;
    $('einfuehrung').classList.toggle('info', info);
    ['aufgabe-box', 'arbeitsbereich', 'ki-bereich', 'erledigt-knopf'].forEach((id) => $(id).hidden = info);
    $('lektion-titel').textContent = lektion.titel;
    $('lektion-intro').innerHTML = lektion.intro;
    $('lektion-aufgabe').innerHTML = lektion.aufgabe.map((a) => `<li>${a}</li>`).join('');
    $('erledigt-knopf').textContent = erledigt.includes(nr) ? '✓ Erledigt' : 'Als erledigt markieren';

    editor.setValue(speicher.lesen('code:' + lektion.id, lektion.code));
    editor.clearHistory();
    editor.scrollTo(0, 0);
    editor.refresh(); // nötig, wenn der Editor vorher versteckt war

    $('zurueck').disabled = nr === NUMMERN[0];
    $('weiter').disabled = nr === NUMMERN[NUMMERN.length - 1];

    leereAusgabe();
    $('ki-antwort').innerHTML = '';
    zeigeListe();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function leereAusgabe() {
    letztesErgebnis = null;
    $('vorschau').srcdoc = '';
    $('quelltext').textContent = '';
    $('meldungen').innerHTML = '';
    $('status').textContent = '';
    $('leer').hidden = false;
    entferneMarkierungen();
}

function entferneMarkierungen() {
    markierteZeilen.forEach(([zeile, klasse]) => editor.removeLineClass(zeile, 'background', klasse));
    markierteZeilen = [];
}

function markiereZeile(zeile, klasse) {
    if (!zeile || zeile > editor.lineCount()) return;
    editor.addLineClass(zeile - 1, 'background', klasse);
    markierteZeilen.push([zeile - 1, klasse]);
}

// Einfache deutsche Übersetzungen für die häufigsten Fehlermeldungen
function deutscherTipp(meldung) {
    const tipps = [
        [/unexpected end of file/, 'Die Datei hört zu früh auf. Meistens fehlt eine schließende Klammer } oder ein Anführungszeichen.'],
        [/unexpected token "\}"|unexpected '\}'/, 'Da ist eine } zu viel, oder davor fehlt ein ;'],
        [/unexpected (identifier|variable|token "echo"|token "\$)/, 'Wahrscheinlich fehlt in der Zeile DAVOR ein Semikolon ;'],
        [/syntax error/, 'Syntaxfehler: PHP versteht den Satzbau nicht. Oft fehlt ein ; oder eine Klammer. Schau auch in die Zeile davor!'],
        [/Undefined variable \$(\w+)/, 'Die Variable $$1 gibt es (noch) nicht. Tippfehler? Groß-/Kleinschreibung? Oder wurde sie erst weiter unten angelegt?'],
        [/Undefined array key/, 'Dieses Fach gibt es im Array nicht. Denk dran: Zählen beginnt bei 0!'],
        [/Call to undefined function (\w+)/, 'Die Funktion $1() gibt es nicht. Tippfehler, oder hast du sie noch nicht geschrieben?'],
        [/Cannot redeclare/, 'Eine Funktion mit diesem Namen gibt es schon. Jeder Funktionsname darf nur einmal vorkommen.'],
        [/Division by zero/, 'Durch 0 teilen geht nicht – auch nicht in PHP!'],
        [/Unsupported operand types/, 'Hier wird mit etwas gerechnet, das keine Zahl ist (z.B. einem Array oder Text).'],
        [/Too few arguments/, 'Die Funktion braucht mehr Eingaben in den Klammern, als du ihr gegeben hast.'],
        [/must be of type (\w+), (\w+) given/, 'Falscher Typ: Erwartet wurde $1, bekommen hat die Funktion aber $2.'],
        [/Maximum execution time/, 'Das Programm lief zu lange – vermutlich eine Endlosschleife. Wird die Bedingung jemals falsch?'],
        [/Allowed memory size/, 'Zu viel Speicher verbraucht – vermutlich eine Endlosschleife, die immer mehr ausgibt.'],
        [/Array to string conversion/, 'Ein Array kann man nicht einfach mit echo ausgeben. Nimm print_r() oder ein bestimmtes Fach wie $liste[0].'],
    ];
    for (const [muster, tipp] of tipps) {
        const treffer = meldung.match(muster);
        if (treffer) return tipp.replace(/\$(\d)/g, (_, i) => treffer[i]);
    }
    return null;
}

function meldungHtml(m, art) {
    const zeile = m.zeile ? ` in Zeile ${m.zeile}` : '';
    const tipp = deutscherTipp(m.meldung);
    return `<div class="meldung ${art}">
        <strong>${art === 'fehler' ? '❌ Fehler' : '⚠️ Warnung'}${zeile}</strong>
        ${tipp ? escapeHtml(tipp) : ''}
        <div class="original">${escapeHtml(m.typ)}: ${escapeHtml(m.meldung)}</div>
    </div>`;
}

async function ausfuehren() {
    const knopf = $('ausfuehren');
    knopf.disabled = true;
    $('status').textContent = 'Läuft …';
    entferneMarkierungen();

    try {
        const antwort = await fetch('ausfuehren.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code: editor.getValue() }),
        });
        const ergebnis = await antwort.json();
        letztesErgebnis = ergebnis;

        $('leer').hidden = true;
        $('quelltext').textContent = ergebnis.ausgabe || '(keine Ausgabe)';
        $('vorschau').srcdoc = '<meta charset="utf-8"><style>body{font:15px/1.5 system-ui,sans-serif;margin:14px;color:#1f1d1a}</style>'
            + (ergebnis.ausgabe || '<p style="color:#888">(keine Ausgabe)</p>');

        let html = '';
        if (ergebnis.fehler) {
            html += meldungHtml(ergebnis.fehler, 'fehler');
            markiereZeile(ergebnis.fehler.zeile, 'fehlerzeile');
        }
        ergebnis.warnungen.slice(0, 5).forEach((w) => {
            html += meldungHtml(w, 'warnung');
            markiereZeile(w.zeile, 'warnzeile');
        });
        if (ergebnis.warnungen.length > 5) {
            html += `<div class="meldung warnung">… und ${ergebnis.warnungen.length - 5} weitere Warnungen</div>`;
        }
        if (html) {
            html += `<div><button class="knopf ki" onclick="fragKi('fehler')">🤖 Erklär mir das</button></div>`;
        }
        $('meldungen').innerHTML = html;

        $('status').textContent = ergebnis.fehler ? '' : `✓ fertig in ${ergebnis.dauer_ms} ms`;
    } catch (e) {
        $('status').textContent = 'Konnte nicht ausgeführt werden: ' + e.message;
    } finally {
        knopf.disabled = false;
    }
}

async function fragKi(modus, frage = '') {
    const ziel = $('ki-antwort');
    ziel.innerHTML = '<p>🤔 Ich denke nach …</p>';
    ziel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    document.querySelectorAll('.knopf.ki').forEach((k) => k.disabled = true);

    try {
        const antwort = await fetch('ki-hilfe.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lektion: aktuell, modus, frage, code: editor.getValue(), ergebnis: letztesErgebnis }),
        });
        const daten = await antwort.json();
        ziel.innerHTML = daten.fehler ? `<p>⚠️ ${escapeHtml(daten.fehler)}</p>` : markdown(daten.antwort);
    } catch (e) {
        ziel.innerHTML = `<p>⚠️ Die KI ist gerade nicht erreichbar: ${escapeHtml(e.message)}</p>`;
    } finally {
        document.querySelectorAll('.knopf.ki').forEach((k) => k.disabled = false);
    }
}

function escapeHtml(text) {
    return String(text).replace(/[&<>"']/g, (z) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[z]));
}

// Ganz einfaches Markdown: Codeblöcke, `code`, **fett**, Listen und Absätze
function markdown(text) {
    const bloecke = [];
    text = escapeHtml(text).replace(/```\w*\n?([\s\S]*?)```/g, (_, code) => {
        bloecke.push(`<pre><code>${code.replace(/\n$/, '')}</code></pre>`);
        return `\u0000${bloecke.length - 1}\u0000`;
    });
    text = text
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
        .split(/\n{2,}/)
        .map((absatz) => {
            if (/^\u0000\d+\u0000$/.test(absatz.trim())) return absatz.trim();
            if (/^\s*([-*]|\d+\.) /m.test(absatz) && absatz.split('\n').every((z) => /^\s*([-*]|\d+\.) /.test(z))) {
                return '<ul>' + absatz.split('\n').map((z) => `<li>${z.replace(/^\s*([-*]|\d+\.) /, '')}</li>`).join('') + '</ul>';
            }
            return `<p>${absatz.replace(/\n/g, '<br>')}</p>`;
        })
        .join('');
    return text.replace(/\u0000(\d+)\u0000/g, (_, i) => bloecke[i]);
}

// --- Knöpfe verbinden ---
$('ausfuehren').addEventListener('click', ausfuehren);
$('zurueck').addEventListener('click', () => zeigeLektion(NUMMERN[NUMMERN.indexOf(aktuell) - 1]));
$('weiter').addEventListener('click', () => zeigeLektion(NUMMERN[NUMMERN.indexOf(aktuell) + 1]));
$('liste').addEventListener('click', (e) => {
    const knopf = e.target.closest('button[data-nr]');
    if (knopf) zeigeLektion(Number(knopf.dataset.nr));
});
$('zuruecksetzen').addEventListener('click', () => {
    if (!confirm('Deinen Code für diese Lektion wirklich zurücksetzen?')) return;
    editor.setValue(LEKTIONEN[aktuell].code);
    leereAusgabe();
});
$('erledigt-knopf').addEventListener('click', () => {
    erledigt = erledigt.includes(aktuell) ? erledigt.filter((n) => n !== aktuell) : [...erledigt, aktuell];
    speicher.schreiben('erledigt', erledigt.map((nr) => LEKTIONEN[nr].id));
    $('erledigt-knopf').textContent = erledigt.includes(aktuell) ? '✓ Erledigt' : 'Als erledigt markieren';
    zeigeListe();
});
document.querySelectorAll('.tabs button').forEach((tab) => tab.addEventListener('click', () => {
    document.querySelectorAll('.tabs button').forEach((t) => t.classList.toggle('aktiv', t === tab));
    $('vorschau').hidden = tab.dataset.ansicht !== 'browser';
    $('quelltext').hidden = tab.dataset.ansicht !== 'quelltext';
}));
document.querySelectorAll('.ki-kopf .knopf').forEach((k) => k.addEventListener('click', () => fragKi(k.dataset.modus)));
$('ki-formular').addEventListener('submit', (e) => {
    e.preventDefault();
    const frage = $('ki-eingabe').value.trim();
    if (!frage) return;
    fragKi('frage', frage);
    $('ki-eingabe').value = '';
});

zeigeLektion(aktuell);
</script>
</body>
</html>
