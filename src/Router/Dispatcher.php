<?php

namespace Lemmon\Router;

class Dispatcher extends AbstractRouter
{
    private $_routes = [];
    private $_matches = [];
    private $_defaults = [];


    public function match(...$args)
    {
        $this->_routes[] = $args;
    }


    function dispatch()
    {
        foreach ($this->_routes as $route) {
            if ($route) {
                // pattern
                $pattern = (isset($route[0]) and is_string($route[0])) ? array_shift($route) : NULL;
                // masks
                $mask = (isset($route[0]) and is_array($route[0])) ? array_shift($route) : [];
                // match
                if (!$pattern or $this->matchPattern($pattern, $mask, $matches, $defaults)) {
                    if ((isset($route[0]) and is_callable($route[0]) and FALSE !== ($res = $route[0]($this, $matches ?? []))) or $res = TRUE) {
                        $this->_matches = $matches;
                        $this->_defaults = $defaults;
                        return $res;
                    }
                }
            }
        }
    }


    function __get($name)
    {
        return $this->_matches[$name] ?? $this->_defaults[$name] ?? NULL;
    }
}