<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        $viewPath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        if (! file_exists($viewPath)) {
            http_response_code(500);
            echo 'Vista no encontrada.';
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__, 2) . '/views/' . $layout . '.php';

        if (! file_exists($layoutPath)) {
            http_response_code(500);
            echo 'Layout no encontrado.';
            return;
        }

        require $layoutPath;
    }
}
