<?php

declare(strict_types=1);

namespace App;

/** Minimaler Router-Entwurf; die aktive Routenzuordnung steht derzeit in index.php. */
final class Router
{
    /** Gibt den normalisierten Pfad als Platzhalter für die spätere Dispatch-Logik zurück. */
    public function dispatch(string $path): string
    {
        // Platzhalter: aktuell wird der Pfad direkt zurückgegeben.
        return $path;
    }
}
