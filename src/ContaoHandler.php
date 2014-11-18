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

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Logging handler for the contao logging system.
 */
class ContaoHandler extends AbstractProcessingHandler
{
    /**
     * The raising function, if not set the channel name is used.
     *
     * @var null|string
     */
    protected $function;

    /**
     * The log action, if not set the level is used.
     *
     * @var null|string
     */
    protected $action;

    /**
     * The internal adapter.
     *
     * @var ContaoHandlerAdapter
     */
    protected $adapter;

    /**
     * {@inheritdoc}
     *
     * @param null $function The raising function, if not set the channel name is used.
     * @param null $action   The log action, if not set the level is used.
     */
    public function __construct($level = Logger::DEBUG, $bubble = true, $function = null, $action = null)
    {
        parent::__construct($level, $bubble);
        $this->function = $function;
        $this->action   = $action;
        $this->adapter  = new ContaoHandlerAdapter();
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if ($this->function !== null) {
            $function = $this->function;
        } else {
            $function = $record['channel'];
        }

        if ($this->action !== null) {
            $action = $this->action;
        } else {
            if ($record['level'] >= Logger::WARNING) {
                $action = 'ERROR';
            } else {
                if ($record['level'] < Logger::INFO) {
                    $action = 'DEBUG';
                } else {
                    $action = 'GENERAL';
                }
            }
        }

        $this->adapter->log(
            $record['formatted'],
            $function,
            $action
        );
    }
}
