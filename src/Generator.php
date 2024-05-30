<?php

namespace AwStudio\NumberGenerator;

class Generator
{
    public function __construct(
        public string|int $start = 0,
        public $prefix = '',
        public $suffix = '',
        public $dynamicDates = false
    ) {
    }

    public function next(): string
    {
        $incremented = $this->start + 1;
        // ray($incremented, $this->start, $this->prefix, $this->suffix);

        $number = str_pad($incremented, strlen($this->start), '0', STR_PAD_LEFT);

        if ($this->prefix != '') {
            $number = $this->resolvePrefix().$number;
        }

        if ($this->suffix) {
            $number .= $this->suffix;
        }

        return $number;
    }

    public function resolvePrefix()
    {
        return self::replacePlaceholders($this->prefix);
    }

    public static function fromPattern($number, $pattern, $dynamicDates = false)
    {
        $startNumber = (new self())->extractNumberByPattern($number, $pattern);

        $prefix = str_replace($startNumber, '', $number);

        $prefix = str_replace('{n}', '', $pattern);

        if (! $dynamicDates) {
            $prefix = str_replace($startNumber, '', $number);
        }

        if ($dynamicDates) {
            $newPrefix = self::replacePlaceholders($prefix);

            $oldPrefix = (new self())->getOldPrefix($number, $pattern);
            if ($newPrefix != $oldPrefix) {
                $startNumber = str_pad(0, strlen($startNumber), '0', STR_PAD_LEFT);
            }
        }

        return new Generator(
            start: $startNumber,
            prefix: $prefix,
            dynamicDates: $dynamicDates
        );

    }

    public function extractNumberByPattern($subject, $pattern)
    {
        // Determine the regex pattern based on the given pattern
        $regexPattern = preg_quote($pattern, '/'); // Escape the pattern for use in regex

        // Replace {Y} and {m} with regex to match digits
        // replace the {n} with a wildcard
        $regexPattern = str_replace('\{rn\}', '(\d+)', $regexPattern);
        $regexPattern = str_replace('\{n\}', '(\d+)', $regexPattern);
        $regexPattern = str_replace('\{Y\}', '(\d{4})', $regexPattern);
        $regexPattern = str_replace('\{m\}', '(\d{2})', $regexPattern);
        $regexPattern = str_replace('\{d\}', '(\d{2})', $regexPattern);

        // Build the final regex pattern to match at the end of the string
        $regexPattern = '/'.$regexPattern.'$/';

        // Use preg_match to extract parts
        if (preg_match($regexPattern, $subject, $matches)) {
            ray($matches)->red();

            return end($matches);
        } else {
            return null; // No match found
        }
    }

    protected function getOldPrefix($number, $pattern)
    {
        $startNumber = $this->extractNumberByPattern($number, $pattern);

        $prefix = str_replace($startNumber, '', $number);

        return $prefix;
    }

    protected static function replacePlaceholders($pattern)
    {
        $placeholders = [
            '{Y}' => date('Y'),
            '{m}' => date('m'),
            '{d}' => date('d'),
        ];

        foreach ($placeholders as $placeholder => $value) {
            $pattern = str_replace($placeholder, $value, $pattern);
        }

        return $pattern;

    }
}
