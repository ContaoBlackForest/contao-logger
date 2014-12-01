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

namespace Bit3\Contao\Logger;

/**
 * Logger bridge for contao 2.x releases.
 */
class Contao2xBridge extends \System
{
    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart
    public function __construct()
    {
        parent::__construct();
    }
    // @codingStandardsIgnoreEnd

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
            parent::log($strText, $strFunction, $strAction);
        }
    }
}
