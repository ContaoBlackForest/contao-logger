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

class Contao3xBridge
{
	public function log($strText, $strFunction, $strAction)
	{
		if (\Database::getInstance()->tableExists('tl_log')) {
			\System::log($strText, $strFunction, $strAction);
		}
	}
}
