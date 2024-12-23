<?php

use PhpDevCommunity\Console\CommandParser;
use PhpDevCommunity\Console\CommandRunner;
use PhpDevCommunity\Console\Output;
use Test\PhpDevCommunity\Console\Command\FooCommand;

set_time_limit(0);

if (file_exists(dirname(__DIR__) . '/../../autoload.php')) {
    require dirname(__DIR__) . '/../../autoload.php';
} elseif (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
} else {
    die(
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'curl -sS https://getcomposer.org/installer | php' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
}

// For modern frameworks using containers and bootstrapping (e.g., Kernel or App classes),
// make sure to retrieve the CommandRunner from the container after booting the application.
// Example for Symfony:
//
// $kernel = new Kernel('dev', true);
// $kernel->boot();
// $container = $kernel->getContainer();
// $app = $container->get(CommandRunner::class);

$app = new CommandRunner([
    new FooCommand(),
]);
$exitCode = $app->run(new CommandParser(), new Output());
exit($exitCode);



