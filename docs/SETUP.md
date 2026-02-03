# Setup Guide

## Base URL
This guide assumes your Drupal site is running at:
`http://drupal10-clean.ddev.site`

If your URL is different, update the links in the README accordingly.

## Local Setup
1. `composer install`
2. Configure your Drupal database (MySQL/MariaDB).
3. Install Drupal (if not already installed).
4. Enable the module:
   `drush en event_registration`
5. Run DB updates:
   `drush updb -y`
6. Clear cache:
   `drush cr`

## Access Links
- `http://drupal10-clean.ddev.site/event/register`
- `http://drupal10-clean.ddev.site/admin/config/event-registration/event`
- `http://drupal10-clean.ddev.site/admin/config/event-registration/settings`
- `http://drupal10-clean.ddev.site/admin/reports/event-registrations`
- `http://drupal10-clean.ddev.site/admin/config/system/site-information`
- `http://drupal10-clean.ddev.site/user/login`

## Required Permissions
Assign these permissions to the relevant role:
- `administer event registrations`
- `view event registrations`
