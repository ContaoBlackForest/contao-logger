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

use Bit3\Contao\Logger\ContaoHandler;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\GroupHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

/**
 * The default log levels
 */
$container['logger.default.level']              = LogLevel::INFO;
$container['logger.default.level.contao']       = function ($container) {
    return $container['logger.default.level'];
};
$container['logger.default.level.buffer']       = function ($container) {
    return $container['logger.default.level'];
};
$container['logger.default.level.chromePhp']    = function ($container) {
    return $container['logger.default.level'];
};
$container['logger.default.level.firePhp']      = function ($container) {
    return $container['logger.default.level'];
};
$container['logger.default.level.rotatingFile'] = function ($container) {
    return $container['logger.default.level'];
};
$container['logger.default.level.mail']         = function ($container) {
    return LogLevel::ERROR;
};
$container['logger.default.level.stream']       = function ($container) {
    return $container['logger.default.level'];
};

/**
 * The default log rotation
 */
$container['logger.default.rotation'] = 28;

/**
 * The default logger handlers
 */
$container['logger.default.handlers'] = new ArrayObject(
    array(
        'logger.handler.contao',
        'logger.handler.stream'
    )
);

/**
 * The default contao syslog handler
 */
$container['logger.handler.contao'] = $container->share(
    function ($container) {
        $factory = $container['logger.factory.handler.contao'];
        return $factory();
    }
);

/**
 * The default stream handler
 */
$container['logger.handler.stream'] = $container->share(
    function ($container) {
        $factory = $container['logger.factory.handler.rotatingFile'];
        return $factory('contao.log', $container['logger.default.rotation']);
    }
);

/**
 * The default logger
 */
$container['logger'] = function ($container) {
    $factory = $container['logger.factory'];
    $logger  = $factory('contao', $container['logger.default.handlers']);

    return $logger;
};

/**
 * Factory to create a contao syslog handler
 */
$container['logger.factory.handler.contao'] = $container->protect(
    function ($level = null, $bubble = true, $function = null, $action = null) {
        global $container;

        if ($level === null) {
            $level = constant('Monolog\Logger::' . strtoupper($container['logger.default.level.contao']));
        } else {
            if (is_string($level) && defined('Monolog\Logger::' . strtoupper($level))) {
                $level = constant('Monolog\Logger::' . strtoupper($level));
            }
        }

        return new ContaoHandler($level, $bubble, $function, $action);
    }
);

/**
 * Factory to create a buffer handler
 */
$container['logger.factory.handler.buffer'] = $container->protect(
    function ($handler, $bufferSize = 0, $level = null, $bubble = true, $flushOnOverflow = false) {
        global $container;

        if (is_string($handler)) {
            $handler = $container[$handler];
        }
        if ($level === null) {
            $level = constant('Monolog\Logger::' . strtoupper($container['logger.default.level.buffer']));
        } else {
            if (is_string($level) && defined('Monolog\Logger::' . strtoupper($level))) {
                $level = constant('Monolog\Logger::' . strtoupper($level));
            }
        }

        return new BufferHandler($handler, $bufferSize, $level, $bubble, $flushOnOverflow);
    }
);

/**
 * Factory to create a chrome php handler
 */
$container['logger.factory.handler.chromePhp'] = $container->protect(
    function ($level = null, $bubble = true) {
        global $container;

        if ($level === null) {
            $level = constant('Monolog\Logger::' . strtoupper($container['logger.default.level.chromePhp']));
        } else {
            if (is_string($level) && defined('Monolog\Logger::' . strtoupper($level))) {
                $level = constant('Monolog\Logger::' . strtoupper($level));
            }
        }

        return new ChromePHPHandler($level, $bubble);
    }
);

/**
 * Factory to create a fingers crossed handler
 */
$container['logger.factory.handler.fingersCrossed'] = $container->protect(
    function ($handler, $activationStrategy = null, $bufferSize = 0, $bubble = true, $stopBuffering = true) {
        global $container;

        if (is_string($handler)) {
            $handler = $container[$handler];
        }

        return new FingersCrossedHandler($$handler, $activationStrategy, $bufferSize, $bubble, $stopBuffering);
    }
);

/**
 * Factory to create a fire php handler
 */
$container['logger.factory.handler.firePhp'] = $container->protect(
    function ($level = null, $bubble = true) {
        global $container;

        if ($level === null) {
            $level = constant('Monolog\Logger::' . strtoupper($container['logger.default.level.firePhp']));
        } else {
            if (is_string($level) && defined('Monolog\Logger::' . strtoupper($level))) {
                $level = constant('Monolog\Logger::' . strtoupper($level));
            }
        }

        return new FirePHPHandler($level, $bubble);
    }
);

/**
 * Factory to create a group handler
 */
