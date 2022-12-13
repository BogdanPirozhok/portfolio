<?php

namespace App\Services\Common;

use App\Contracts\Notificationable;
use App\Exceptions\RegularException;
use App\Models\User\User;
use App\Models\User\UserDevice;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    protected Notificationable $notificationable;
    protected ?User $recipient;

    protected int $countAllType;
    protected int $countCurrentType;
    /**
     * @throws RegularException
     */
    public function generatePush(Notificationable $notificationable): void
    {
        $this->notificationable = $notificationable;
        $this->recipient = $this->notificationable->getRecipient();

        if ($this->recipient) {
            $devices = $this->recipient->devices;

            $this->calcCount();

            foreach ($devices as $device) {
                try {
                    $this->sendPush($device);
                } catch (\Exception $exception) {
                    throw new RegularException($exception->getMessage(), 500);
                }
            }
        }
    }

    protected function calcCount(): void
    {
        $data = $this->recipient->getUnreadNotifications();

        $this->countCurrentType = $data[$this->notificationable->getNoticeType()];
        $this->countAllType = $data['sum_all_count'];
    }

    protected function sendPush(UserDevice $device): void
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'key='. config('firebase.app_key')
        ])
            ->post(
                config('firebase.send_notice_api_url'),
                [
                    'to' => $device->token,
                    'notification' => [
                        'title' => $this->notificationable->getNoticeTitle(),
                        'body'  => $this->notificationable->getNoticeText(),
                        'priority' => 'HIGH',
                        'badge' => $this->countAllType,
                        'sound' => 'default',
                    ],
                    'data' => [
                        'link' => $this->notificationable->getNoticeLink(),
                        'type' => $this->notificationable->getNoticeType(),
                        'count' => $this->countCurrentType
                    ],
                    'android' => [
                        'priority' => 'HIGH',
                        'notification' => [
                            'notification_priority' => 'PRIORITY_MAX',
                            'default_sound' => true,
                            'visibility' => 'PUBLIC',
                            'notification_count' => $this->countAllType,
                        ],
                    ],
                ]
            )
            ->json();

        if (empty($response['success'])) {
            $device->delete();
        }
    }
}
