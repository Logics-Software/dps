<?php
class Router {
    private $routes = [];
    private $params = [];
    
    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    public function get($path, $controller, $action) {
        $this->add('GET', $path, $controller, $action);
    }
    
    public function post($path, $controller, $action) {
        $this->add('POST', $path, $controller, $action);
    }
    
    public function put($path, $controller, $action) {
        $this->add('PUT', $path, $controller, $action);
    }

    public function patch($path, $controller, $action) {
        $this->add('PATCH', $path, $controller, $action);
    }
    
    public function delete($path, $controller, $action) {
        $this->add('DELETE', $path, $controller, $action);
    }
    
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Handle method override for PUT/DELETE (browsers don't support these natively)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Normalize URI - remove trailing slash except for root
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
        
        // Handle root route
        if ($uri === '/' && $method === 'GET') {
            if (Auth::check()) {
                header('Location: /dashboard');
            } else {
                header('Location: /login');
            }
            exit;
        }
        
        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['path']);
            
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->params = $matches;
                
                $controllerName = $route['controller'];
                $actionName = $route['action'];
                
                if (!class_exists($controllerName)) {
                    http_response_code(500);
                    die("Error: Controller class '{$controllerName}' not found.");
                }
                
                $controller = new $controllerName();
                
                if (!method_exists($controller, $actionName)) {
                    http_response_code(500);
                    die("Error: Method '{$actionName}' not found in controller '{$controllerName}'.");
                }
                
                call_user_func_array([$controller, $actionName], $this->params);
                return;
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found<br>";
        echo "URI: " . htmlspecialchars($uri) . "<br>";
        echo "Method: " . htmlspecialchars($method) . "<br>";
        echo "<br>Available routes:<br>";
        foreach ($this->routes as $route) {
            echo htmlspecialchars($route['method'] . ' ' . $route['path']) . "<br>";
        }
    }
    
    private function convertToRegex($path) {
        // Simple approach: replace {param} with regex, escape the rest
        $pattern = $path;
        // Replace {param} placeholders with regex groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $pattern);
        // Escape forward slashes and other special chars, but preserve our capture groups
        $pattern = str_replace('/', '\/', $pattern);
        return '#^' . $pattern . '$#';
    }
}

