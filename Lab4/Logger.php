<?php


class Logger
{
    const OUTPUT_SCREEN = 'screen';
    const OUTPUT_FILE = 'file';

    private string $outputType;
    private ?string $logFile;

    public function __construct(string $outputType = self::OUTPUT_SCREEN, ?string $logFile = null)
    {
        $this->outputType = $outputType;
        $this->logFile = $logFile;

        if ($outputType === self::OUTPUT_FILE && empty($logFile)) {
            throw new InvalidArgumentException('Log file path must be specified for file output');
        }
    }

    public function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;

        switch ($this->outputType) {
            case self::OUTPUT_SCREEN:
                echo $logMessage;
                break;

            case self::OUTPUT_FILE:
                file_put_contents($this->logFile, $logMessage, FILE_APPEND);
                break;

            default:
                throw new RuntimeException("Unknown output type: {$this->outputType}");
        }
    }

    public static function toScreen(): self
    {
        return new self(self::OUTPUT_SCREEN);
    }

    public static function toFile(string $filePath): self
    {
        return new self(self::OUTPUT_FILE, $filePath);
    }
}