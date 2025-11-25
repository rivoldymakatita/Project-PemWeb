<?php

namespace Core;

class Controller
{
    protected $view;
    protected $models = [];

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Load model
     */
    protected function loadModel($modelName)
    {
        $modelClass = "App\\Models\\{$modelName}";
        
        if (!isset($this->models[$modelName])) {
            $this->models[$modelName] = new $modelClass();
        }
        
        return $this->models[$modelName];
    }

    /**
     * Render view
     */
    protected function render($view, $data = [])
    {
        return $this->view->render($view, $data);
    }

    /**
     * Redirect to another page
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get request data
     */
    protected function getRequest($key = null, $default = null)
    {
        $data = array_merge($_GET, $_POST);
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }

    /**
     * Check if request method is POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request method is GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Validate input
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $rules_array = explode('|', $fieldRules);
            
            foreach ($rules_array as $rule) {
                $this->validateField($field, $data[$field] ?? null, $rule, $errors);
            }
        }
        
        return $errors;
    }

    private function validateField($field, $value, $rule, &$errors)
    {
        if ($rule === 'required' && empty($value)) {
            $errors[$field] = "{$field} is required";
        }
        
        if (strpos($rule, 'min:') === 0 && strlen($value) < (int)substr($rule, 4)) {
            $errors[$field] = "{$field} must be at least " . substr($rule, 4) . " characters";
        }
        
        if (strpos($rule, 'max:') === 0 && strlen($value) > (int)substr($rule, 4)) {
            $errors[$field] = "{$field} must not exceed " . substr($rule, 4) . " characters";
        }
        
        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$field] = "{$field} must be a valid email";
        }
    }
}