<?php

namespace App\Enums\Cabinet;

enum CabinetStatus : int
{
    case NEGATIVE_LIMIT_WHEN_MAILING = 1; // Превышение негативного лимита при рассылке
    case TARIFF_LIMIT_EXCEEDED = 2; // Превышение лимита по тарифам
    case RESOURCE_LIMIT_EXCEEDED = 3; // Превышение лимита по ресурсам
    case NOT_FILLED_MARKETING_CONTACTS = 4; // Не заполнены маркетинг контакты
    case CAME_FROM_AFFILIATE_LINK = 5; // Пришел по партнерской ссылке
}