<?php

namespace Core;

class View
{
    protected $viewPath = __DIR__ . '/../app/Views/';
    protected $data = [];

    /**
     * Render view file
     */
    public function render($view, $data = [])
    {
        $this->data = $data;
        
        $filePath = $this->viewPath . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($filePath)) {
            throw new \Exception("View file not found: {$filePath}");
        }
        
        extract($data);
        ob_start();
        include $filePath;
        return ob_get_clean();
    }

    /**
     * Echo data
     */
    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Include partial/component
     */
    public function include($view, $data = [])
    {
        echo $this->render($view, array_merge($this->data, $data));
    }

    /**
     * Escape HTML
     */
    public function escape($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate URL
     */
    public function url($path = '')
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
    }

    /**
     * Check if variable exists
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }
}