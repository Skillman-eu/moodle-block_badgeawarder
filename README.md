# Badge CSV Awarder

## What is Badge CSV Awarder?
Save time and effort awarding multiple course badges to your students, by using one CSV file upload instead.

You also have an optional feature to create users using their details, if enabled in the configuration, and to enrol them onto the badge's course.

## License
2026 Onward [Brickfield Education Labs](https://www.brickfield.ie)

## Copyright
2026 Onward [Brickfield Education Labs](https://www.brickfield.ie)

## Version support
This plugin has been developed to work on Moodle releases 4.5, 5.0, and 5.1.

## Funding credits
Initial funding for this plugin was provided by the [HSA](https://www.hsa.ie/).

## Development
This plugin has been developed by [Learning Technology Services](https://www.lts.ie) and is maintained by Brickfield Education Labs.

Original Lead Developer: Bas Brands

## Important Links
* [Code repository](https://github.com/brickfield/moodle-block_badgeawarder)
* [Badge CSV Awarder user guide](https://docs.brickfield.ie/block-badgeawarder/)

## Installation
1. Unzip and copy the "badgeawarder" folder into Moodle's "blocks/" folder
2. Visit the admin page to install the plugin

Further installation instructions can be found on the
"[Installing plugins](http://docs.moodle.org/en/Installing_contributed_modules_or_plugins)" Moodle documentation page.

## Usage
How to use Badge Awarder in a course? 
----------------------------------------- 
Login as an Administrator or Teacher or an account with course editing privilege. 

Navigate to the course you wish to award the badges for.

Turn editing on and add the Badge Awarder block within the new course.

You will then see the Badge Awarder block with its link, "Upload Badges CSV".  
Once clicked, this will bring you to the Badge CSV upload page. 

The Badge CSV upload page uses the File Picker for you to select the relevant CSV file to upload.  
This also allows you to select the delimiter, encoding and number of rows to show on the Preview screen.

There are also three Import modes:  
1. Award to new users only - this will parse the CSV file and only process those users which do not 
already exist on the Moodle site.  
2. Award to all users, create non-existing users - this will parse the CSV file and process all rows.  
All existing users will be enrolled on the course and awarded their badge.  
All non-existing users will be enrolled, have their login details mailed to their email account, and then also be enrolled on the course and awarded their badge.  
3. Award to existing users only - this will parse the CSV file and only process those users which do already exist on the Moodle site.

Click on the Preview button to submit the CSV file and view the pending user details before they are finally submitted, to review in case any changes are needed.

On the Preview page, if you are processing new users, you will also need to select their Country and City for the submission process.

Once satisfied with the preview details, click on the "Award badges" button and the CSV file will be fully processed.  
Once completed, the page will display the results of the CSV file upload, including  
1. Total Badges awarded.
2. Number of accounts created.
3. Existing Users Enrolled.
4. Any award errors.

====================================================================
PHP 8.1 / 8.3 compatibility review (25 November 2026)
--------------------------------------------------
The plugin uses PHP 8.1-compatible syntax and APIs. Review against the PHP 8.2
and 8.3 migration guides found no additional language incompatibilities in the
normal workflow. Processor properties are declared; per-recipient metadata is
added to stdClass records, which permit dynamic properties. No PHP 8.3-only
features or AllowDynamicProperties suppression were introduced.

Incomplete CSV rows are now normalized and rejected before account lookup or
creation. This prevents undefined array keys and null string arguments under
PHP 8.1–8.3. Missing-field rows are counted as errors; valid later rows continue.

Validation here: all PHP files pass PHP 8.1 syntax checks and an isolated parser
smoke check passes with warnings promoted to exceptions. PHP 8.3 is not installed
locally, and no configured Moodle PHPUnit installation is available; the Moodle
integration tests have not been run here. Run the plugin suite from a separate
Moodle 4.5 PHPUnit installation under both PHP 8.1 and PHP 8.3:

```sh
vendor/bin/phpunit blocks/badgeawarder/tests
```

References: [PHP 8.2 deprecations](https://www.php.net/manual/en/migration82.deprecated.php),
[PHP 8.3 incompatible changes](https://www.php.net/manual/en/migration83.incompatible.php),
[PHP 8.3 deprecations](https://www.php.net/manual/en/migration83.deprecated.php).

Skillman adjustments:
- badge description added to the user's badge award email
- auto-generate email by pattern (with random characters)
- permission to upload csv for non-editing teacher.
ATTENTION! Permission to upload csv for non-editing teacher might not works! Must be defined explicitly on the page 
/admin/roles/manage.php->"Non-editing teacher"->Edit->block/badgeawarder:uploadcsv->"Allow"
