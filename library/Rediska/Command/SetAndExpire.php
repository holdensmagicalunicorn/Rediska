<?php

/**
 * Set + Expire atomic command
 * 
 * @author Ivan Shumkov
 * @package Rediska
 * @category Commands
 * @version @package_version@
 * @link http://rediska.geometria-lab.net
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
class Rediska_Command_SetAndExpire extends Rediska_Command_Abstract
{
    /**
     * Supported version
     *
     * @var string
     */
    protected $_version = '2.0';

    /**
     * Create command
     *
     * @param string  $key   Key name
     * @param mixed   $value Value
     * @param integer $time  Expire time
     * @return Rediska_Connection_Exec
     */
    public function create($key, $value, $time)
    {
        $connection = $this->_rediska->getConnectionByKeyName($key);

        $command = array('SETEX',
                         $this->_rediska->getOption('namespace') . $key,
                         $time, $this->_rediska->getSerializer()->serialize($value));

        return new Rediska_Connection_Exec($connection, $command);
    }
}