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
        
        // Skip routing for static files (assets, uploads, images, etc.)
        $staticExtensions = ['.css', '.js', '.jpg', '.jpeg', '.png', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot', '.pdf', '.doc', '.docx', '.xls', '.xlsx'];
        $staticPaths = ['/assets/', '/uploads/', '/favicon.ico'];
        
        foreach ($staticPaths as $staticPath) {
            if (strpos($uri, $staticPath) === 0) {
                // Let web server handle static files
                return;
            }
        }
        
        foreach ($staticExtensions as $ext) {
            if (substr($uri, -strlen($ext)) === $ext) {
                // Let web server handle static files
                return;
            }
        }
        
        // Handle method override for PUT/PATCH/DELETE (browsers don't support these natively)
        // Store raw input for later use (php://input can only be read once)
        $rawInput = null;
        if ($method === 'POST') {
            // Check form data first
            if (isset($_POST['_method'])) {
                $method = strtoupper($_POST['_method']);
            }
            // Also check JSON body for method override
            elseif (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                $rawInput = file_get_contents('php://input');
                $jsonInput = json_decode($rawInput, true);
                if (isset($jsonInput['_method'])) {
                    $method = strtoupper($jsonInput['_method']);
                }
                // Store raw input for controllers to use
                $GLOBALS['_RAW_INPUT'] = $rawInput;
            }
        } elseif (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
            // Store raw input for PUT/PATCH/DELETE requests
            $rawInput = file_get_contents('php://input');
            $GLOBALS['_RAW_INPUT'] = $rawInput;
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
            
            // Debug for messages routes
            if (strpos($route['path'], 'messages') !== false) {
                $testMatch = preg_match($pattern, $uri, $testMatches);
                error_log("Testing route: {$route['path']} -> Pattern: {$pattern} -> Match: " . ($testMatch ? 'YES' : 'NO'));
                if ($testMatch) {
                    error_log("Matched params: " . print_r($testMatches, true));
                }
            }
            
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->params = $matches;
                
                $controllerName = $route['controller'];
                $actionName = $route['action'];
                
                // Debug: Log route match for messages
                if (strpos($route['path'], 'messages') !== false) {
                    error_log("Router matched: " . $route['path'] . " -> " . $controllerName . "::" . $actionName);
                    error_log("Router params: " . print_r($this->params, true));
                }
                
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
            $pattern = $this->convertToRegex($route['path']);
            $matched = preg_match($pattern, $uri, $testMatches) ? '✓ MATCH' : '';
            echo htmlspecialchars($route['method'] . ' ' . $route['path']) . " " . $matched . "<br>";
        }
    }
    
    private function convertToRegex($path) {
        // Simple approach: replace {param} with regex, escape the rest
        $pattern = $path;
        // Replace {param} placeholders with regex groups (allow alphanumeric and dashes)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9\-]+)', $pattern);
        // Escape forward slashes and other special chars, but preserve our capture groups
        $pattern = str_replace('/', '\/', $pattern);
        return '#^' . $pattern . '$#';
    }
}

