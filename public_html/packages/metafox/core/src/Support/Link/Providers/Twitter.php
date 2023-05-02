<?php

namespace MetaFox\Core\Support\Link\Providers;

use Exception;
use Illuminate\Support\Facades\Http;

/**
 * @SuppressWarnings(PHPMD)
 */
class Twitter extends AbstractLinkProvider
{
    private string $tokenUrl  = 'https://api.twitter.com/oauth2/token';
    private string $tweetsUrl = 'https://api.twitter.com/labs/2/tweets';
    private string $usersUrl  = 'https://api.twitter.com/labs/2/users';

    private string $apiKey;
    private string $secretKey;

    public function setOptions(array $options): void
    {
        $this->apiKey    = $options['api_key'];
        $this->secretKey = $options['secret_key'];
    }

    public function verifyUrl(string $url, &$matches = []): bool
    {
        $pattern = '|https?://(www\.)?twitter\.com|';

        return preg_match($pattern, $url, $matches) === 1;
    }

    public function parseUrl(string $url): ?array
    {
        $aReturnDefault = [
            'title'       => 'Login on Twitter',
            'description' => 'Welcome back to Twitter. Sign in now to check your notifications, join the conversation and catch up on Tweets from the people you follow.',
            'image'       => 'https://abs.twimg.com/responsive-web/web/icon-ios.8ea219d4.png',
        ];

        if (!$this->verifyUrl($url, $matches)) {
            return null;
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return $aReturnDefault;
        }

        $patternStatus = '|https?://(www\.)?twitter\.com/(?:\#!/)?(\w+)/status(es)?/(\d+)|';
        $patternUser   = '|https?://(www\.)?twitter\.com/(#!/)?@?([^/]*)|';

        $reqUrl = $id = $userName = '';
        if (preg_match($patternStatus, $url, $matches)) {
            if (!empty($matches[4])) {
                $id     = $matches[4];
                $reqUrl = $this->tweetsUrl . '/' . $id . '?expansions=attachments.media_keys,author_id&media.fields=url';
            }
        } elseif (preg_match($patternUser, $url, $matches)) {
            if (!empty($matches[3])) {
                $userName = $matches[3];
                $reqUrl   = $this->usersUrl . '/by?usernames=' . $userName . '&user.fields=profile_image_url,description';
            }
        }

        $content = $this->getContent($reqUrl, $token);
        if (empty($content)) {
            return $aReturnDefault;
        }

        if (!empty($id)) {
            return $this->processStatus($content);
        }

        if (!empty($userName)) {
            return $this->processUser($content) ?? $aReturnDefault;
        }

        return $aReturnDefault;
    }

    private function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, $this->secretKey)
                ->asForm()
                ->post($this->tokenUrl, ['grant_type' => 'client_credentials'])
                ->json();

            return $response['access_token'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param string $url
     * @param string $token
     *
     * @return ?array<mixed>
     */
    private function getContent(string $url, string $token): ?array
    {
        try {
            $response = Http::withToken($token)
                ->get($url)
                ->json();

            return $response['data'];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param array<mixed> $content
     *
     * @return array<mixed>
     */
    private function processStatus(array $content): array
    {
        if (isset($content['includes'])) {
            $media = null;
            $user  = null;
            if (isset($content['includes']['media'])) {
                $media = array_shift($content['includes']['media']);
            }

            if (isset($content['includes']['users'])) {
                $user = array_shift($content['includes']['users']);
            }

            $image = $media ? $media['url'] : '';
            $title = $user ? $user['name'] . ' on Twitter' : '';
        }

        return [
            'title'       => $title ?? null,
            'description' => $content['text'] ?? null,
            'image'       => $image ?? null,
        ];
    }

    /**
     * @param array<mixed> $content
     *
     * @return array<mixed>
     */
    private function processUser(array $content): ?array
    {
        $user = array_shift($content);
        if (empty($user)) {
            return null;
        }

        $image = null;
        if (isset($user['profile_image_url'])) {
            $image = str_replace('_normal', '', $user['profile_image_url']);
        }

        return [
            'title'       => $user['name'] ?? '',
            'description' => $user['description'] ?? '',
            'image'       => $image,
        ];
    }
}
