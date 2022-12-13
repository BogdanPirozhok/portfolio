<?php

namespace App\Enums;

use App\Models\User\Profile;

final class NotificationEnum
{
    public const LINK_TYPE_PROFILE = 'profile';
    public const LINK_TYPE_COMMENT = 'comment';
    public const LINK_TYPE_POST = 'post';
    public const LINK_TYPE_CUSTOM = 'custom';
    public const LINK_TYPE_OTHER = 'other';

    public const TYPE_SUBSCRIBE = 'subscribe';
    public const TYPE_UNSUBSCRIBE = 'unsubscribe';

    public const TYPE_NEW_COMMENT = 'new_comment';
    public const TYPE_COMMENT_ANSWER = 'comment_answer';

    public const TYPE_POSITIVE_RATING_POST = 'positive_rating_post';
    public const TYPE_NEGATIVE_RATING_POST = 'negative_rating_post';

    public const TYPE_POSITIVE_RATING_COMMENT = 'positive_rating_comment';
    public const TYPE_NEGATIVE_RATING_COMMENT = 'negative_rating_comment';

    public const TYPE_NEW_REGISTRATION = 'new_registration';

    public const TYPE_SYSTEM = 'system';

    public const TYPES = [
        self::TYPE_SUBSCRIBE => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Подписался на вас',
                    Profile::GENDER_OTHER => 'Подписался на вас',
                    Profile::GENDER_FEMALE => 'Подписалась на вас',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Followed you',
                    Profile::GENDER_OTHER => 'Followed you',
                    Profile::GENDER_FEMALE => 'Followed you',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE
            ]
        ],
        self::TYPE_UNSUBSCRIBE => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Отписался от вас',
                    Profile::GENDER_OTHER => 'Отписался от вас',
                    Profile::GENDER_FEMALE => 'Отписалась от вас',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Unfollowed you',
                    Profile::GENDER_OTHER => 'Unfollowed you',
                    Profile::GENDER_FEMALE => 'Unfollowed you',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE
            ]
        ],
        self::TYPE_NEW_COMMENT => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Оставил комментарий к вашей статье',
                    Profile::GENDER_OTHER => 'Оставил комментарий к вашей статье',
                    Profile::GENDER_FEMALE => 'Оставила комментарий к вашей статье',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Left a comment on your post',
                    Profile::GENDER_OTHER => 'Left a comment on your post',
                    Profile::GENDER_FEMALE => 'Left a comment on your post',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE,
                self::LINK_TYPE_COMMENT,
            ]
        ],
        self::TYPE_COMMENT_ANSWER => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Ответил на ваш комментарий к статье',
                    Profile::GENDER_OTHER => 'Ответил на ваш комментарий к статье',
                    Profile::GENDER_FEMALE => 'Ответила на ваш комментарий к статье',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Replied to your comment on post',
                    Profile::GENDER_OTHER => 'Replied to your comment on post',
                    Profile::GENDER_FEMALE => 'Replied to your comment on post',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE,
                self::LINK_TYPE_COMMENT,
            ]
        ],
        self::TYPE_POSITIVE_RATING_POST => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Положительно оценил вашу статью',
                    Profile::GENDER_OTHER => 'Положительно оценил вашу статью',
                    Profile::GENDER_FEMALE => 'Положительно оценила вашу статью',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Gendered your post',
                    Profile::GENDER_OTHER => 'Gendered your post',
                    Profile::GENDER_FEMALE => 'Gendered your post',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE,
                self::LINK_TYPE_POST,
            ]
        ],
        self::TYPE_NEGATIVE_RATING_POST => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Отрицательно оценил вашу статью',
                    Profile::GENDER_OTHER => 'Отрицательно оценил вашу статью',
                    Profile::GENDER_FEMALE => 'Отрицательно оценила вашу статью',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Gendered your post negative',
                    Profile::GENDER_OTHER => 'Gendered your post negative',
                    Profile::GENDER_FEMALE => 'Gendered your post negative',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE,
                self::LINK_TYPE_POST,
            ]
        ],
        self::TYPE_POSITIVE_RATING_COMMENT => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Положительно оценил ваш комментарий к статье',
                    Profile::GENDER_OTHER => 'Положительно оценил ваш комментарий к статье',
                    Profile::GENDER_FEMALE => 'Положительно оценила ваш комментарий к статье',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Positively your comment on the post',
                    Profile::GENDER_OTHER => 'Positively your comment on the post',
                    Profile::GENDER_FEMALE => 'Positively your comment on the post',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE,
                self::LINK_TYPE_COMMENT,
            ]
        ],
        self::TYPE_NEGATIVE_RATING_COMMENT => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Отрицательно оценил ваш комментарий к статье',
                    Profile::GENDER_OTHER => 'Отрицательно оценил ваш комментарий к статье',
                    Profile::GENDER_FEMALE => 'Отрицательно оценила ваш комментарий к статье',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Negatively rated your comment on the post',
                    Profile::GENDER_OTHER => 'Negatively rated your comment on the post',
                    Profile::GENDER_FEMALE => 'Negatively rated your comment on the post',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE,
                self::LINK_TYPE_COMMENT,
            ]
        ],
        self::TYPE_NEW_REGISTRATION => [
            'text' => [
                'ru' => [
                    Profile::GENDER_MALE => 'Присоеденился по вашему коду приглащения (Referral code)',
                    Profile::GENDER_OTHER => 'Присоеденился по вашему коду приглащения (Referral code)',
                    Profile::GENDER_FEMALE => 'Присоеденилась по вашему коду приглащения (Referral code)',
                ],
                'en' => [
                    Profile::GENDER_MALE => 'Joined with your referral code',
                    Profile::GENDER_OTHER => 'Joined with your referral code',
                    Profile::GENDER_FEMALE => 'Joined with your referral code',
                ]
            ],
            'links' => [
                self::LINK_TYPE_PROFILE
            ]
        ],
        self::TYPE_SYSTEM => [
            'links' => [
                self::LINK_TYPE_CUSTOM
            ]
        ]
    ];
}
