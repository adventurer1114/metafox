<?php

namespace MetaFox\Core\Support;

class BanWord implements \MetaFox\Platform\Contracts\BanWord
{
    /**
     * @var array<mixed>
     */
    private array $words = [];

    public function __construct()
    {
        $this->init();
    }

    private function init(): void
    {
        // @todo here.
        $this->words = [];
    }

    /**
     * @return array<mixed>
     */
    public function getWords(): array
    {
        return $this->words;
    }

    public function clean(?string $string = null): string
    {
        if (!is_string($string)) {
            return '';
        }

        return $this->parseString($string);
    }

    /**
     * @param string|null $string
     *
     * @return string
     * @SuppressWarnings(PHPMD)
     * @todo implement later
     */
    private function parseString(?string $string = null): string
    {
        if (null === $string) {
            return '';
        }

        $words = $this->getWords();

        if (empty($words)) {
            return $string;
        }

        foreach ($words as $filterString => $mValue) {
            if (!is_string($filterString)) {
                continue;
            }

            $filterString = str_replace('/', "\/", $filterString);
            $filterString = str_replace('&#42;', '*', $filterString);

            if ($this->isAllowHtml()) {
                $filterString = str_replace('&#039;', '\'', $filterString);
            }

            if (!is_string($string)) {
                continue;
            }

            if (preg_match('/\*/i', $filterString)) {
                $filterString = str_replace(['.', '*'], ['\.', '([a-zA-Z@]?)*'], $filterString);
                $string = preg_replace('/' . $filterString . '/is', ' ' . $mValue . ' ', $string);
                continue;
            }

            $string = preg_replace("/(\b)" . $filterString . "(\b)/i", '${1}' . $mValue . '${2}', $string);

            if (!is_string($string)) {
                continue;
            }

            $string = ltrim($string);
            $string = rtrim($string);
        }

        if (!is_string($string)) {
            return '';
        }

        return $string;
    }

    private function isAllowHtml(): bool
    {
        return true;
    }

    /**
     * @param  string|null $string
     * @return string
     */
    public function parse(?string $string = null): string
    {
        // TODO: Implement parse() method.
        if (!is_string($string)) {
            return '';
        }

        return $string;
    }
}
