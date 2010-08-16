<?php

// Require Rediska
require_once dirname(__FILE__) . '/../../Rediska.php';

/**
 * Rediska hash key
 * 
 * @author Ivan Shumkov
 * @package Rediska
 * @version @package_version@
 * @link http://rediska.geometria-lab.net
 * @licence http://www.opensource.org/licenses/bsd-license.php
 */
class Rediska_Key_Hash extends Rediska_Key_Abstract implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * Construct hash
     *
     * @param string                    $name        Key name
     * @param integer                   $expire      Expire time in seconds
     * @param string|Rediska_Connection $serverAlias Server alias or Rediska_Connection object where key is placed
     */
    public function  __construct($name, $expire = null, $serverAlias = null)
    {
        parent::__construct($name, $expire, $serverAlias);

        $this->_throwIfNotSupported();
    }

    /**
     * Set value to a hash field or fields
     *
     * @param array|string  $fieldOrData  Field or array of many fields and values: field => value
     * @param mixed         $value        Value for single field
     * @param boolean       $overwrite    Overwrite for single field (if false don't set and return false if key already exist). For default true.
     * @return boolean
     */
    public function set($fieldOrData, $value = null, $overwrite = true)
    {
        $result = $this->_getRediskaOn()->setToHash($this->_name, $fieldOrData, $value, $overwrite);

        if (!is_null($this->_expire) && ((!$overwrite && $result) || ($overwrite))) {
            $this->expire($this->_expire, $this->_isExpireTimestamp);
        }

        return $result;
    }

    /**
     * Magic for set a field
     *
     * @param string $field
     * @param mixed  $value
     * @return boolean
     */
    public function  __set($field, $value)
    {
        $this->set($field, $value);

        return $value;
    }

    /**
     * Array magic for set a field
     *
     * @param string $field
     * @param mixed $value
     * @return boolean
     */
    public function offsetSet($field, $value)
    {
        if (is_null($field)) {
            throw new Rediska_Key_Exception('Field must be present');
        }

        $this->set($field, $value);

        return $value;
    }

    /**
     * Get value from hash field or fields
     *
     * @param string       $name          Key name
     * @param string|array $fieldOrFields Field or fields
     * @return mixed
     */
    public function get($fieldOrFields)
    {
        return $this->_getRediskaOn()->getFromHash($this->_name, $fieldOrFields);
    }

    /**
     * Magic for get a field
     *
     * @param string $field
     * @return mixed
     */
    public function  __get($field)
    {
        return $this->get($field);
    }

    /**
     * Array magic for get a field
     *
     * @param string $name
     * @return mixed
     */
    public function offsetGet($field)
    {
        return $this->get($field);
    }

    /**
     * Increment field value in hash
     *
     * @param mixed  $field            Field
     * @param number $amount[optional] Increment amount. Default: 1
     * @return integer
     */
    public function increment($field, $amount = 1)
    {
        $result = $this->_getRediskaOn()->incrementInHash($this->_name, $field, $amount);

        if (!is_null($this->_expire) && $result) {
            $this->expire($this->_expire, $this->_isExpireTimestamp);
        }

        return $result;
    }

    /**
     * Test if field is present in hash
     *
     * @prarm mixed  $field Field
     * @return boolean
     */
    public function exists($field)
    {
        return $this->_getRediskaOn()->existsInHash($this->_name, $field);
    }

    /**
     * Magic for test if field is present in hash
     *
     * @param string $field
     * @return boolean
     */
    public function  __isset($field)
    {
        return $this->exists($field);
    }

    /**
     * Array magic for test if field is present in hash
     *
     * @param string $field
     * @return boolean
     */
    public function offsetExists($field)
    {
        return $this->exists($field);
    }

    /**
     * Remove field from hash
     *
     * @param mixed  $field Field
     * @return boolean
     */
    public function remove($field)
    {
        $result = $this->_getRediskaOn()->deleteFromHash($this->_name, $field);

        if (!is_null($this->_expire) && $result) {
            $this->expire($this->_expire, $this->_isExpireTimestamp);
        }

        return $result;
    }

    /**
     * Magic for remove field from hash
     *
     * @param string $field
     * @return boolean
     */
    public function  __unset($field)
    {
        return $this->remove($field);
    }

    /**
     * Array magic for remove field from hash
     *
     * @param string $field
     * @return boolean
     */
    public function offsetUnset($field)
    {
        return $this->remove($field);
    }

    /**
     * Get hash fields
     * 
     * @return array
     */
    public function getFields()
    {
        return $this->_getRediskaOn()->getHashFields($this->_name);
    }

    /**
     * Get hash values
     * 
     * @return array
     */
    public function getValues()
    {
        return $this->_getRediskaOn()->getHashValues($this->_name);
    }

    /**
     * Get hash as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_getRediskaOn()->getHash($this->_name);
    }

    /* Countable implementation */

    public function count()
    {
        return $this->_getRediskaOn()->getHashLength($this->_name);
    }

    /* IteratorAggregate implementation */

    public function getIterator()
    {
        return new ArrayObject($this->toArray());
    }

    /**
     * Throw if PubSub not supported by Redis
     */
    protected function _throwIfNotSupported()
    {
        $version = '1.3.10';
        $redisVersion = $this->getRediska()->getOption('redisVersion');
        if (version_compare($version, $this->getRediska()->getOption('redisVersion')) == 1) {
            throw new Rediska_PubSub_Exception("Publish/Subscribe requires {$version}+ version of Redis server. Current version is {$redisVersion}. To change it specify 'redisVersion' option.");
        }
    }
}