<?php
require_once ROOT . "functions/utilities.php";

class Router
{
    private array $routes = [];

    /**
     * Register a new route or multiple routes.
     *
     * @param string|array $paths
     * @param string $handler
     * @param array|string $methods
     * @param bool $authRequired
     */
    public function register($paths, string $handler, $methods = 'GET', bool $authRequired = false): void
    {
        // Normalize methods to an array
        $methods = is_array($methods) ? $methods : [$methods];

        // Normalize paths to an array
        $paths = is_array($paths) ? $paths : [$paths];

        foreach ($paths as $path) {
            $this->routes[$path] = [
                'handler' => $handler,
                'methods' => $methods,
                'authRequired' => $authRequired
            ];
        }
    }

    /**
     * Dispatch the request to the appropriate controller and action.
     *
     * @param string|null $path
     */
    public function dispatch(string $path = null): void
    {
        $path = $path ?? PATH ?? "/";
        $matchedRoute = $this->matchRoute($path);

        if (!$matchedRoute) {
            utilities::apiMessage("Not Found", 404);
            exit;
        }

        [$route, $parameters] = $matchedRoute;
        $handler = $route['handler'];
        $methods = $route['methods'];
        $authRequired = $route['authRequired'];

        // Check if the HTTP method is allowed
        if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) {
            utilities::apiMessage("Method not allowed", 405);
            exit;
        }

        // Check if authentication is required
        if ($authRequired) {
            $this->checkAuth();
        }

        [$controllerName, $actionDefinition] = explode("@", $handler);

        // Extract action and additional parameters
        $actionParts = explode('|', $actionDefinition);
        $action = array_shift($actionParts);

        // Load the controller file
        $controllerFile = "controllers/$controllerName.php";
        if (!file_exists($controllerFile)) {
            utilities::apiMessage("Controller not found", 404);
            exit;
        }
        require_once $controllerFile;

        // Instantiate the controller
        $className = ucfirst($controllerName);
        if (!class_exists($className)) {
            utilities::apiMessage("Controller class not found", 500);
            exit;
        }
        $controller = new $className();

        try {
            // Call the action with parameters
            $response = count($parameters) > 0
                ? $controller->$action(...$parameters)
                : $controller->$action();

            echo $response;
        } catch (Throwable $e) {
            error_log($e);
            utilities::apiMessage("Int An error occurred", 500);
        }
    }

    /**
     * Match the given path to a registered route.
     *
     * @param string $path
     * @return array|null
     */
    private function matchRoute(string $path): ?array
    {
        foreach ($this->routes as $route => $details) {
            $routePattern = preg_replace('/:\w+/', '([^/]+)', $route); // Replace :param with regex
            $routePattern = '#^' . $routePattern . '$#'; // Add start and end delimiters

            if (preg_match($routePattern, $path, $matches)) {
                array_shift($matches); // Remove the full match
                $this->populateGetParameters($route, $matches);
                return [$details, $matches];
            }
        }

        return null;
    }

    /**
     * Populate $_GET with dynamic parameters.
     *
     * @param string $route
     * @param array $matches
     */
    private function populateGetParameters(string $route, array $matches): void
    {
        $routeParts = explode('/', $route);
        foreach ($routeParts as $index => $part) {
            if (strpos($part, ':') === 0) { // Check if it's a dynamic parameter
                $paramName = substr($part, 1); // Remove the colon
                $_GET[$paramName] = $matches[$index - count($routeParts) + count($matches)];
            }
        }
    }

    /**
     * Resolve additional parameters for the action.
     *
     * @param array $actionParts
     * @return array
     */
    private function resolveParameters(array $actionParts): array
    {
        $parameters = [];
        foreach ($actionParts as $part) {
            if (preg_match("/^'(.*)'$/", $part, $matches)) {
                $parameters[] = $matches[1]; // Extract string values without quotes
            } elseif (isset($GLOBALS[$part])) {
                $parameters[] = $GLOBALS[$part]; // Use global variable values if defined
            }
        }
        return $parameters;
    }

    /**
     * Check if the user is authenticated.
     */
    private function checkAuth(): void
    {
        $user = new user();
        $user->set_user(); // This will terminate the request if the user is not authenticated
    }
}