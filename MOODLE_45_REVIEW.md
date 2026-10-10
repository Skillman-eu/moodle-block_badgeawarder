# Moodle 4.5 compatibility review

Reviewed the Skillman customizations at `3f98d30` against Moodle's
`MOODLE_405_STABLE` APIs on 10 October 2026.

## Upgrade failure and deployment

Moodle 4.5 removed `MESSAGE_DEFAULT_LOGGEDIN` and `MESSAGE_DEFAULT_LOGGEDOFF`
(MDL-73284). `message_airnotifier` exposes the error because its upgrade reloads
all registered providers, including this block's `db/messages.php`. The failing
component is the block's provider definition, not Airnotifier.

The email default is now:

```php
'email' => MESSAGE_FORCED + MESSAGE_DEFAULT_ENABLED,
```

The provider name is preserved. Email remains forced; popup remains permitted
and disabled by default. No compatibility constants should be added to core.
Existing stored notification settings are preserved: changing a provider file
does not forcibly overwrite administrator or user preferences.

Deploy the updated block files before retrying the site upgrade. From the Moodle
root, using the site's configured PHP executable:

```sh
php admin/cli/upgrade.php --non-interactive
php admin/cli/purge_caches.php
```

The version increment registers the updated plugin. No schema migration is
needed. Run the normal upgrade rather than editing database version records.

## Fixed runtime regressions

| Finding | Change |
| --- | --- |
| A `continue` outside the CSV loop makes `processor.php` fail compilation on PHP 8.1. | Removed the leftover code after the loop and moved custom notification fields into `finalize_award()` for each recipient. |
| The refactored award path calls messaging without the badge description, subject, issued hash, or attachment settings. | Populate all fields before sending, preserving custom subjects, placeholders, and links. |
| Missing `f3.png` causes a method call on null. | Send without an attachment when the image is unavailable. |
| Existing-user template references an undefined generated password. | Remove the credentials section from that template; keep credentials for newly created accounts. |
| Moodle 4.5 allows duplicate badge names (MDL-43938); `get_record()` assumes uniqueness. | Detect ambiguous names and ask for unique names before import, rather than selecting an arbitrary badge. |
| Plain-text notification body is labelled HTML. | Set `FORMAT_PLAIN`, keep the HTML alternative, and supply notification/course/summary fields. |
| Country normalization inspects the unset property rather than the submitted option. | Normalize the submitted `0` value; initialize optional country and city to empty strings. |
| Notification preferences lack a provider label. | Add `messageprovider:badge_awarding_message`. |

The existing migration already replaced `print_error()` with exceptions,
declared institution/department properties, used `user_create_user()`, escaped
CSV table cells, and added course enrolment/award capability checks. Those
changes are retained. The global `badge` class alias, CSV APIs, manual award API,
and message object used here remain available in Moodle 4.5.

## Remaining issues (not introduced by Moodle 4.5)

- Enrolment assumes student role ID `5`. Resolve a configured/default student
  role rather than assuming database IDs on customized sites.
- Manual award attribution assumes exactly one role with archetype `teacher`,
  regardless of the issuer's actual roles or configured badge criteria. A
  missing or duplicated archetype can fail; attribution can be inaccurate.
- New account creation checks course enrolment/award capabilities but does not
  separately require system `moodle/user:create`. Review whether delegated
  teachers should be allowed to create site accounts before changing that policy.
- Recipient lookups do not exclude deleted accounts or scope usernames to the
  local MNet host, and duplicate emails can make `get_record()` fail.
- Execution does not recheck all required CSV fields before using them, and the
  final error counter is never incremented. Malformed rows and reported totals
  need separate handling.
- Issuing with `issue($userid, true)` intentionally skips core badge baking and
  notification. The attachment remains a plain course badge image, not a baked
  recipient assertion. Review separately if portable badge attachments are needed.
- Import is not atomic. An ambiguous name encountered after earlier rows have
  been processed stops the import without rolling those rows back. Preview and
  resolve duplicate names before submitting the final import.

## Validation

- All 21 PHP files pass PHP 8.1 syntax checks; `git diff --check` passes.
- An isolated PHP 8.1 smoke harness, with Moodle dependencies stubbed and PHP
  warnings promoted to exceptions, passed provider loading without legacy
  constants, per-recipient links, missing images, existing-user notification
  content, and failed message-send handling. This is not Moodle integration testing.
- Added a provider regression test; updated notification tests to use
  `redirectMessages()` and added multi-recipient/missing-image and duplicate-name
  regressions. PHPUnit/Behat were not run: this workspace has no configured
  Moodle 4.5 test installation or database.

On a Moodle 4.5 test installation with this plugin and PHPUnit initialized:

```sh
vendor/bin/phpunit blocks/badgeawarder/tests/messages_test.php
vendor/bin/phpunit blocks/badgeawarder/tests/processor_test.php
```

Then verify a real email with and without an image, a new account and existing
account import, custom message placeholders, and upgrade from a copy of the
affected database. Message redirection tests do not verify SMTP delivery.

## Sources

- [Moodle 4.5 release notes](https://moodledev.io/general/releases/4.5)
- [Moodle 4.5 Message API](https://moodledev.io/docs/4.5/apis/core/message)
- [Message default translation](https://github.com/moodle/moodle/blob/MOODLE_405_STABLE/message/lib.php)
- [Provider reload and preference initialization](https://github.com/moodle/moodle/blob/MOODLE_405_STABLE/lib/messagelib.php)
- [Badge class and issuance](https://github.com/moodle/moodle/blob/MOODLE_405_STABLE/badges/classes/badge.php)
