<?php

namespace MetaFox\Core\Support\Link\Providers;

use Vimeo\Vimeo as VimeoClient;

/**
 * @SuppressWarnings(PHPMD)
 */
class Vimeo extends AbstractLinkProvider
{
    private string $clientId;
    private string $clientSecret;
    private string $accessToken;

    public function setOptions(array $options): void
    {
        $this->clientId     = $options['client_id'];
        $this->clientSecret = $options['client_secret'];
        $this->accessToken  = $options['access_token'];
    }

    public function verifyUrl(string $url, &$matches = []): bool
    {
        $pattern = '~https?://(?:www\.)?vimeo\.com/(?:[0-9a-z_-]+/)?(?:[0-9a-z_-]+/)?([0-9]{1,})~imu';

        return preg_match($pattern, $url, $matches) === 1;
    }

    public function parseUrl(string $url): ?array
    {
        if (!$this->verifyUrl($url, $matches)) {
            return null;
        }

        $iVideoId = $matches[1];
        try {
            $client   = new VimeoClient($this->clientId, $this->clientSecret, $this->accessToken);
            $response = $client->request('/videos/' . $iVideoId, []);
            if (!empty($response['status']) && $response['status'] == 200) {
                $aBody = $response['body'];

                $sDescription = __p('core::phrase.vimeo_default_description');

                if (!empty($aBody['description'])) {
                    $sDescription = $aBody['description'];
                }

                if (isset($aBody['user']['name'])) {
                    $sDescription = __p('core::phrase.vimeo_default_with_owner_description', [
                        'title'     => $aBody['name'],
                        'full_name' => $aBody['user']['name'],
                    ]);
                }

                return [
                    'title'       => $aBody['name'],
                    'is_video'    => true,
                    'image'       => isset($aBody['pictures']['sizes']) ? end($aBody['pictures']['sizes'])['link'] : '',
                    'description' => $sDescription,
                    'embed'       => $aBody['embed']['html'] ?? '',
                ];
            }
        } catch (\Exception $e) {
            // Silent
        }

        return null;
    }
}
