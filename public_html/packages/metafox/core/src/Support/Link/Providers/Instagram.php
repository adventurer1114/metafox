<?php

namespace MetaFox\Core\Support\Link\Providers;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook as FacebookClient;
use Facebook\HttpClients\FacebookCurlHttpClient;

/**
 * @SuppressWarnings(PHPMD)
 */
class Instagram extends AbstractLinkProvider
{
    private string $appId;
    private string $appSecret;

    public function setOptions(array $options): void
    {
        $this->appId     = $options['app_id'];
        $this->appSecret = $options['app_secret'];
    }

    public function verifyUrl(string $url, &$matches = []): bool
    {
        $pattern = '/(https?:\/\/www\.)?instagram\.com(\/p\/\w+\/?)/';

        return preg_match($pattern, $url, $matches) === 1;
    }

    public function parseUrl(string $url): ?array
    {
        if (!$this->verifyUrl($url, $matches)) {
            return null;
        }

        if (empty($this->appId) || empty($this->appSecret)) {
            return null;
        }

        $aImplemented = class_implements(FacebookCurlHttpClient::class);
        if (empty($aImplemented) || end($aImplemented) != 'Facebook\HttpClients\FacebookHttpClientInterface') {
            return null;
        }

        try {
            $oFb = new FacebookClient([
                'app_id'                => $this->appId,
                'app_secret'            => $this->appSecret,
                'default_graph_version' => 'v8.0',
            ]);
            $oFb->setDefaultAccessToken($oFb->getApp()->getAccessToken());
            $oResponse = $oFb->get(
                '/instagram_oembed?url=' . $url
            );
        } catch (FacebookResponseException | FacebookSDKException $e) {
            return null;
        }

        $aBody = $oResponse->getDecodedBody();

        return [
            'title' => $aBody['author_name'] ?? ($aBody['provider_name'] ?: ''),
            'image' => $aBody['thumbnail_url'] ?? '',
            'embed' => $aBody['html'],
        ];
    }
}
