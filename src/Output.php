<?php

namespace Pear\Cache\Lite;

/**
* This class extends Cache_Lite and uses output buffering to get the data to cache.
*
* There are some examples in the 'docs/examples' file
* Technical choices are described in the 'docs/technical' file
*
* @package Cache_Lite
* @author Fabien MARTY <fab@php.net>
*/

class Output extends Lite
{
    /**
    * Start the cache
    *
    * @param string $id cache id
    * @param string $group name of the cache group
    * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
    * @return boolean true if the cache is hit (false else)
    */
    function start(string $id, $group = Lite::DEFAULT_GROUP, $doNotTestCacheValidity = false)
    {
        $data = $this->get($id, $group, $doNotTestCacheValidity);

        if ($data !== false) {
            echo($data);
            return true;
        }

        ob_start();
        ob_implicit_flush(false);
        return false;
    }

    /**
    * Stop the cache
    */
    function end()
    {
        $data = ob_get_contents();
        ob_end_clean();
        $this->save($data, $this->_id, $this->_group);
        echo($data);
    }

}
