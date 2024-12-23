<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use PhpDevCommunity\Console\Output;

$output = new Output();
$console = new Output\ConsoleOutput($output);

$data = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'active' => true,
    'roles' => ['admin', 'user']
];

$console->json($data);
//
$console->spinner();

$message = "This is a long message that needs to be automatically wrapped within the box, so it fits neatly without manual line breaks.";
$console->boxed($message);
$console->boxed($message, '.', 2);

$console->success('The deployment was successful! All application components have been updated and the server is running the latest version. You can access the application and check if all services are functioning correctly.');
$console->success('The deployment was successful! All application components have been updated and the server is running the latest version.');

$console->warning('Warning: The connection to the remote server is unstable! Several attempts to establish a stable connection have failed, and data integrity cannot be guaranteed. Please check the network connection.');
$console->warning('Warning: You are attempting to run a script with elevated privileges!');
//

$console->info('Info: Data export was completed successfully! All requested records have been exported to the specified format and location. You can now download the file or access it from the application dashboard.');
$console->info('Info: Data export was completed successfully!');

$console->error('Critical error encountered! The server encountered an unexpected condition that prevented it from fulfilling the request.');

$console->title('My Application Title');

$items = ['First item', 'Second item', 'Third item'];
$console->list($items);
$console->numberedList($items);


$items = [
    'Main Item 1',
    ['Sub-item 1.1', 'Sub-item 1.2'],
    'Main Item 2',
    ['Sub-item 2.1', ['Sub-sub-item 2.1.1']]
];
$console->indentedList($items);
$console->writeln('');
$options = [
    'Username' => 'admin',
    'Password' => '******',
    'Server' => 'localhost',
    'Port' => '3306'
];
$console->listKeyValues($options);

$headers = ['ID', 'Name', 'Status'];
$rows = [
    ['1', 'John Doe', 'Active'],
    ['2', 'Jane Smith', 'Inactive'],
    ['3', 'Emily Johnson', 'Active']
];
$console->table($headers, $rows);

for ($i = 0; $i <= 100; $i++) {
    $console->progressBar(100, $i);
    usleep(8000); // Simulate some work being done
}

if ($console->confirm('Are you sure you want to proceed ?')) {
    $console->success('Action confirmed.');
} else {
    $console->error('Action canceled.');
}

$name = $console->ask('What is your name ?');
$console->success("Hello, $name!");

$password = $console->ask('Enter your demo password', true, true);
$console->success("Your password is: $password");

$console->success('Done!');
