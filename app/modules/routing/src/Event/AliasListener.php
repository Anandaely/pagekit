<?php

namespace Pagekit\Routing\Event;

use Pagekit\Event\Event;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Routing\Route;
use Pagekit\Routing\Routes;
use Symfony\Component\Routing\RouteCollection;

class AliasListener implements EventSubscriberInterface
{
    protected $routes;

    /**
     * Constructor.
     *
     * @param Routes $routes
     */
    public function __construct(Routes $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Adds all aliases for this route.
     *
     * @param Event           $event
     * @param Route           $route
     * @param RouteCollection $routes
     */
    public function onConfigureRoute($event, $route, $routes)
    {
        $name = $route->getName();

        $aliases = array_filter($this->routes->getAliases(), function ($alias) use ($name) {
            return $name == $alias[0] || $name == strtok($alias[0], '?');
        });

        if (!$aliases) {
            return;
        }

        $variables = $route->compile()->getPathVariables();

        foreach ($aliases as $alias) {
            list($name, $path, $defaults) = $alias;

            // TODO: is this still needed?
            $params = [];
            if ($query = substr(strstr($name, '?'), 1)) {
                parse_str($query, $params);
            }

            $routes->add($name, new Route($path, array_merge($route->getDefaults(), $params, $defaults, ['_variables' => $variables])));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'route.configure' => ['onConfigureRoute', -16]
        ];
    }
}