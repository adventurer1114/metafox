<?php

namespace MetaFox\Firebase\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MetaFox\Platform\Facades\Settings;

class CloudMessaging
{
    public const FCM_URL_SEND_ENDPOINT         = 'https://fcm.googleapis.com/fcm/send';
    public const FCM_URL_NOTIFICATION_ENDPOINT = 'https://fcm.googleapis.com/fcm/notification';

    /**
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function sendPushNotification(array $data): bool
    {
        $serverKey = $this->getServerKey();
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
            ->post(self::FCM_URL_SEND_ENDPOINT);

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
     * @param  int           $userId
     * @param  array<string> $tokens
     * @return bool
     */
    public function addUserDeviceGroup(int $userId, array $tokens = []): bool
    {
        $notificationKey = $this->getUserDeviceGroup($userId);
        $headers         = $this->getNotificationHeaders();

        $data = [
            'operation'             => $notificationKey ? 'add' : 'create',
            'notification_key_name' => sprintf('user-%s', $userId),
            'registration_ids'      => $tokens,
        ];

        if ($notificationKey) {
            $data['notification_key'] = $notificationKey;
        }

        $body = json_encode($data);

        $response = Http::withHeaders($headers)
            ->withBody($body ?: '', 'application/json')
            ->post(self::FCM_URL_NOTIFICATION_ENDPOINT);

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
     * @param  int           $userId
     * @param  array<string> $tokens
     * @return bool
     */
    public function removeUserDeviceGroup(int $userId, array $tokens = []): bool
    {
        $notificationKey = $this->getUserDeviceGroup($userId);
        if (!$notificationKey) {
            return false;
        }

        $headers = $this->getNotificationHeaders();

        $data = [
            'operation'             => 'remove',
            'notification_key'      => $notificationKey,
            'notification_key_name' => sprintf('user-%s', $userId),
            'registration_ids'      => $tokens,
        ];

        $body = json_encode($data);

        $response = Http::withHeaders($headers)
            ->withBody($body ?: '', 'application/json')
            ->post(self::FCM_URL_NOTIFICATION_ENDPOINT);

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

    public function getUserDeviceGroup(int $userId): ?string
    {
        $query = [
            'notification_key_name' => sprintf('user-%s', $userId),
        ];

        $response = Http::withHeaders($this->getNotificationHeaders())
            ->get(self::FCM_URL_NOTIFICATION_ENDPOINT, $query);

        if (!$response->successful()) {
            return null;
        }

        return $response->json('notification_key');
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

    protected function getServerKey(): ?string
    {
        $key = Settings::get('firebase.server_key');

        return is_string($key) ? $key : null;
    }

    protected function getSenderId(): ?string
    {
        $senderID = Settings::get('firebase.sender_id');

        return is_string($senderID) ? $senderID : null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getNotificationHeaders(): array
    {
        return [
            'Authorization' => 'key=' . $this->getServerKey(),
            'project_id'    => $this->getSenderId(),
            'Content-Type'  => 'application/json',
        ];
    }
}
