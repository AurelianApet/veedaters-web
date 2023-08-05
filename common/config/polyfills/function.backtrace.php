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
if (!function_exists('backtrace')) {
    /**
     * Returns the values from a debug_backtrace of the input array, identified by
     * the array key.
     *
     * Optionally, you may provide an $debugKey to index the values in the returned
     * array by the values from the $debugKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values. This will generally be a return value
     *                     of a results from php debug_backtrace method.
     * @param mixed $debugKey The array of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array
     *                         returned from the result of debug_backtrace.
     * @return array
     */
    function backtrace()
    {     
        $input = debug_backtrace();        
        $resultArray = array();
        foreach($input as $key => $val){        
            if(array_key_exists('file', $val)){                
                $resultArray [] = $val['file'] . (isset($val['line']) ? ": LINE #".$val['line']  : '' ) . (isset($val['function']) ? ": FUNC #".$val['function']."()" : '' );
            }elseif(array_key_exists('class', $val)){
                $resultArray [] = $val['class'] . (isset($val['line']) ? ": LINE #".$val['line']  : '' ). (isset($val['function']) ? ": FUNC #".$val['function']."()" : '' );;
            }elseif(array_key_exists('function', $val)){
                $resultArray [] = $val['function'] . (isset($val['line']) ? ": LINE #".$val['line']  : '' ). (isset($val['function']) ? ": FUNC #".$val['function']."()" : '' );;
            }else{
                continue;
            }
        }
        return $resultArray;
    }

}