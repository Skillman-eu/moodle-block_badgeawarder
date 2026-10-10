<?php
/**
 * badgeawarder messaging.
 *
 * @package   block_badgeawarder
 * @copyright 2023 Andrii Semenets
 */

defined('MOODLE_INTERNAL') || die();

$messageproviders = [
    'badge_awarding_message' => [
        'defaults' => [
            'popup' => MESSAGE_PERMITTED,
            'email' => MESSAGE_FORCED + MESSAGE_DEFAULT_ENABLED,
        ],
    ],
];
