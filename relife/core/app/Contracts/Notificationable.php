<?php

namespace App\Contracts;

use App\Models\User\User;

interface Notificationable
{
    const COMMON_NOTICE_TYPE = 'common_notice';
    const CHAT_MESSAGE_TYPE = 'chat_message';

    public function getRecipient(): ?User;

    public function getNoticeTitle(): string;

    public function getNoticeText(): string;

    public function getNoticeLink(): ?string;

    public function getNoticeType(): string;
}
