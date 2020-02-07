<?php

declare(strict_types=1);

namespace Polidog\HypermediaBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class UrlGenerator
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var bool
     */
    private $enableFullPath;

    public function __construct(RouterInterface $router, bool $enableFullPath)
    {
        $this->router = $router;
        $this->enableFullPath = $enableFullPath;
    }

    public function nameResolve(string $name, array $parameters): string
    {
        $route = $this->router->getRouteCollection()->get($name);
        $routeParameters = [];
        foreach ($route->getRequirements() as $key) {
            if ($parameters[$key]) {
                $routeParameters[$key] = $parameters;
            }
        }

        return $this->router->generate($name, $routeParameters, $this->referenceType());
    }

    public function pathResolve(string $path, array $parameters): string
    {
        try {
            $route = $this->router->match($path);

            return $this->router->generate($route['_route'], $parameters, $this->referenceType());
        } catch (ResourceNotFoundException $e) {
            $routeParameters = array_filter($parameters, [$this, 'filterSystemQuery'], ARRAY_FILTER_USE_KEY);

            return implode('/', array_map(function (string $path) use ($routeParameters) {
                if (preg_match('/\{(.+)\}/', $path, $matches)) {
                    if (isset($routeParameters[$matches[1]])) {
                        return $routeParameters[$matches[1]];
                    }
                }

                return $path;
            }, explode('/', $path)));
        }
    }

    public function selfResource(Request $request): string
    {
        $routeParameters = array_filter($request->attributes->all(), [$this, 'filterSystemQuery'], ARRAY_FILTER_USE_KEY);

        return $this->router->generate($request->attributes->get('_route'), $routeParameters, $this->referenceType());
    }

    private function referenceType(): int
    {
        if ($this->enableFullPath) {
            return RouterInterface::ABSOLUTE_URL;
        }

        return RouterInterface::ABSOLUTE_PATH;
    }

    private function filterSystemQuery(string $key): bool
    {
        return 0 !== strpos($key, '_');
    }
}
