<?php

declare(strict_types=1);

namespace App;

final class View
{
    public static function render(string $templatePath, array $data = []): void
    {
        // Stellt Template-Variablen als lokale Variablen bereit.
        extract($data, EXTR_SKIP);
        require $templatePath;
    }
}
