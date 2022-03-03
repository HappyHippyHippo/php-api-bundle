<?php

namespace Hippy\Api\Model\Controller\Index;

use Hippy\Model\Model;
use Symfony\Component\Routing\RouteCollection;

class IndexResponse extends Model
{
    /** @var array<string, string> */
    protected array $routes = [];

    /**
     * @param string $name
     * @param string $version
     * @param RouteCollection $routes
     */
    public function __construct(
        protected string $name,
        protected string $version,
        RouteCollection $routes,
    ) {
        parent::__construct();

        foreach ($routes->all() as $name => $route) {
            if ('_preview_error' != $name) {
                $path = $route->getPath();
                foreach ($route->getMethods() as $method) {
                    $this->routes[$name] = sprintf('[%s] %s', $method, $path);
                }
            }
        }
        ksort($this->routes);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return array<string, string>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
