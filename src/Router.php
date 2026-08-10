<?php

declare(strict_types=1);

namespace App;

final class Router
{
    public function dispatch(string $path): string
    {
        // Platzhalter: aktuell wird der Pfad direkt zurückgegeben.
        return $path;
    }
}
