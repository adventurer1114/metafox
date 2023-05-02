<?php

namespace MetaFox\Core\Support;

use Illuminate\Support\Str;
use MetaFox\Platform\Contracts\Output as OutputContract;
use MetaFox\Platform\MetaFoxConstant;

class Output implements OutputContract
{
    public const HASHTAG_REGEX         = '(#[^\s!@#$%^&*()=\-+.\/,\[{\]};:\'"?><]+)';
    public const TOPIC_REGEX           = '(##[^!@#$%^&*()=\-+.\/,\[{\]};:\'"?><]+[^\r\n\t\f\v]+)';
    public const HASHTAG_LINK          = '<a href="%s">%s</a>';
    public const URL_REGEX             = '@(http(s)?)?(:\/\/)?(([a-zA-Z0-9])([-\w]+\.)+([\w=%]+\S*)+)@';
    public const PARSE_URL_IGNORE_TAGS = 'head|link|a|script|style|code|pre|select|textarea|button';
    public const TEXT_NEW_LINE_REGEX   = '@<(\/p|br)>@';

    public const URL_POPULAR_DOMAIN_REGEX = '@\w+\.(com|gov|vn|net)$@'; // Todo: need to move this to a full support list of domain?

    /**
     * @inerhitDoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getHashtags(?string $text, bool $allowSpace = false): array
    {
        if (null === $text) {
            return [];
        }

        //Remove "#<something>" part of a url.
        $linkRegex  = '/(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/i';
        $styleRegex = '/style=(\'|")[^\'"]+(\'|")/i';
        $text       = preg_replace($linkRegex, '', $text);
        $text       = preg_replace($styleRegex, '', $text);
        $text       = trim($text);

        if (in_array($text, [null, MetaFoxConstant::EMPTY_STRING])) {
            return [];
        }

        $regex   = self::HASHTAG_REGEX;
        $replace = '#';

        if ($allowSpace) {
            $regex   = self::TOPIC_REGEX;
            $replace = '##';
        }

        //Search for hashtags
        $matches = Str::of($text)
            ->matchAll($regex)
            ->map(function (string $hashtag) use ($replace) {
                return Str::replace($replace, '', $hashtag);
            });

        return $matches->toArray();
    }

    public function buildHashtagLink(?string $string, string $uri, ?string $templateRegex = null): string
    {
        if (null === $string) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        $url = '/hashtag/search?q=' . $uri;

        $linkRegex = $templateRegex ?? self::HASHTAG_LINK;

        return sprintf($linkRegex, $url, $string);
    }

    /**
     * @inerhitDoc
     */
    public function getDescription(?string $string, int $limit = MetaFoxConstant::CHARACTER_LIMIT, string $end = '...'): string
    {
        if (null === $string) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        // Parse any new line tags into end of line char
        $string = $this->handleNewLineTag($string);

        $string = strip_tags($string);
        $string = str_replace('&nbsp;', '', $string);

        return Str::limit($string, $limit, $end);
    }

    public function parse(?string $string): string
    {
        if (null === $string) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        /**
         * Always clean script & style tags.
         */
        $string = $this->cleanScriptTag($string);
        $string = $this->cleanStyleTag($string);

        $isAllowHtml = $this->isAllowHtml();

        if (!$isAllowHtml) {
            $string = strip_tags($string);
        }

        $string = ban_word()->clean($string);
        $string = ban_word()->parse($string);

        $string = $this->parseUrl($string);

        return trim($string);
    }

    public function cleanScriptTag(?string $string): string
    {
        if (null === $string) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        /** @var string $parsedText */
        $parsedText = preg_replace("/<script([^\>]*)>/uim", '', $string);
        $parsedText = preg_replace("/<\/script>/uim", '', $parsedText);

        if (null === $parsedText) {
            return $string;
        }

        return $parsedText;
    }

    public function cleanStyleTag(?string $string): string
    {
        if (null === $string) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        /** @var string $parsedText */
        $parsedText = preg_replace("/<style([^\>]*)>/uim", '', $string);
        $parsedText = preg_replace("/<\/style>/uim", '', $parsedText);

        if (null === $parsedText) {
            return $string;
        }

        return $parsedText;
    }

    public function isAllowHtml(): bool
    {
        return true; //@todo: implement setting here
    }

    /**
     * @param string|null          $string  $string
     * @param array<string, mixed> $options
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function parseUrl(?string $string, array $options = []): string
    {
        if (null === $string) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        $ignoreTags = self::PARSE_URL_IGNORE_TAGS;
        $chunks     = preg_split('/(<.+?>|\s+)/im', $string, 0, PREG_SPLIT_DELIM_CAPTURE);

        if (false === $chunks) {
            return $this->linkify($string);
        }

        $chunkLength = count($chunks);
        for ($i = 0; $i < $chunkLength; $i++) {
            $isTextNode = !preg_match("@(<($ignoreTags).*(?<!\/)>|\s+)$@is", $chunks[$i]);
            if ($isTextNode) {
                $chunks[$i] = $this->linkify($chunks[$i]);
            }
        }

        return implode($chunks);
    }

    /**
     * @param  string|null $string
     * @return string
     * @todo: should move to a parse_link service?
     */
    public function linkify(?string $string): string
    {
        if (null === $string) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        $string = str_replace('&nbsp;', '', $string);

        if (!$this->isValidLink($string)) {
            return $string;
        }

        $parsed = preg_replace(self::URL_REGEX, '<a rel="noopener" href="http$2://$4" target="_blank" title="$0">$0</a>', $string);

        return null === $parsed ? '' : $parsed;
    }

    /**
     * @param  string $string
     * @return bool
     * @Todo: Should be extend to support a list of all active link?
     */
    private function isValidLink(string $string): bool
    {
        $scheme = parse_url($string, PHP_URL_SCHEME);
        if (is_string($scheme)) {
            return true;
        }

        if (preg_match(self::URL_POPULAR_DOMAIN_REGEX, $string)) {
            return true;
        }

        return false;
    }

    /**
     * @param  string $string
     * @return string
     */
    public function handleNewLineTag(string $string): string
    {
        // Prepend a blank space before any new line delimeter to prepare for strip tags
        $parsed = preg_replace_callback(self::TEXT_NEW_LINE_REGEX, function ($matches) {
            return MetaFoxConstant::BLANK_SPACE . $matches[0];
        }, $string);

        if (null === $parsed) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        return $parsed;
    }
}
