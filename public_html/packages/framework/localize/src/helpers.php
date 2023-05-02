<?php

use MetaFox\Platform\MetaFoxConstant;

if (!function_exists('csv_to_multi_array')) {
    /**
     * @return array<int, mixed>
     */
    function csv_to_multi_array(string $path): array
    {
        $handle = @fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        /** @var string[] $header */
        $header = null;
        $result = [];
        while (($line = fgetcsv($handle, null, ',')) !== false) {
            if (!$header) { // split first row.
                $header = $line;
                continue;
            }
            $row = [];
            foreach ($line as $index => $value) {
                $row[$header[$index]] = trim($value);
            }
            $result[] = $row;
        }

        fclose($handle);

        return $result;
    }
}

if (!function_exists('toTranslationKey')) {
    /**
     * Get translation key.
     *
     * @param string $namespace
     * @param string $group
     * @param string $name
     *
     * @return string
     */
    function toTranslationKey(string $namespace, string $group, string $name): string
    {
        if ($group === '*') {
            return $name;
        }

        if ($namespace === '*') {
            return sprintf('%s.%s', $group, $name);
        }

        return sprintf('%s::%s.%s', $namespace, $group, $name);
    }
}

if (!function_exists('__translation_wrapper')) {
    /**
     * @param  string $phrase
     * @return string
     */
    function __translation_wrapper(string $phrase): string
    {
        if (!config('localize.disable_translation')) {
            return $phrase;
        }

        if (\Illuminate\Support\Str::match('/[\w\d_-]+::[\w\d_-]+\.[\w\d_-]+/', $phrase)) {
            return $phrase;
        }

        if (\Illuminate\Support\Str::match('/^\[.*\]$/s', $phrase)) {
            return $phrase;
        }

        // Skip those cases where phrase key is returned instead of the actual message
        if (\Illuminate\Support\Str::match('/^(\w+)_[\w!@#$%?&]*$/m', $phrase)) {
            return $phrase;
        }

        // For some phrases which are wrapped with html tag
        if (\Illuminate\Support\Str::match('/^<html>.+<\/html>$/m', $phrase)) {
            $replaced =  preg_replace('/^(<html>)(\[?)(.+[^\]])(\]?)(<\/html>)$/m', '$1[$3]$5', $phrase);

            return is_string($replaced) ? $replaced : $phrase;
        }

        // Skip cases of empty string
        if (empty($phrase)) {
            return $phrase;
        }

        return sprintf('[%s]', $phrase);
    }
}

if (!function_exists('__p')) {
    /**
     * Translate the given message.
     *
     * @param string|null          $key
     * @param array<string, mixed> $replace
     * @param string|null          $locale
     *
     * @return string
     */
    function __p(string $key = null, array $replace = [], ?string $locale = null): string
    {
        if (null === $key || $key === '') {
            return '';
        }

        $phrase = app('translator')->get($key, $replace, $locale);

        if (!is_string($phrase)) {
            return $key;
        }

        $result = MessageFormatter::formatMessage($locale ?? app('translator')->locale(), $phrase, $replace);

        if (false === $result) {
            return __translation_wrapper($phrase);
        }

        return __translation_wrapper($result);
    }
}
