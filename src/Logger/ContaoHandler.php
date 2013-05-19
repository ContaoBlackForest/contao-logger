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

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class ContaoHandler extends AbstractProcessingHandler
{
	protected $function;

	protected $action;

	protected $adapter;

	public function __construct($level = Logger::DEBUG, $bubble = true, $function = null, $action = null)
	{
		parent::__construct($level, $bubble);
		$this->adapter = new ContaoHandlerAdapter();
	}

	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  array $record
	 *
	 * @return void
	 */
	protected function write(array $record)
	{
		if ($this->function !== null) {
			$function = $this->function;
		}
		else {
			$function = $record['channel'];
		}

		if ($this->action !== null) {
			$action = $this->action;
		}
		else if ($record['level'] >= Logger::WARNING) {
			$action = 'ERROR';
		}
		else if ($record['level'] < Logger::INFO) {
			$action = 'DEBUG';
		}
		else {
			$action = 'GENERAL';
		}

		$this->adapter->log(
			$record['formatted'],
			$function,
			$action
		);
	}
}
