<?php

/**
 * Get key type
 * 
 * @author Ivan Shumkov
 * @package Rediska
 * @version @package_version@
 * @link http://rediska.geometria-lab.net
 * @licence http://www.opensource.org/licenses/bsd-license.php
 */
class Rediska_Command_GetType extends Rediska_Command_Abstract
{
    /**
     * Create command
     *
     * @param string $key Key name
     * @return Rediska_Connection_Exec
     */
    public function create($key)
    {
        $connection = $this->_rediska->getConnectionByKeyName($key);

        $command = "TYPE {$this->_rediska->getOption('namespace')}$key";

        return new Rediska_Connection_Exec($connection, $command);
    }
}