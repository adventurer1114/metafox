<?php

namespace MetaFox\Core\Support\Link\Providers;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook as FacebookClient;

/**
 * @SuppressWarnings(PHPMD)
 */
class Facebook extends AbstractLinkProvider
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
        $pattern = '/http(?:s?):\/\/(?:www\.|web\.|m\.)?(facebook\.com)|(fb\.watch)/';

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

        $aImplemented = class_implements(\Facebook\HttpClients\FacebookCurlHttpClient::class);
        if (empty($aImplemented) || end($aImplemented) != 'Facebook\HttpClients\FacebookHttpClientInterface') {
            return null;
        }

        //Force open source agent to get more information
        $_SERVER['HTTP_USER_AGENT'] = 'facebookexternalhit/1.1 (+https://www.facebook.com/externalhit_uatext.php)';

        //Only setup Graph API App for video
        $replaceVideoUrl = preg_replace(['/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/', '/http(?:s?):\/\/(fb\.watch)\/([A-z0-9_\-]+)/'], '$2', $url);
        $isVideo         = !empty($replaceVideoUrl) && $replaceVideoUrl != $url;
        try {
            $oFb = new FacebookClient([
                'app_id'                => $this->appId,
                'app_secret'            => $this->appSecret,
                'default_graph_version' => 'v8.0',
            ]);
            $oFb->setDefaultAccessToken($oFb->getApp()->getAccessToken());
            $oResponse = $oFb->get(
                ($isVideo ? '/oembed_video?url=' : '/oembed_post?url=') . $url
            );
        } catch (FacebookResponseException | FacebookSDKException $e) {
            return null;
        }
        $aBody = $oResponse->getDecodedBody();

        $description = null;
        $embed       = null;
        $embedWidth  = 0;
        $embedHeight = 0;

        if (!empty($aBody['html'])) {
            $embed = $aBody['html'];

            if (isset($aBody['width'])) {
                $embedWidth = $aBody['width'];
            }

            if (isset($aBody['height'])) {
                $embedHeight = $aBody['height'];
            }

            $regex = '/(.|\n)*<blockquote[^>]*><p>((.|\n)*)?<\/p>(.|\n)*/';

            $regex2 = '/(.|\n)*<blockquote[^>]*>((.|\n)*)?<\/blockquote>(.|\n)*/';

            $tryDesc = null;
            if (preg_match($regex, $aBody['html'])) {
                $tryDesc = preg_replace($regex, '$2', $aBody['html']);
            } elseif (preg_match($regex2, $aBody['html'])) {
                $tryDesc = preg_replace($regex2, '$2', $aBody['html']);
            }

            if (is_string($tryDesc)) {
                $description = strip_tags($tryDesc);
            }
        }

        $title = $aBody['author_name'] ?? ($aBody['provider_name'] ?: '');
        $host  = $aBody['provider_url'] ?: 'www.facebook.com';

        return [
            'embed'        => $embed,
            'embed_width'  => $embedWidth,
            'embed_height' => $embedHeight,
            'is_video'     => $isVideo,
            'title'        => $title,
            'description'  => $description,
            'host'         => $host,
        ];
    }
}
