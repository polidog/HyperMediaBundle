<?php

declare(strict_types=1);

namespace Polidog\HypermediaBundle;

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
        $referenceType = RouterInterface::ABSOLUTE_PATH;
        if ($this->enableFullPath) {
            $referenceType = RouterInterface::ABSOLUTE_URL;
        }

        return $this->router->generate($name, $parameters, $referenceType);
    }

    public function pathResolve(string $path, array $parameters): string
    {
        return implode('/', array_map(static function (string $target) use ($parameters) {
            if (preg_match('/\{(.+)\}/', $target, $matches)) {
                return $parameters[$matches[1]] ?? $target;
            }
        }, explode('/', $path)));
    }
}
