<?php

if (!function_exists('set_active')) {
    /**
     * Helper function for setting active menu classes
     * 
     * @param string|array $route
     * @return string
     */
    function set_active($route)
    {
        if (is_array($route)) {
            return in_array(request()->path(), $route) ? 'active' : '';
        }
        return request()->path() == $route ? 'active' : '';
    }
} 