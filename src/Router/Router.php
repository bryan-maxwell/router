<?php

namespace Lemmon\Router;

class Router extends AbstractRouter
{
    private $_routes = [];
    private $_matches;
    private $_defaults;
    private $_name;


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
                if (($callback and FALSE !== ($res = $callback($this, $matches ?? []))) or !$callback) {
                    $this->_matches = $matches;
                    #$this->_defaults = $defaults;
                    $this->_name = $name;
                    return TRUE;
                }
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


    function getName()
    {
        return $this->_name;
    }
}