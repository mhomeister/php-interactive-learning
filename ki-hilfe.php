<?php
// Fragt die OpenAI-API nach einer Erklärung bzw. einem Tipp und gibt die Antwort als JSON zurück.

header('Content-Type: application/json; charset=utf-8');

if (! in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit(json_encode(['fehler' => 'Nur lokal erlaubt.']));
}

$config = file_exists(__DIR__.'/config.php') ? require __DIR__.'/config.php' : [];
$apiKey = $config['openai_api_key'] ?? '';
$modell = $config['openai_model'] ?? 'gpt-4.1-mini';

if ($apiKey === '') {
    exit(json_encode(['fehler' => 'Kein API-Key gefunden. Kopiere config.example.php nach config.php und trage deinen OpenAI-Key ein.']));
}

$daten = json_decode(file_get_contents('php://input'), true) ?? [];
$lektionen = require __DIR__.'/lektionen.php';
$lektion = $lektionen[(int) ($daten['lektion'] ?? 1)] ?? $lektionen[1];
$ergebnis = $daten['ergebnis'] ?? null;

$auftrag = match ($daten['modus'] ?? 'frage') {
    'fehler' => 'Erkläre den Fehler bzw. die Warnungen. Was bedeutet die Meldung auf Deutsch, in welcher Zeile liegt das Problem und warum? Gib einen Tipp, wie man es behebt, aber schreib nicht den kompletten korrigierten Code hin.',
    'pruefen' => 'Prüfe, welche Teile der Aufgabe schon gelöst sind und welche noch fehlen. Lob, was gut ist. Für fehlende Teile gib einen kleinen Hinweis, nicht die fertige Lösung.',
    default => 'Beantworte die Frage des Schülers: '.trim($daten['frage'] ?? ''),
};

// Zeilennummern vor den Code setzen, damit die KI sich auf Zeilen beziehen kann
$zeilen = explode("\n", $daten['code'] ?? '');
$codeMitNummern = implode("\n", array_map(fn ($zeile, $i) => str_pad($i + 1, 3, ' ', STR_PAD_LEFT).' | '.$zeile, $zeilen, array_keys($zeilen)));

$nachricht = "Lektion: {$lektion['titel']}\n"
    ."Aufgaben der Lektion:\n- ".implode("\n- ", array_map('strip_tags', $lektion['aufgabe']))."\n\n"
    ."Code des Schülers (mit Zeilennummern):\n```\n{$codeMitNummern}\n```\n\n";

if ($ergebnis) {
    $nachricht .= "Ausgabe beim letzten Ausführen:\n```\n".mb_substr($ergebnis['ausgabe'] ?? '', 0, 3000)."\n```\n";
    if (! empty($ergebnis['fehler'])) {
        $f = $ergebnis['fehler'];
        $nachricht .= "FEHLER: {$f['typ']}: {$f['meldung']} (Zeile ".($f['zeile'] ?? '?').")\n";
    }
    foreach ($ergebnis['warnungen'] ?? [] as $w) {
        $nachricht .= "WARNUNG: {$w['typ']}: {$w['meldung']} (Zeile ".($w['zeile'] ?? '?').")\n";
    }
} else {
    $nachricht .= "Der Code wurde noch nicht ausgeführt.\n";
}

$nachricht .= "\nAuftrag: {$auftrag}";

$system = <<<'TEXT'
    Du bist ein geduldiger, freundlicher Programmier-Tutor für einen absoluten PHP-Anfänger (Jugendlicher, deutschsprachig).
    Er lernt gerade die Grundlagen, bevor es mit Laravel weitergeht.
    Regeln:
    - Antworte immer auf Deutsch, duze ihn, kurz und einfach (maximal ca. 100 Wörter).
    - Er kennt nur: echo, Variablen (string, int, bool), var_dump(), Texte, Rechnen, rand(), Vergleiche (=, ==, <, >, <=, >=), if/elseif/else, for-Schleifen und einfache Funktionen.
      Benutze keine anderen Konzepte (keine Arrays, Klassen o.ä.) in deinen Erklärungen.
    - Benutze Alltagsvergleiche statt Fachchinesisch. Fachbegriffe kurz erklären.
    - Nenne die konkrete Zeilennummer, wenn es um einen Fehler geht.
    - Erkläre typische PHP-Eigenheiten, wenn sie eine Rolle spielen (fehlendes Semikolon, $ vergessen, = vs ==,
      . statt + bei Texten, Anführungszeichen vergessen, Komma statt Punkt bei Kommazahlen).
    - Verrate keine komplette Lösung, sondern gib Hinweise, damit er selbst draufkommt. Kleine Code-Schnipsel sind okay.
    - Formatiere mit kurzen Absätzen, `code` in Backticks und **fett** für das Wichtigste.
    - Sei ermutigend: Fehler gehören zum Programmieren dazu.
    TEXT;

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer '.$apiKey,
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => $modell,
        'messages' => [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $nachricht],
        ],
    ]),
]);

$antwort = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlFehler = curl_error($ch);

if ($antwort === false) {
    exit(json_encode(['fehler' => 'Keine Verbindung zu OpenAI: '.$curlFehler]));
}

$json = json_decode($antwort, true);

if ($status !== 200) {
    exit(json_encode(['fehler' => 'OpenAI-Fehler: '.($json['error']['message'] ?? "HTTP $status")]));
}

echo json_encode(['antwort' => $json['choices'][0]['message']['content'] ?? '']);
