<?php

namespace App\controllers;


class Controller {
    private const VIEWS_PATH = "\\src\\views";
    private const NOT_FOUND_VIEW_PATH = "404_not_found.html";

    final protected function simpleParseView(string $viewName, array $params = []) : string {
        $path = DOCUMENT_ROOT . self::VIEWS_PATH . "\\" . $viewName;
        if (empty($viewName)
            || !file_exists($path)) {
            throw new \Exception("View requested does not exists. ViewName: {$viewName}\n Path: {$path}");
        }
        ob_start();
        include_once $path;
        $view = ob_get_contents();
        ob_end_clean();

        foreach ($params as $target => $replacement) {
            $view = str_replace("{{{$target}}}", $replacement, $view);
        }
        return $view;
    }

    final public function getNotFoundView() : string {
        return $this->parseView(self::NOT_FOUND_VIEW_PATH);
    }
}