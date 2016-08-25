<?php

namespace olcms\Foundation;

/**
 * container by twittee
 * http://twittee.org/
 */
class Container
{
    protected $s = [];
    
    public function __set($k, $c)
    {
        $this->s[$k] = $c;
    }
    
    public function __get($k)
    {
        return $this->s[$k]($this);
    }
    
}
