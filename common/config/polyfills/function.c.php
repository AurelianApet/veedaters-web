<?php
/**
 * This file is part of the debug_backtrace library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @license http://opensource.org/licenses/MIT MIT
 * @author Puneet Sethi
 */
if (!function_exists('c')) {
    /**
     * Prints the formated human readable result of a print_r method.
     *
     * Optionally, prints the type of the expression argument if supplied with 
     * the second argument as true
     *
     * @param mixed[array|object|string|integer|boolean] $exp Any expression of any 
     *                                                  data type.
     * @param mixed $dump   The second argument of the method if supplied with bool
     *                      value true, the print_r will be replaced with var_dump 
     *                      php method.
     * 
     * @return mixed
     */
    function c($expression = null, $dump = false)
    {
        echo '<pre>';
        (!$dump ? print_r($expression) : var_dump($expression));
        echo '</pre>';
    }

}