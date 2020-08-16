<?php

namespace Pear\Cache\Lite;

/**
* This class extends Cache_Lite and offers a cache system driven by a master file
*
* With this class, cache validity is only dependent of a given file. Cache files
* are valid only if they are older than the master file. It's a perfect way for
* caching templates results (if the template file is newer than the cache, cache
* must be rebuild...) or for config classes...
* There are some examples in the 'docs/examples' file
* Technical choices are described in the 'docs/technical' file
*
* @package Cache_Lite
* @author Fabien MARTY <fab@php.net>
*/

class File extends Lite
{

    // --- Private properties ---

    /**
    * Complete path of the file used for controlling the cache lifetime
    *
    * @var string $_masterFile
    */
    private $_masterFile;

    /**
    * Masterfile mtime
    *
    * @var int $_masterFile_mtime
    */
    private $_masterFile_mtime;

    // --- Public methods ----

    /**
     * Constructor
     *
     * $options is an assoc. To have a look at availables options,
     * see the constructor of the Cache_Lite class in 'Cache_Lite.php'
     *
     * Comparing to Cache_Lite constructor, there is another option :
     * $options = array(
     *     (...) see Cache_Lite constructor
     *     'masterFile' => complete path of the file used for controlling the cache lifetime(string)
     * );
     *
     * @param array $options options
     * @throws Exceptions\CacheLiteException
     */
    public function __construct(array $options = [])
    {
        $options['lifetime'] = 0;
        parent::__construct($options);

        if (!isset($options['masterFile'])) {
            $this->raiseError('MasterFile option must be set!');
        }

        $this->_masterFile = $options['masterFile'];

        if (!($this->_masterFile_mtime = @filemtime($this->_masterFile))) {
            $this->raiseError('Cache_Lite_File : Unable to read masterFile : '.$this->_masterFile, -3);
        }
    }

    /**
     * Test if a cache is available and (if yes) return it
     *
     * @param string $id cache id
     * @param string $group name of the cache group
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string data of the cache
     * @throws Exceptions\CacheLiteException
     */
    function get($id, $group = self::DEFAULT_GROUP, $doNotTestCacheValidity = false)
    {
        if ($data = parent::get($id, $group, true)) {
            if ($filemtime = $this->lastModified()) {
                if ($filemtime > $this->_masterFile_mtime) {
                    return $data;
                }
            }
        }

        return null;
    }

}
