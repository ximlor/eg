<?php

namespace Micfunc\loadFromFile;

use Micfunc\loadFromFile\InvalidPathException;

class Loader
{

    protected $filePath;

    protected $fileSuffix;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;

        $this->fileSuffix = ($suffix = strrpos($filePath, '.')) ? substr($filePath, strrpos($filePath, '.')) : null;

        return $this;
    }

    public function load()
    {
        $filePath = $this->filePath;

        $this->ensureFileReadable($filePath);

        $lines = $this->loadLinesFromFile($filePath);

        foreach ($lines as $line) {
            $line = trim($line);

            if (!$this->isComment($line) && $this->isFormatted($line)) {
                list($key, $value) = $this->parse($line);
//                $this->setEnvironmentVariable();
            }
        }
    }

    public function ensureFileReadable($filePath)
    {
        if (!is_readable($filePath) || !is_file($filePath)) {
            throw new InvalidPathException(sprintf("Invalid Path: %s", $filePath));
        }
    }

    public function loadLinesFromFile($filePath)
    {
        $autoDetectDefault = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', 1);
        $fileLines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autoDetectDefault);

        return $fileLines;
    }

    public function isComment($line)
    {
        return strpos($line, '#') === 0;
    }

    public function isFormatted($line)
    {
        return strpos($line, '=') !== false;
    }

    public function parse($line)
    {
        list($key, $value) = array_map('trim', explode('=', $line, 2));

        $key = $this->normaliseKey($key);
        $value = $this->normaliseValue($value);

        return [$key, $value];
    }

    protected function normaliseKey($key)
    {
        return trim(str_replace(['export ', '\'', '"'], '', $key));
    }

    protected function normaliseValue($value)
    {
        if (!$value) {
            return $value;
        }

        if (strpbrk($value[0], '\'"') !== false) {
            $quote = $value[0];
            $regexp = sprintf(
                '/
                ^%1$s
                (
                 (?:
                  [^%1$s\\\\]
                  || \\\\\\\\
                  || \\\\%1$s
                 )*
                )
                %1$s
                .*$
                /mx', $quote);

            $value = preg_replace($regexp, '$1', $value);
            $value = str_replace("\\$quote", "$quote", $value);
            $value = str_replace("\\$quote", "$quote", $value);
        } else {

        }
        return $value;
    }

}
