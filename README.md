Logger bridge for Contao Open Source CMS
========================================

This bridge provide PSR-3 logger support for (Contao Open Source CMS)[http://contao.org].
The logger is available via the (dependency injection container)[https://github.com/bit3/contao-dependency-container].

By default the logger use two handlers.
* An contao syslog handler, that write log entries to the system log database.
* And an stream handler, that write log entries to `system/logs/contao.log`.

By default it use (Monolog)[https://github.com/Seldaek/monolog] as implementation, but it is designed to be replaceable with any PSR-3 compatible logger implementation.

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
$container['logger.default.level'] = \Monolog\Logger::WARNING;
```

### Define default log handlers

The default log handlers are stored in `$container['logger.handlers']` containing a list of handler services.

```php
global $container;

// receive the default log handlers array (its an ArrayObject instance)
$handlers = $container['logger.handlers'];

// remove the contao syslog handler
foreach ($handlers as $index => $serviceKey) {
	if ($serviceKey == 'logger.handler.contao') {
		unset($handlers[$index]);
		break;
	}
}

// add a custom handler
$container['logger.handler.custom'] = function($container) {
	return new StreamHandler(TL_ROOT . '/system/logs/critical.log', \Monolog\Logger::CRITICAL);
}
$handlers->append('logger.handler.custom');
```

### Create your own logger

```php
global $container;

// register a handler
$container['logger.handler.custom'] = function($container) {
	return new StreamHandler(TL_ROOT . '/system/logs/critical.log', \Monolog\Logger::CRITICAL);
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
---------

### Services

<table>
<tr>
	<td>`$container['logger.default.level']`</td>
	<td>(`int`) the default log level, default: `Logger::INFO`</td>
</tr>
<tr>
	<td>`$container['logger.default.rotation']`</td>
	<td>(`int`) number of days for log rotation, default: 28</td>
</tr>
<tr>
	<td>`$container['logger.handler.contao']`</td>
	<td>(`Monolog\Handler\HandlerInterface|Logger\ContaoHandler`) default contao syslog handler</td>
</tr>
<tr>
	<td>`$container['logger.handler.stream']`</td>
	<td>(`Monolog\Handler\HandlerInterface|Monolog\Handler\RotatingFileHandler`) default rotating logfile (system/logs/contao-Y-m-d.log) handler</td>
</tr>
<tr>
	<td>`$container['logger.handlers']`</td>
	<td>(`ArrayObject`) list of default log handlers</td>
</tr>
<tr>
	<td>`$container['logger']`</td>
	<td>(`Psr\Log\LoggerInterface|Monolog\Logger`) the default logger</td>
</tr>
</table>

### Factories

<table>
<tr>
	<td>`$container['logger.factory.handler.contao']`</td>
	<td>`function($level = null, $bubble = true, $function = null, $action = null)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.buffer']`</td>
	<td>`function($handler, $bufferSize = 0, $level = null, $bubble = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.chromePhp']`</td>
	<td>`function($level = null, $bubble = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.fingersCrossed']`</td>
	<td>`function($handler, $activationStrategy = null, $bufferSize = 0, $bubble = true, $stopBuffering = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.firePhp']`</td>
	<td>`function($level = null, $bubble = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.group']`</td>
	<td>`function(array $handlers, $bubble = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.rotatingFile']`</td>
	<td>`function($filename, $maxFiles = null, $level = null, $bubble = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.mail']`</td>
	<td>`function($to = null, $subject = null, $from = null, $level = null, $bubble = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory.handler.stream']`</td>
	<td>`function($uri, $level = null, $bubble = true)`</td>
</tr>
<tr>
	<td>`$container['logger.factory']`</td>
	<td>`function($name, array $handlers)`</td>
</tr>
</table>
