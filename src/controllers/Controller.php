<?php

namespace App\controllers;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller {
    private const VIEWS_PATH = "\\src\\views";
    private const NOT_FOUND_VIEW_PATH = "404_not_found.html.twig";

    final protected function parseView(string $viewName, array $params = []) : string {
        $loader = new FilesystemLoader(DOCUMENT_ROOT . self::VIEWS_PATH);
        $twig = new Environment($loader);
        $session = $_SESSION['user'] ?? null;
        if (! is_null($session)) {
            $params['session'] = $session;
        }
        return $twig->render($viewName, $params);
    }

    final public function getNotFoundView() : string {
        return $this->parseView(self::NOT_FOUND_VIEW_PATH);
    }
}