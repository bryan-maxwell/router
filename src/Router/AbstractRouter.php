<?php

namespace Lemmon\Router;

abstract class AbstractRouter implements RouterInterface, \ArrayAccess
{
    private $_options = [];
    private $_root;
    private $_base;
    private $_path;
    private $_query;
    private $_params = [];
    private $_definedLinks = [];
    private $_definedClosures = [];


    function __construct(array $options = [])
    {
        $o = array_replace($this->_options, $options);
        preg_match('#^(.*/)([^/]+\.php)$#', $_SERVER['SCRIPT_NAME'], $m);
        $this->_root = $o['root'] ?? $m[1];
        $this->_base = $o['base'] ?? $m[2] . '/';
        $this->_path = $o['path'] ?? substr(@$_SERVER['PATH_INFO'], 1);
        $this->_params = $this->_path ? explode('/', $this->_path) : [];
        $this->_options = $o;
    }


    public function offsetExists($offset)
    {
        return is_int($offset) and isset($this->_params[$offset]);
    }


    public function offsetGet($offset)
    {
        return self::offsetExists($offset) ? $this->_params[$offset] : FALSE;
    }


    public function offsetSet($offset, $value) { return FALSE; }
    public function offsetUnset($offset) { return FALSE; }


    public function getParams()
    {
        return $this->_params;
    }


    public function getRoot($keepBase = FALSE, $keepIndex = FALSE)
    {
        if (FALSE !== $this->_root) {
            return $this->_root . ($keepBase ? (($keepIndex or 'index.php/' != $this->_base) ? $this->_base : '') : '');
        } else {
            return $this->_base . $this->relative($this->_path, './');
        }
    }


    public function getHome(): string
    {
        return isset($this->_definedLinks[':home'])
            ? $this->to($this->_definedLinks[':home'])
            : $this->getRoot(TRUE)
            ;
    }


    public function getBase(): string
    {
        return $this->_base;
    }


    public function getPath(): string
    {
        return $this->_path;
    }


    public function to($link, ...$args): string
    {
        // validate link
        while (is_string($link) and ':' == $link{0}) {
            switch ($link) {
                case ':self': return $this->getSelf();
                case ':root': return $this->getRoot();
                case ':home': return $this->getHome();
                default:
                    if (isset($this->_definedLinks[$link])) {
                        $link = $this->_definedLinks[$link];
                    } else {
                        trigger_error(sprintf('Route not defined (%s)', $link));
                        return '#';
                    }
            }
        }
        // chained arguments
        $link = preg_replace_callback('#%(?<from>\d+)(?<sep>.)?\.\.(%?(?<to>\d+))?#', function($m) use ($args) {
            return '%' . join(@$m['sep'] . '%', range($m['from'], @$m['to'] ?: count($args)));
        }, $link);
        // match link variables with params
        $link = preg_replace_callback('#((?<!\\\)?<keep>@)?({((?<match>[\w\.]+)|%(?<arg>\d+))(=(?<default>\w+))?}|%(?<arg0>\d+))#', function($m) use ($args){
            // argument
            $res = !empty($args) ? $args[(($i = (int)@$m['arg0'] or $i = (int)@$m['arg']) and isset($args[$i - 1])) ? $i - 1 : 0] : '';
            // match
            if (!empty($m['match'])) {
                $_res = $res;
                $_match = explode('.', $m['match']);
                foreach ($_match as $_m) {
                    if (is_array($_res) and isset($_res[$_m])) {
                        $_res = $_res[$_m];
                    } elseif (is_object($_res) and $_res instanceof \ArrayAccess and isset($_res[$_m])) {
                        $_res = $_res[$_m];
                    } elseif (is_object($_res) and isset($_res->{$_m})) {
                        $_res = $_res->{$_m};
                    } elseif (is_object($_res) and method_exists($_res, 'get' . $_m)) {
                        $_res = $_res->{'get' . $_m}();
                    } else {
                        $_res = '';
                        break;
                    }
                }
                if (is_string($_res) or is_numeric($_res)) {
                    $res = strval($_res);
                } elseif (is_object($_res) and method_exists($_res, '__toString')) {
                    $res = $_res->__toString();
                } else {
                    $res = '';
                }
            }
            // res
            if (is_object($res) and method_exists($res, '__toString')) {
                $res = $res->__toString();
            }
            // default
            if (!empty($m['default']) and $m['default'] == $res) {
                $res = NULL;
            }
            //
            return ((is_string($res) or is_int($res)) and !empty($res)) ? $res : (isset($m['keep']) ? $m['keep'] : '');
        }, $link);
        // paste current route params
        if (FALSE !== strpos($link, '@')) {
            $link = explode('/', $link);
            foreach ($link as $i => $_param) {
                if ($_param and '@' == $_param{0}) {
                    $link[$i] = isset($this->_params[$i]) ? $this->_params[$i] : '';
                }
            }
            $link = join('/', $link);
            $link = str_replace('\\@', '@', $link);
        }
        // root & base
        if ('' == $link or ('/' !== $link{0} and FALSE === strpos($link, '://'))) {
            if (FALSE !== $this->_root) {
                if ('./' == substr($link, 0, 2)) {
                    $link = $this->_root . $this->normalize(substr($link, 2));
                } else {
                    $link = $this->_root . $this->_base . $this->normalize($link);
                }
            } else {
                $link = $this->_base . ($this->relative($this->_path, $link));
            }
        }
        //
        return str_replace('/./', '/', $link);
    }


