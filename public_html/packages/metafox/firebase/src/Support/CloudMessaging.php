<?php

namespace MetaFox\Firebase\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MetaFox\Platform\Facades\Settings;

class CloudMessaging
{
    public const FCM_URL_ENDPOINT = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function sendPushNotification(array $data): bool
    {
        $serverKey = Settings::get('firebase.server_key');
        $tokens    = Arr::get($data, 'tokens', []);
        $bodyData  = Arr::get($data, 'bodyData', []);
        $fields    = [
            'registration_ids' => $tokens,
            'priority'         => 'high',
            ...$bodyData,
        ];

        $headers = [
            'Authorization' => 'key=' . $serverKey,
            'Content-Type'  => 'application/json',
        ];

        $body = json_encode($fields);

        $response = Http::withHeaders($headers)
            ->withBody($body ?: '', 'application/json')
            ->post(self::FCM_URL_ENDPOINT);

        $this->logInfo([
            'header'   => json_encode($headers),
            'body'     => $body,
            'response' => $response->body(),
        ]);

        if ($response->successful()) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<string, mixed> $data
     * @return void
     */
    protected function logInfo(array $data): void
    {
        if (!config('app.debug')) {
            return;
        }

        Log::channel('push')->info(json_encode($data) ?: '');
    }
}
