<?php
class FileSystemObject{
    private string $name;
    private string $type;
    private int $sizeBytes;

    public function __construct(string $name, string $type, int $sizeBytes) {
        $this->name = $name;
        $this->type = $type;
        $this->sizeBytes = $sizeBytes;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getSize(string $unit = 'B'): float {
        $unit = strtoupper($unit);
        $units = ['B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4];

        if (!isset($units[$unit])) {
            throw new InvalidArgumentException("Invalid unit: {$unit}");
        }

        return $this->sizeBytes / (1024 ** $units[$unit]);
    }

    public static function createFromPath(string $path): ?FileSystemObject {
        if (!file_exists($path)) {
            return null;
        }

        $name = basename($path);
        $type = is_dir($path) ? 'directory' : 'file';
        $sizeBytes = $type === 'directory' ? 0 : filesize($path);

        return new self($name, $type, $sizeBytes);
    }

    public static function getDirectoryContents(string $directory): array {
        $objects = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;
            $object = self::createFromPath($path);

            if ($object !== null) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

}