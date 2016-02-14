<?php

namespace Lemmon\Router;

class SimpleRouter extends AbstractRouter
{


    public function match(...$args)
    {
        if (!$args) {
            return FALSE;
        }
        // pattern
        $pattern = (isset($args[0]) and is_string($args[0])) ? array_shift($args) : NULL;
        // masks
        $mask = (isset($args[0]) and is_array($args[0])) ? array_shift($args) : [];
        // match
        if (!$pattern or $this->matchPattern($pattern, $mask, $matches)) {
            if (isset($args[0]) and is_callable($args[0]) and FALSE !== ($res = $args[0]($this, $matches ?? []))) {
                return $res;
            }
            return TRUE;
        }
        // n/a
        return FALSE;
    }
}