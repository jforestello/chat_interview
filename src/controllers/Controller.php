<?php

namespace App\controllers;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller {
    private const VIEWS_PATH = "\\src\\views";
    private const NOT_FOUND_VIEW_PATH = "404_not_found.html";

    final protected function parseView(string $viewName, array $params = []) : string {
        $loader = new FilesystemLoader(DOCUMENT_ROOT . self::VIEWS_PATH);
        $twig = new Environment($loader);
        return $twig->render($viewName, $params);
    }

    final public function getNotFoundView() : string {
        return $this->parseView(self::NOT_FOUND_VIEW_PATH);
    }
}