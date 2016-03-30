<?php

namespace Lemmon\Router;

class Router extends AbstractRouter
{
    private $_routes = [];
    private $_matches;
    private $_defaults;
    private $_name;
    private $_self;


    function match(...$args)
    {
        $route = $this->matchParams(...$args);
        if ($route[4] and $route[1]) {
            $def = $route[1];
            $def = preg_replace('#\(\?<\w+>#', '', $def);
            $def = strtr($def, ['(' => '', ')!' => '', ')' => '']);
            $this->define($route[4], $def);
        }
        $this->_routes[] = $route;
    }


    function dispatch()
    {
        foreach ($this->_routes as $route) {
            list($method, $pattern, $mask, $callback, $name) = $route;
            if ($this->matchPattern($method, $pattern, $mask, $matches, $defaults)) {
                $this->_matches = $matches;
                $this->_defaults = $defaults;
                $this->_name = $name;
                $this->_self = $route[1];
                if ($callback) {
                    $arg = [];
                    $ref = new \ReflectionFunction($callback);
                    foreach (array_slice($ref->getParameters(), 1) as $x) {
                        $arg[] = $matches[$x->getName()] ?? $defaults[$x->getName()] ?? NULL;
                    }
                    if (FALSE === $callback($this, ...$arg)) {
                        $this->_matches = NULL;
                        $this->_defaults = NULL;
                        $this->_name = NULL;
                        $this->_self = NULL;
                        return;
                    }
                }
                return TRUE;
            }
        }
    }


    function __isset($name)
    {
        return isset($this->_matches[$name]) || isset($this->_defaults[$name]);
    }


    function __get($name)
    {
        return $this->_matches[$name] ?? $this->_defaults[$name] ?? NULL;
    }


    public function getName()
    {
        return $this->_name;
    }


    public function getSelf(...$args): string
    {
        return $this->to($this->_self, ...$args);
    }
}