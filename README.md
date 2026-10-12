# PHP lernen 🐘

Ein kleiner PHP-Spielplatz für absolute Anfänger, als Vorbereitung auf Laravel.
Jede Lektion hat eine kurze Erklärung, einen Code-Editor mit **▶ Ausführen**-Knopf
und einen KI-Tutor, der Fehler auf Deutsch erklärt.

## Lektionen

0. Einführung: Was ist PHP?
1. Hallo Welt!
2. Variablen (string, int, bool)
3. Texte zusammenbauen
4. Rechnen
5. Vergleichen (`=`, `==`, `<`, `>`, `<=`, `>=`)
6. Würfeln und Zwischenspeichern
7. Entscheiden mit `if`
8. Wiederholen (`for`)
9. Eigene Befehle (Funktionen)
10. Mini-Spiel: Würfel-Duell
11. Ausblick: Was ist Laravel?

## Starten

Den Ordner mit [Laravel Herd](https://herd.laravel.com) (oder einem anderen lokalen PHP-Server) ausliefern
und `index.php` im Browser öffnen, z.B. `http://lars.test`.

Ohne Herd geht es auch mit dem eingebauten PHP-Server:

```bash
php -S 127.0.0.1:8000
```

## KI-Tutor einrichten (optional)

```bash
cp config.example.php config.php
```

Dann in `config.php` einen OpenAI-API-Key eintragen. `config.php` steht in der `.gitignore`.

## Dateien

| Datei | Wofür |
|---|---|
| `index.php` | Die Lernseite |
| `lektionen.php` | Erklärungen und Aufgaben aller Lektionen |
| `lernen-1.php` … `lernen-10.php` | Startcode jeder Lektion (funktioniert auch einzeln im Browser) |
| `ausfuehren.php` | Führt den Code aus dem Editor aus |
| `ki-hilfe.php` | Fragt die OpenAI-API nach Erklärungen |

> ⚠️ `ausfuehren.php` führt beliebigen PHP-Code aus und ist deshalb nur von `127.0.0.1` erreichbar.
> Nur lokal benutzen, niemals auf einem öffentlichen Server!