    protected function normalize(string $uri): string
    {
        do {
            $uri = preg_replace('#(^|/)(?!\.\.)[^/]+/\.\.#', '', $uri, -1, $n);
        } while ($n);
        $uri = ltrim($uri, '/');
        $uri = str_replace('/./', '/', $uri);
        return $uri;
    }


    protected function relative(string $from, string $link): string
    {
        $from = explode('/', $from);
        $link = explode('/', $link);
        array_pop($from);
        while ($link and '.' == $link[0]) {
            array_shift($link);
        }
        while ($from and $link and $from[0] == $link[0]) {
            array_shift($from);
            array_shift($link);
        }
        $link = array_pad($link, -(count($from) + count($link)), '..');
        return join($link, '/');
    }


    protected function matchPattern(string $pattern, array $mask = [], &$matches = [], &$defaults = [])
    {
        // match route
        $pattern = strtr($pattern, [
            ')!' => ')',
            ')'  => ')?',
            '.'  => '\.',
            '*'  => '.*',
        ]);
        $pattern = preg_replace_callback('#{(?<name>(\w+))(:(?<pattern>.+)(:(?<length>.+))?)?(=(?<default>.+))?}#U', function($m) use ($mask, &$defaults){
            if (@$m['pattern']) {
                switch ($m['pattern']) {
                    case 'num':      $_pattern = '\d'; break;
                    case 'alpha':    $_pattern = '[a-z\-]'; break;
                    case 'alphanum': $_pattern = '[\w\-]'; break;
                    case 'word':     $_pattern = '[a-z]([\w\-]+)?'; break;
                    case 'hex':      $_pattern = '[0-9a-z]'; break;
                    default:         $_pattern = $m['pattern'];
                }
                $_pattern .= @$m['length'] ? "{{$m['length']}}" : '+';
            } elseif (array_key_exists($m['name'], $mask)) {
                $_pattern = $mask[$m['name']];
            } else {
                $_pattern = '[^/]+';
            }
            if (isset($m['default'])) {
                @$defaults[$m['name']] = $m['default'];
            }
            return "(?<{$m['name']}>{$_pattern})";
        }, $pattern);
        return preg_match("#^{$pattern}$#i", $this->_path, $matches) ? TRUE : FALSE;
    }
}