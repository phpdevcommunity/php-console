<?php

namespace PhpDevCommunity\Console\Output;


use PhpDevCommunity\Console\OutputInterface;

final class ConsoleOutput implements OutputInterface
{
    const FOREGROUND_COLORS = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'green' => '0;32',
        'light_green' => '1;32',
        'red' => '0;31',
        'light_red' => '1;31',
        'yellow' => '0;33',
        'light_yellow' => '1;33',
        'blue' => '0;34',
        'dark_blue' => '0;34',
        'light_blue' => '1;34',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];

    const BG_COLORS = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    private OutputInterface $output;

    public static function create(OutputInterface $output): ConsoleOutput
    {
        return new self($output);
    }

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function success(string $message): void
    {
        [$formattedMessage, $lineLength, $color] = $this->formatMessage('OK', $message, 'green');
        $this->outputMessage($formattedMessage, $lineLength, $color);
    }

    public function error(string $message): void
    {
        [$formattedMessage, $lineLength, $color] = $this->formatMessage('ERROR', $message, 'red');
        $this->outputMessage($formattedMessage, $lineLength, $color);
    }

    public function warning(string $message): void
    {
        [$formattedMessage, $lineLength, $color] = $this->formatMessage('WARNING', $message, 'yellow');
        $this->outputMessage($formattedMessage, $lineLength, $color);
    }

    public function info(string $message): void
    {
        [$formattedMessage, $lineLength, $color] = $this->formatMessage('INFO', $message, 'blue');
        $this->outputMessage($formattedMessage, $lineLength, $color);
    }

    public function title(string $message): void
    {
        $consoleWidth = $this->geTerminalWidth();
        $titleLength = mb_strlen($message);
        $underline = str_repeat('=', min($consoleWidth, $titleLength));

        $this->writeColor(PHP_EOL);
        $this->writeColor($message);
        $this->writeColor(PHP_EOL);
        $this->writeColor($underline);
        $this->writeColor(PHP_EOL);
    }

    public function list(array $items): void
    {
        foreach ($items as $item) {
            $this->writeColor('- ' . $item);
            $this->writeColor(PHP_EOL);
        }
        $this->writeColor(PHP_EOL);
    }

    public function listKeyValues(array $items, bool $inlined = false): void
    {
        $maxKeyLength = 0;
        if ($inlined) {
            foreach ($items as $key => $value) {
                $keyLength = mb_strlen($key);
                if ($keyLength > $maxKeyLength) {
                    $maxKeyLength = $keyLength;
                }
            }
        }

        foreach ($items as $key => $value) {
            $key = str_pad($key, $maxKeyLength, ' ', STR_PAD_RIGHT);
            $this->writeColor($key, 'green');
            $this->writeColor(' : ');
            $this->writeColor($value, 'white');
            $this->writeColor(PHP_EOL);
        }
        $this->writeColor(PHP_EOL);
    }

    public function indentedList(array $items, int $indentLevel = 1): void
    {
        if ($indentLevel == 1) {
            $this->writeColor(PHP_EOL);
        }

        foreach ($items as $item) {
            if (is_array($item)) {
                $this->indentedList($item, $indentLevel + 1);
            } else {
                $indentation = '';
                if ($indentLevel > 1) {
                    $indentation = str_repeat('  ', $indentLevel); // Indent with spaces
                }
                $this->writeColor($indentation . '- ', 'red');
                $this->writeColor($item, 'white');
                $this->writeColor(PHP_EOL);
            }
        }

        if ($indentLevel == 1) {
            $this->writeColor(PHP_EOL);
        }
    }

    public function numberedList(array $items)
    {
        foreach ($items as $index => $item) {
            $this->writeColor(($index + 1) . '. ', 'white');
            $this->writeColor($item, 'green');
            $this->writeColor(PHP_EOL);
        }
        $this->writeColor(PHP_EOL);
    }

    public function table(array $headers, array $rows): void
    {
        $columnWidths = array_map(function ($header) {
            return mb_strlen($header);
        }, $headers);

        foreach ($rows as $row) {
            foreach ($row as $index => $column) {
                $columnWidths[$index] = max($columnWidths[$index], mb_strlen($column));
            }
        }

        foreach ($headers as $index => $header) {
            $this->writeColor(str_pad($header, $columnWidths[$index] + 2));
        }
        $this->writeColor(PHP_EOL);

        $this->writeColor(str_repeat('-', array_sum($columnWidths) + count($columnWidths) * 2));
        $this->writeColor(PHP_EOL);

        foreach ($rows as $row) {
            foreach ($row as $index => $column) {
                $this->writeColor(str_pad($column, $columnWidths[$index] + 2));
            }
            $this->writeColor(PHP_EOL);
        }
    }

    public function progressBar(int $total, int $current): void
    {
        $barWidth = 50;
        $progress = ($current / $total) * $barWidth;
        $bar = str_repeat('#', (int)$progress) . str_repeat(' ', $barWidth - (int)$progress);
        $this->writeColor(sprintf("\r[%s] %d%%", $bar, ($current / $total) * 100));
        if ($current === $total) {
            $this->writeColor(PHP_EOL);
        }
    }

    public function confirm(string $message): bool
    {
        $this->writeColor($message . ' [y/n]: ', 'yellow');

        $handle = fopen('php://stdin', 'r');
        $input = trim(fgets($handle));
        fclose($handle);

        return in_array($input, ['y', 'yes', 'Y', 'YES'], true);
    }

    public function ask(string $question, bool $hidden = false, bool $required = false): string
    {
        $this->writeColor($question . ': ', 'cyan');

        if ($hidden) {
            if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
                throw new \RuntimeException('Windows platform is not supported for hidden input');
            } else {
                system('stty -echo');
                $input = trim(fgets(STDIN));
                system('stty echo');
                $this->writeColor(PHP_EOL);
            }
        } else {
            $handle = fopen('php://stdin', 'r');
            $input = trim(fgets($handle));
            fclose($handle);
        }

        if ($required && empty($input)) {
            throw new \InvalidArgumentException('Response cannot be empty');
        }

        return $input;
    }

    public function spinner(int $duration = 3): void
    {
        $spinnerChars = ['|', '/', '-', '\\'];
        $time = microtime(true);
        while ((microtime(true) - $time) < $duration) {
            foreach ($spinnerChars as $char) {
                $this->writeColor("\r$char");
                usleep(100000);
            }
        }
        $this->writeColor("\r");
    }

    public function json(array $data): void
    {
        $jsonOutput = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->writeColor("Error encoding JSON: " . json_last_error_msg() . PHP_EOL, 'red');
            return;
        }
        $this->writeColor($jsonOutput . PHP_EOL);
    }

    public function boxed(string $message, string $borderChar = '*', int $padding = 1): void
    {
        $this->writeColor(PHP_EOL);
        $lineLength = mb_strlen($message);
        $boxWidth = $this->geTerminalWidth();
        if ($lineLength > $boxWidth) {
            $lineLength = $boxWidth - ($padding * 2) - 2;
        }
        $lines = explode('|', wordwrap($message, $lineLength, '|', true));
        $border = str_repeat($borderChar, $lineLength + ($padding * 2) + 2);

        $this->writeColor($border . PHP_EOL);
        foreach ($lines as $line) {
            $strPad = str_repeat(' ', $padding);
            $this->writeColor($borderChar . $strPad . str_pad($line, $lineLength) . $strPad . $borderChar . PHP_EOL);
        }
        $this->writeColor($border . PHP_EOL);
        $this->writeColor(PHP_EOL);
    }

    public function writeColor(string $message, ?string $color = null, ?string $background = null): void
    {

        $formattedMessage = '';

        if ($color) {
            $formattedMessage .= "\033[" . self::FOREGROUND_COLORS[$color] . 'm';
        }
        if ($background) {
            $formattedMessage .= "\033[" . self::BG_COLORS[$background] . 'm';
        }

        $formattedMessage .= $message . "\033[0m";

        $this->write($formattedMessage);
    }

    public function write(string $message): void
    {
        $this->output->write($message);
    }

    public function writeln(string $message): void
    {
        $this->output->writeln($message);
    }

    private function outputMessage($formattedMessage, int $lineLength, string $color): void
    {
        $this->writeColor(PHP_EOL);
        $this->writeColor(str_repeat(' ', $lineLength), 'white', $color);
        $this->writeColor(PHP_EOL);

        if (is_string($formattedMessage)) {
            $formattedMessage = [$formattedMessage];
        }

        foreach ($formattedMessage as $line) {
            $this->writeColor($line, 'white', $color);
        }

        $this->writeColor(PHP_EOL);
        $this->writeColor(str_repeat(' ', $lineLength), 'white', $color);
        $this->writeColor(PHP_EOL);
        $this->writeColor(PHP_EOL);
    }

    private function formatMessage(string $prefix, string $message, string $color): array
    {
        $formattedMessage = sprintf('[%s] %s', $prefix, trim($message));
        $lineLength = mb_strlen($formattedMessage);
        $consoleWidth = $this->geTerminalWidth();

        if ($lineLength > $consoleWidth) {
            $lineLength = $consoleWidth;
            $lines = explode('|', wordwrap($formattedMessage, $lineLength, '|', true));
            $formattedMessage = array_map(function ($line) use ($lineLength) {
                return str_pad($line, $lineLength);
            }, $lines);
        }
        return [$formattedMessage, $lineLength, $color];
    }
    private function geTerminalWidth(): int
    {
        return ((int)exec('tput cols') ?? 85 - 5);
    }
}