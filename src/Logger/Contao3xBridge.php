<?php

/**
 * Logger bridge for Contao Open Source CMS
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    logger
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Logger;

/**
 * Logger bridge for contao 3.x releases.
 */
class Contao3xBridge
{
    /**
     * Create a new log entry.
     *
     * @param string $strText     The log message.
     * @param string $strFunction The raising function.
     * @param string $strAction   The log action.
     *
     * @return void
     */
    public function log($strText, $strFunction, $strAction)
    {
        if (\Database::getInstance()->tableExists('tl_log')) {
            \System::log($strText, $strFunction, $strAction);
        }
    }
}
