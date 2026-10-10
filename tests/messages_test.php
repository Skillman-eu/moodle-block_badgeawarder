<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace block_badgeawarder;

defined('MOODLE_INTERNAL') || die();

/**
 * Message provider upgrade regression tests.
 *
 * @package block_badgeawarder
 * @copyright 2026 Andrii Semenets
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
final class messages_test extends \advanced_testcase {
    public function test_provider_loads_with_moodle_45_constants(): void {
        $providers = message_get_providers_from_file('block_badgeawarder');
        $this->assertArrayHasKey('badge_awarding_message', $providers);
        $defaults = $providers['badge_awarding_message']['defaults'];
        [$locked, $enabled] = translate_message_default_setting($defaults['email'], 'email');
        $this->assertTrue((bool) $locked);
        $this->assertTrue((bool) $enabled);
        [$locked, $enabled] = translate_message_default_setting($defaults['popup'], 'popup');
        $this->assertFalse((bool) $locked);
        $this->assertFalse((bool) $enabled);
    }
}