$container['logger.factory.handler.group'] = $container->protect(
    function ($handlers, $bubble = true) {
        global $container;

        foreach ($handlers as $index => $handler) {
            if (is_string($handler)) {
                $handlers[$index] = $container[$handler];
            }
        }

        if ($handlers instanceof ArrayObject) {
            $handlers = $handlers->getArrayCopy();
        }

        return new GroupHandler($handlers, $bubble);
    }
);

/**
 * Factory to create a rotating file handler
 */
$container['logger.factory.handler.rotatingFile'] = $container->protect(
    function ($filename, $maxFiles = null, $level = null, $bubble = true, $filePermission = null, $useLocking = false) {
        global $container;

        if (strpos('/', $filename) === false) {
            $filename = TL_ROOT . '/system/logs/' . $filename;
        }

        if ($level === null) {
            $level = constant('Monolog\Logger::' . strtoupper($container['logger.default.level.rotatingFile']));
        } else {
            if (is_string($level) && defined('Monolog\Logger::' . strtoupper($level))) {
                $level = constant('Monolog\Logger::' . strtoupper($level));
            }
        }

        if ($maxFiles === null) {
            $maxFiles = $container['logger.default.rotation'];
        }

        return new RotatingFileHandler($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
    }
);

/**
 * Factory to create a mail handler
 */
$container['logger.factory.handler.mail'] = $container->protect(
    function ($to = null, $subject = null, $from = null, $level = null, $bubble = true) {
        global $container;

        /**
         * Include SwiftMailer classes
         */
        require_once(TL_ROOT . '/plugins/swiftmailer/classes/Swift.php');
        require_once(TL_ROOT . '/plugins/swiftmailer/swift_init.php');

        if (!$GLOBALS['TL_CONFIG']['useSMTP']) {
            // Mail
            $transport = Swift_MailTransport::newInstance();
        } else {
            // SMTP
            $transport = Swift_SmtpTransport::newInstance(
                $GLOBALS['TL_CONFIG']['smtpHost'],
                $GLOBALS['TL_CONFIG']['smtpPort']
            );

            // Encryption
            if ($GLOBALS['TL_CONFIG']['smtpEnc'] == 'ssl' || $GLOBALS['TL_CONFIG']['smtpEnc'] == 'tls') {
                $transport->setEncryption($GLOBALS['TL_CONFIG']['smtpEnc']);
            }

            // Authentication
            if ($GLOBALS['TL_CONFIG']['smtpUser'] != '') {
                $transport
                    ->setUsername($GLOBALS['TL_CONFIG']['smtpUser'])
                    ->setPassword($GLOBALS['TL_CONFIG']['smtpPass']);
            }
        }

        $mailer = Swift_Mailer::newInstance($transport);

        $messageFactory = function () use ($to, $subject, $from) {
            if (!$to) {
                $to = $GLOBALS['TL_CONFIG']['adminEmail'];
            }
            if (!$subject) {
                $subject =
                    'Log message from ' . $GLOBALS['TL_CONFIG']['websiteTitle'] . ' (' . Environment::getInstance(
                    )->request . ')';
            }
            if (!$from) {
                $from = $GLOBALS['TL_CONFIG']['adminEmail'];
            }

            $message = Swift_Message::newInstance();
            $message
                ->getHeaders()
                ->addTextHeader('X-Mailer', 'Logger for Contao Open Source CMS');
            $message->setTo($to);
            $message->setSubject($subject);
            $message->setFrom($from);
            return $message;
        };

        if ($level === null) {
            $level = constant('Monolog\Logger::' . strtoupper($container['logger.default.level.mail']));
        } else {
            if (is_string($level) && defined('Monolog\Logger::' . strtoupper($level))) {
                $level = constant('Monolog\Logger::' . strtoupper($level));
            }
        }

        return new SwiftMailerHandler($mailer, $messageFactory, $level, $bubble);
    }
);

/**
 * Factory to create a stream handler
 */
$container['logger.factory.handler.stream'] = $container->protect(
    function ($uri, $level = null, $bubble = true) {
        global $container;

        if (strpos('/', $uri) === false) {
            $uri = TL_ROOT . '/system/logs/' . $uri;
        }
        if ($level === null) {
            $level = constant('Monolog\Logger::' . strtoupper($container['logger.default.level.stream']));
        } else {
            if (is_string($level) && defined('Monolog\Logger::' . strtoupper($level))) {
                $level = constant('Monolog\Logger::' . strtoupper($level));
            }
        }

        return new StreamHandler($uri, $level, $bubble);
    }
);

/**
 * Factory to create a logger
 */
$container['logger.factory'] = $container->protect(
    function ($name, $handlers = array()) {
        global $container;

        $logger = new Logger($name);

        foreach ($handlers as $handler) {
            if (is_string($handler)) {
                $handler = $container[$handler];
            }
            $logger->pushHandler($handler);
        }

        return $logger;
    }
);
