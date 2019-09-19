# Controls for Contact Form 7

Contributors: kasparsd, buzztone   
Tags: Contact Form 7, cf7, forms, form, admin, backend, redirect, tracking, analytics, google analytics, facebook pixel, ga, simple, interface, dashboard, recaptcha   
Requires at least: 4.6   
Tested up to: 5.2   
Stable tag: STABLETAG   

Simple controls, analytics, tracking and redirects for Contact Form 7.


## Description

This plugin enables simple controls for some of the advanced features of the [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) plugin:

- Track form submissions, errors and completions [with Google Analytics, Matomo (formerly Piwik) and Facebook Pixel](https://formcontrols.com/docs).
- Redirect to URL on form submission
- Disable AJAX form submissions
- Disable default form CSS
- Disable automatic paragraph formatting
- Disable HTML5 input field types or enable the HTML5 input type fallback
- Specify the Google Recaptcha language

Please note that some settings work on per-page level and will inherit for all forms on the same page. For example, disabling AJAX form submissions for one form will disable AJAX submissions on all forms on the same page.

### 🚀 Get PRO

Support the continued development of this plugin by [pre-ordering the PRO version](https://formcontrols.com/pro) that will include advanced analytics and tracking features. [Learn more →](https://formcontrols.com/pro)

### Requirements

- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) version 4.3 or later for features related to submission tracking and redirects.

### Usage

The plugin adds a new "Customize" tab for each Contact Form 7 form in the WordPress administration area.

### Analytics Tracking

The plugin automatically triggers analytics events for the following services:

- [Google Analytics](https://analytics.google.com/analytics/web/) with `ga()`, `_gaq.push()` and `dataLayer.push()` implementations,
- [Matomo](https://matomo.org/) (formerly Piwik),
- [Facebook Pixel Conversion Tracking](https://developers.facebook.com/docs/facebook-pixel/implementation/conversion-tracking).

It passes the following data with the event:

- "Contact Form" as the event category,
- "Submit", "Sent", "Error" or "Spam" as the event action, and
- the form title as the event title.

#### Facebook Pixel

The [standard Contact event](https://developers.facebook.com/docs/facebook-pixel/implementation/conversion-tracking#standard-events) is used for Facebook Pixel with `content_category` property set to the event type (Submit, Sent, Error, Spam) and `content_name` set to the form title.

### Contribute

- Report issues and suggest improvements [on GitHub](https://github.com/kasparsd/contact-form-7-extras).
- Add [a translation to your language](https://translate.wordpress.org/projects/wp-plugins/contact-form-7-extras).


## Changelog

See the [release notes](https://github.com/kasparsd/contact-form-7-extras/releases) for the complete changelog.


## Installation

Search for "Controls for Contact Form 7" under "Plugins" → "Add New" in your WordPress administration panel.

Alternatively, add it as [a Composer dependency](https://packagist.org/packages/kasparsd/contact-form-7-extras):

	composer require kasparsd/contact-form-7-extras


## Frequently Asked Questions

### How to save Contact Form 7 submissions in the WordPress database?

The "[Storage for Contact Form 7](https://codecanyon.net/item/storage-for-contact-form-7-/7806229)" plugin stores all contact form submissions (including attachments) securely in the WordPress database. It also provides a CSV export of the form entries.


## Screenshots

1. Advanced Controls for Contact Form 7


## Upgrade Notice

### 0.7.1

Documentation update to mention our new website. Support the continued development of this plugin by pre-ordering the PRO version 🚀 that will include advanced analytics and tracking features.
