<?php

namespace Straube\Common\Utils;

class StringUtils
{
    
    /**
     * 
     * @param string $key
     * @return string
     */
    public static function jsonKeyToClassProperty($key)
    {
        return str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $key))));
    }
    
}
