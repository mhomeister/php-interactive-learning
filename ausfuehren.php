<?php
// Führt den Code aus dem Editor aus und gibt Ausgabe, Fehler und Warnungen als JSON zurück.
// ACHTUNG: Hier wird beliebiger PHP-Code ausgeführt. Deshalb nur von diesem Rechner aus erlaubt.

if (! in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Nur lokal erlaubt.');
}

$code = json_decode(file_get_contents('php://input'), true)['code'] ?? '';

final class Spielplatz
{
    public static array $warnungen = [];
    public static ?array $fehler = null;
    public static float $start = 0;
}

const SPIELPLATZ_FEHLERNAMEN = [
    E_WARNING => 'Warning',
    E_NOTICE => 'Notice',
    E_DEPRECATED => 'Deprecated',
    E_USER_WARNING => 'Warning',
    E_USER_NOTICE => 'Notice',
    E_USER_ERROR => 'Error',
    E_ERROR => 'Fatal error',
    E_PARSE => 'Parse error',
    E_COMPILE_ERROR => 'Fatal error',
    E_CORE_ERROR => 'Fatal error',
];

// Server-Pfade aus Meldungen entfernen, die verwirren nur
$aufraeumen = fn (string $meldung) => preg_replace('/,? called in .*? on line \d+/', '', $meldung);

// Zeilennummern zählen nur, wenn der Fehler im Code aus dem Editor passiert ist
$zeileImEditor = fn (string $datei, int $zeile) => str_contains($datei, "eval()'d code") ? $zeile : null;

set_error_handler(function (int $nummer, string $meldung, string $datei, int $zeile) use ($zeileImEditor, $aufraeumen) {
    Spielplatz::$warnungen[] = [
        'typ' => SPIELPLATZ_FEHLERNAMEN[$nummer] ?? 'Warning',
        'meldung' => $aufraeumen($meldung),
        'zeile' => $zeileImEditor($datei, $zeile),
    ];

    return true;
});

// Läuft immer ganz am Ende, auch nach exit() oder einem schweren Fehler (z.B. Endlosschleife)
register_shutdown_function(function () use ($zeileImEditor) {
    $ausgabe = '';
    while (ob_get_level() > 0) {
        $ausgabe = ob_get_clean().$ausgabe;
    }

    $letzter = error_get_last();
    if (Spielplatz::$fehler === null && $letzter && in_array($letzter['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR], true)) {
        Spielplatz::$fehler = [
            'typ' => SPIELPLATZ_FEHLERNAMEN[$letzter['type']],
            'meldung' => $letzter['message'],
            'zeile' => $zeileImEditor($letzter['file'], $letzter['line']),
        ];
    }

    if (strlen($ausgabe) > 200_000) {
        $ausgabe = substr($ausgabe, 0, 200_000)."\n\n[... Ausgabe abgeschnitten, sie war zu lang]";
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ausgabe' => $ausgabe,
        'fehler' => Spielplatz::$fehler,
        'warnungen' => Spielplatz::$warnungen,
        'dauer_ms' => round((microtime(true) - Spielplatz::$start) * 1000, 1),
    ], JSON_INVALID_UTF8_SUBSTITUTE);
});

ini_set('display_errors', '0');
ini_set('memory_limit', '64M');
set_time_limit(3);

Spielplatz::$start = microtime(true);
ob_start();

try {
    // Eigene Funktion drumherum, damit die Variablen aus dem Editor hier nichts kaputt machen.
    // Das PHP-Ende-Zeichen vorne dran, damit der Code genau wie eine .php-Datei mit <?php beginnen darf.
    (static function () {
        eval('?>'.func_get_arg(0));
    })($code);
} catch (Throwable $e) {
    Spielplatz::$fehler = [
        'typ' => (new ReflectionClass($e))->getShortName(),
        'meldung' => $aufraeumen($e->getMessage()),
        'zeile' => $zeileImEditor($e->getFile(), $e->getLine()),
    ];
}
