<?php

namespace App\Providers;

class View{
    /**
     * Template being rendered.
     */
    protected $templates = null;


    /**
     * Initialize a new view context.
     */
    public function __construct($templates) {
        $this->templates = $templates;
    }

    /**
     * Safely escape/encode the provided data.
     */
    public function e($data) {
        return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render the template, returning it's content.
     * @param array $data Data made available to the view.
     * @return string The rendered template.
     */
    public function render($view = '', Array $data = []) {
        extract($data);

        return include $this->templates . $view;
    }
}