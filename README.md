Logger bridge for Contao Open Source CMS
========================================

This bridge provide PSR-3 logger support for [Contao Open Source CMS](http://contao.org).
The logger is available via the [dependency injection container](https://github.com/bit3/contao-dependency-container).

By default the logger use two handlers.
* An contao syslog handler, that write log entries to the system log database.
* And an stream handler, that write log entries to `system/logs/contao.log`.

By default it use [Monolog](https://github.com/Seldaek/monolog) as implementation, but it is designed to be replaceable with any PSR-3 compatible logger implementation.

Access and use the logger
-----------------

```php
global $container;

/** @var \Psr\Log\LoggerInterface */
$logger = $container['logger'];
$logger->emergency('Some extreme critical message');
```

Logger configuration
--------------------

### Receive and change the default log level

```php
global $container;

// receive default log level
$level = $container['logger.default.level'];

// change default log level
$container['logger.default.level'] = \Psr\Log\LogLevel::WARNING;
```

### Define default log handlers

The default log handlers are stored in `$container['logger.default.handlers']` containing a list of handler services.

```php
global $container;

// receive the default log handlers array (its an ArrayObject instance)
$handlers = $container['logger.default.handlers'];

// remove the contao syslog handler
foreach ($handlers as $index => $serviceKey) {
	if ($serviceKey == 'logger.handler.contao') {
		unset($handlers[$index]);
		break;
	}
}

// add a custom handler
$container['logger.handler.custom'] = function($container) {
	$factory = $container['logger.factory.handler.stream'];
	// store in /var/log/critical.log
	return $factory('/var/log/critical.log', \Psr\Log\LogLevel::CRITICAL);
}
$handlers->append('logger.handler.custom');
```

### Create your own logger

```php
global $container;

// register a handler
$container['logger.handler.custom'] = function($container) {
	$factory = $container['logger.factory.handler.stream'];
	// store in system/logs/critical.log
	return $factory('critical.log', \Monolog\Logger::CRITICAL);
}

// register your logger
$container['logger.custom'] = function($container) {
	// using the logger factory
	$factory = $container['logger.factory'];
	$logger = $factory('contao', array('logger.handler.custom'));

	return $logger;
};

// receive your logger
$logger = $container['logger.custom'];
```

Reference
=========

Services
--------

### `$container['logger.default.level']`
(`int`) the default log level, default: `Psr\Log\LogLevel::INFO`

### `$container['logger.default.level.contao']`
(`int`) the default log level, inherited from `$container['logger.default.level']`

### `$container['logger.default.level.buffer']`
(`int`) the default log level, inherited from `$container['logger.default.level']`

### `$container['logger.default.level.chromePhp']`
(`int`) the default log level, inherited from `$container['logger.default.level']`

### `$container['logger.default.level.firePhp']`
(`int`) the default log level, inherited from `$container['logger.default.level']`

### `$container['logger.default.level.rotatingFile']`
(`int`) the default log level, inherited from `$container['logger.default.level']`

### `$container['logger.default.level.mail']`
(`int`) the default log level, default: `Psr\Log\LogLevel::ERROR`

### `$container['logger.default.level.stream']`
(`int`) the default log level, inherited from `$container['logger.default.level']`

### `$container['logger.default.rotation']`
(`int`) number of days for log rotation, default: 28

### `$container['logger.handler.contao']`
(`Monolog\Handler\HandlerInterface|Logger\ContaoHandler`) default contao syslog handler

### `$container['logger.handler.stream']`
(`Monolog\Handler\HandlerInterface|Monolog\Handler\RotatingFileHandler`) default rotating logfile (system/logs/contao-Y-m-d.log) handler

### `$container['logger.default.handlers']`
(`ArrayObject`) list of default log handlers

### `$container['logger']`
(`Psr\Log\LoggerInterface|Monolog\Logger`) the default logger

Factories
---------

### `$container['logger.factory.handler.contao']`
```php
/**
 * @param int    $level    The minimum logging level at which this handler will be triggered
 * @param bool   $bubble   Whether the messages that are handled can bubble up the stack or not
 * @param string $function The function name in the contao syslog (use channel name by default)
 * @param string $action   The action name in the contao syslog (use simplified log level name by default)
 */
function($level = null, $bubble = true, $function = null, $action = null)
```

### `$container['logger.factory.handler.buffer']`
```php
/**
 * @param string|callable|Monolog\Handler\HandlerInterface $handler         Service name, callable or handler object.
 * @param int                                              $bufferSize      How many entries should be buffered at most, beyond that the oldest items are removed from the buffer.
 * @param int                                              $level           The minimum logging level at which this handler will be triggered
 * @param bool                                             $bubble          Whether the messages that are handled can bubble up the stack or not
 * @param bool                                             $flushOnOverflow If true, the buffer is flushed when the max size has been reached, by default oldest entries are discarded
 */
function function($handler, $bufferSize = 0, $level = null, $bubble = true, $flushOnOverflow = false)
```

### `$container['logger.factory.handler.chromePhp']`
```php
/**
 * @param int  $level  The minimum logging level at which this handler will be triggered
 * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
 */
function function($level = null, $bubble = true)
```

### `$container['logger.factory.handler.fingersCrossed']`
```php
/**
 * @param string|callable|Monolog\Handler\HandlerInterface $handler            Service name, callable or handler object.
 * @param int|ActivationStrategyInterface                  $activationStrategy The minimum logging level at which this handler will be triggered
 * @param int                                              $bufferSize         How many entries should be buffered at most, beyond that the oldest items are removed from the buffer.
 * @param bool                                             $bubble             Whether the messages that are handled can bubble up the stack or not
 * @param bool                                             $stopBuffering      Whether the handler should stop buffering after being triggered (default true)
 */
function function($handler, $activationStrategy = null, $bufferSize = 0, $bubble = true, $stopBuffering = true)
```

### `$container['logger.factory.handler.firePhp']`
```php
/**
 * @param int  $level  The minimum logging level at which this handler will be triggered
 * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
 */
function function($level = null, $bubble = true)
```

### `$container['logger.factory.handler.group']`
```php
/**
 * @param array $handlers List of services, callbacks or handlers.
 * @param bool  $bubble   Whether the messages that are handled can bubble up the stack or not
 */
function function(array $handlers, $bubble = true)
```

### `$container['logger.factory.handler.rotatingFile']`
```php
/**
 * @param string $filename Absolute filename or single name (stored in system/logs/)
 * @param int    $maxFiles The maximal amount of files to keep (0 means unlimited)
 * @param int    $level  The minimum logging level at which this handler will be triggered
 * @param bool   $bubble Whether the messages that are handled can bubble up the stack or not
 */
function function($filename, $maxFiles = null, $level = null, $bubble = true)
```

### `$container['logger.factory.handler.mail']`
```php
/**
 * A handler using swift to send entries as emails.
 *
 * @param string $to      The email recipient address
 * @param string $subject The email subject
 * @param string $from    The email sender address
 * @param int    $level   The minimum logging level at which this handler will be triggered
 * @param bool   $bubble  Whether the messages that are handled can bubble up the stack or not
 */
function function($to = null, $subject = null, $from = null, $level = null, $bubble = true)
```

### `$container['logger.factory.handler.stream']`
```php
/**
 * @param string $uri    Stream uri
 * @param int    $level  The minimum logging level at which this handler will be triggered
 * @param bool   $bubble Whether the messages that are handled can bubble up the stack or not
 */
function function($uri, $level = null, $bubble = true)
```

### `$container['logger.factory']`
```php
/**
 * @param string $name     The channel name
 * @param array  $handlers List of services or handlers.
 */
function function($name, array $handlers = array())
```
