# PHP Console

A lightweight PHP library designed to simplify command handling in console applications. This library is dependency-free and focused on providing a streamlined and efficient solution for building PHP CLI tools.
## Installation

You can install this library via [Composer](https://getcomposer.org/). Ensure your project meets the minimum PHP version requirement of 7.4.

```bash
composer require phpdevcommunity/php-console
```
## Requirements

- PHP version 7.4 or higher

## Table of Contents

1. [Setup in a PHP Application](#setup-in-a-php-application)
2. [Creating a Command](#creating-a-command)
3. [Defining Arguments and Options](#defining-arguments-and-options)
4. [Handling Different Output Types](#handling-different-output-types)


I attempted to rewrite the chapter "Setup in a PHP Application" in English while updating the document, but the update failed due to an issue with the pattern-matching process. Let me fix the issue manually. Here's the rewritten content in English:

---

## Setup in a PHP Application

To use this library in any PHP application (Symfony, Laravel, Slim, or others), first create a `bin` directory in your project. Then, add a script file, for example, `bin/console`, with the following content:

```php
#!/usr/bin/php
<?php

use PhpDevCommunity\Console\CommandParser;
use PhpDevCommunity\Console\CommandRunner;
use PhpDevCommunity\Console\Output;

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


$app = new CommandRunner([]);
$exitCode = $app->run(new CommandParser(), new Output());
exit($exitCode);
```

By convention, the file is named `bin/console`, but you can choose any name you prefer. This script serves as the main entry point for your CLI commands. Make sure the file is executable by running the following command:

```bash
chmod +x bin/console
```

## Creating a Command

To add a command to your application, you need to create a class that implements the `CommandInterface` interface. Here is an example implementation for a command called `send-email`:

```php
use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\OutputInterface;

class SendEmailCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'send-email';
    }

    public function getDescription(): string
    {
        return 'Sends an email to the specified recipient with an optional subject.';
    }

    public function getOptions(): array
    {
        return [
            new CommandOption('subject', 's', 'The subject of the email', false),
        ];
    }

    public function getArguments(): array
    {
        return [
            new CommandArgument('recipient', true, null, 'The email address of the recipient'),
        ];
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        // Validate and retrieve the recipient email
        if (!$input->hasArgument('recipient')) {
            $output->writeln('Error: The recipient email is required.');
            return;
        }
        $recipient = $input->getArgumentValue('recipient');

        // Validate email format
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $output->writeln('Error: The provided email address is not valid.');
            return;
        }

        // Retrieve the subject option (if provided)
        $subject = $input->hasOption('subject') ? $input->getOptionValue('subject') : 'No subject';

        // Simulate email sending
        $output->writeln('Sending email...');
        $output->writeln('Recipient: ' . $recipient);
        $output->writeln('Subject: ' . $subject);
        $output->writeln('Email sent successfully!');
    }
}
```

###  Registering the Command in `CommandRunner`

After creating your command, you need to register it in the `CommandRunner` so that it can be executed. Here is an example of how to register the `SendEmailCommand`:

```php
use PhpDevCommunity\Console\CommandRunner;

$app = new CommandRunner([
    new SendEmailCommand()
]);

```

The `CommandRunner` takes an array of commands as its parameter. Each command should be an instance of a class that implements the `CommandInterface`. Once registered, the command can be called from the console.

### Example Usage in the Terminal

1. **Command without a subject option:**

   ```bash
   bin/console send-email john.doe@example.com
   ```

   **Output:**
   ```
   Sending email...
   Recipient: john.doe@example.com
   Subject: No subject
   Email sent successfully!
   ```

2. **Command with a subject option:**

   ```bash
   bin/console send-email john.doe@example.com --subject "Meeting Reminder"
   ```

   **Output:**
   ```
   Sending email...
   Recipient: john.doe@example.com
   Subject: Meeting Reminder
   Email sent successfully!
   ```

3. **Command with an invalid email format:**

   ```bash
   bin/console send-email invalid-email
   ```

   **Output:**
   ```
   Error: The provided email address is not valid.
   ```

4. **Command without the required argument:**

   ```bash
   bin/console send-email
   ```

   **Output:**
   ```
   Error: The recipient email is required.
   ```

---

### Explanation of the Features Used

1. **Required Arguments**:
    - The `recipient` argument is mandatory. If missing, the command displays an error.

2. **Optional Options**:
    - The `--subject` option allows defining the subject of the email. If not specified, a default value ("No subject") is used.

3. **Simple Validation**:
    - The email address is validated using `filter_var` to ensure it has a valid format.

4. **User Feedback**:
    - Clear and simple messages are displayed to guide the user during the command execution.

## Defining Arguments and Options

Arguments and options allow developers to customize the behavior of a command based on the parameters passed to it. These concepts are managed by the `CommandArgument` and `CommandOption` classes, respectively.

---

### 1 Arguments

An **argument** is a positional parameter passed to a command. For example, in the following command:

```bash
bin/console send-email recipient@example.com
```

`recipient@example.com` is an argument. Arguments are defined using the `CommandArgument` class. Here are the main properties of an argument:

- **Name (`name`)**: The unique name of the argument.
- **Required (`isRequired`)**: Indicates whether the argument is mandatory.
- **Default Value (`defaultValue`)**: The value used if no argument is provided.
- **Description (`description`)**: A brief description of the argument, useful for help messages.

##### Example of defining an argument:

```php
use PhpDevCommunity\Console\Argument\CommandArgument;

new CommandArgument(
    'recipient', // The name of the argument
    true,        // The argument is required
    null,        // No default value
    'The email address of the recipient' // Description
);
```

If a required argument is not provided, an exception is thrown.

---

### 2 Options

An **option** is a named parameter, often prefixed with a double dash (`--`) or a shortcut (`-`). For example, in the following command:

```bash
bin/console send-email recipient@example.com --subject "Meeting Reminder"
```

`--subject` is an option. Options are defined using the `CommandOption` class. Here are the main properties of an option:

- **Name (`name`)**: The full name of the option, used with `--`.
- **Shortcut (`shortcut`)**: A short alias, used with a single dash (`-`).
- **Description (`description`)**: A brief description of the option, useful for help messages.
- **Flag (`isFlag`)**: Indicates whether the option is a simple flag (present or absent) or if it accepts a value.

##### Example of defining an option:

```php
use PhpDevCommunity\Console\Option\CommandOption;

new CommandOption(
    'subject',    // The name of the option
    's',          // Shortcut
    'The subject of the email', // Description
    false         // Not a flag, expects a value
);

new CommandOption(
    'verbose',    // The name of the option
    'v',          // Shortcut
    'Enable verbose output', // Description
    true          // This is a flag
);
```

An option with a flag does not accept a value; its mere presence indicates that it is enabled.

---

### 3 Usage in a Command

In a command, arguments and options are defined by overriding the `getArguments()` and `getOptions()` methods from the `CommandInterface`.

##### Example:

```php
use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\Option\CommandOption;

public function getArguments(): array
{
    return [
        new CommandArgument('recipient', true, null, 'The email address of the recipient'),
    ];
}

public function getOptions(): array
{
    return [
        new CommandOption('subject', 's', 'The subject of the email', false),
        new CommandOption('verbose', 'v', 'Enable verbose output', true),
    ];
}
```

In this example:
- `recipient` is a required argument.
- `--subject` (or `-s`) is an option that expects a value.
- `--verbose` (or `-v`) is a flag option.

---

### 4 Validation and Management

Arguments and options are automatically validated when the command is executed. For instance, if a required argument is missing or an attempt is made to access an undefined option, an exception will be thrown.

The `InputInterface` allows you to retrieve these parameters in the `execute` method:
- Arguments: `$input->getArgumentValue('recipient')`
- Options: `$input->getOptionValue('subject')` or `$input->hasOption('verbose')`

This ensures clear and consistent management of the parameters passed within your PHP project.

## Handling Different Output Types

Output management provides clear and useful information during command execution. Below is a practical example demonstrating output functionalities.

### Example: Command `UserReportCommand`

This command generates a report for a specific user and uses various output features.

```php
use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\Output\ConsoleOutput;
use PhpDevCommunity\Console\OutputInterface;

class UserReportCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'user:report';
    }

    public function getDescription(): string
    {
        return 'Generates a detailed report for a specific user.';
    }

    public function getArguments(): array
    {
        return [
            new CommandArgument('user_id', true, null, 'The ID of the user to generate the report for'),
        ];
    }

    public function getOptions(): array
    {
        return [
            new CommandOption('verbose', 'v', 'Enable verbose output', true),
            new CommandOption('export', 'e', 'Export the report to a file', false),
        ];
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $console = new ConsoleOutput($output);

        // Main title
        $console->title('User Report Generation');

        // Arguments and options
        $userId = $input->getArgumentValue('user_id');
        $verbose = $input->hasOption('verbose');
        $export = $input->hasOption('export') ? $input->getOptionValue('export') : null;

        $console->info("Generating report for User ID: $userId");

        // Simulating user data retrieval
        $console->spinner();
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'active' => true,
            'roles' => ['admin', 'user']
        ];

        if ($verbose) {
            $console->success('User data retrieved successfully.');
        }

        // Displaying user data
        $console->json($userData);

        // Displaying user roles as a list
        $console->listKeyValues([
            'Name' => $userData['name'],
            'Email' => $userData['email'],
            'Active' => $userData['active'] ? 'Yes' : 'No',
        ]);
        $console->list($userData['roles']);

        // Table of recent activities
        $headers = ['ID', 'Activity', 'Timestamp'];
        $rows = [
            ['1', 'Login', '2024-12-22 12:00:00'],
            ['2', 'Update Profile', '2024-12-22 12:30:00'],
        ];
        $console->table($headers, $rows);

        // Progress bar
        for ($i = 0; $i <= 100; $i += 20) {
            $console->progressBar(100, $i);
            usleep(500000);
        }

        // Final result
        if ($export) {
            $console->success("Report exported to: $export");
        } else {
            $console->success('Report generated successfully!');
        }

        // Confirmation for deletion
        if ($console->confirm('Do you want to delete this user?')) {
            $console->success('User deleted successfully.');
        } else {
            $console->warning('User deletion canceled.');
        }
    }
}
```

#### Features Used

1. **Rich Messages**:
   - `success($message)`: Displays a success message.
   - `warning($message)`: Displays a warning message.
   - `error($message)`: Displays a critical error message.
   - `info($message)`: Displays an informational message.
   - `title($title)`: Displays a main title.

2. **Structured Output**:
   - `json($data)`: Displays data in JSON format.
   - `list($items)`: Displays a simple list.
   - `listKeyValues($data)`: Displays key-value pairs.
   - `table($headers, $rows)`: Displays tabular data.

3. **Progression and Interactivity**:
   - `progressBar($total, $current)`: Displays a progress bar.
   - `spinner()`: Displays a loading spinner.
   - `confirm($question)`: Prompts for user confirmation.

---
## License

This library is open-source software licensed under the [MIT license](LICENSE).