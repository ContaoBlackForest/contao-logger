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

class ContaoHandlerAdapter extends \System
{
	public function __construct()
	{
		parent::__construct();
	}

	public function log($strText, $strFunction, $strAction)
	{
		parent::log($strText, $strFunction, $strAction);
	}
}
