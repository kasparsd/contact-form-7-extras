# Contact Form 7 Controls

Contributors: kasparsd, buzztone   
Tags: Contact Form 7, cf7, admin, backend, redirect, tracking, google analytics, facebook pixel, ga, simple, interface, dashboard, recaptcha   
Requires at least: 4.6   
Tested up to: 5.1   
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

**Visit the [official plugin homepage →](https://formcontrols.com)**

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


## Installation

Search for "Contact Form 7 Controls" using the standard plugin installer.

Alternatively, add it as [a Composer dependency](https://packagist.org/packages/kasparsd/contact-form-7-extras):

	composer require kasparsd/contact-form-7-extras


## Frequently Asked Questions

### How to save Contact Form 7 submissions in the WordPress database?

The "[Storage for Contact Form 7](https://codecanyon.net/item/storage-for-contact-form-7-/7806229)" plugin stores all contact form submissions (including attachments) securely in the WordPress database. It also provides a CSV export of the form entries.



## Screenshots

1. Contact Form 7 Advanced Controls


## Changelog

### 0.7.0 (March 22, 2019)

- Add support for automatic [Facebook Pixel event tracking](https://developers.facebook.com/docs/facebook-pixel/implementation/conversion-tracking/).
- Marked as tested with WordPress 5.1.

### 0.6.2 (December 4, 2018)

- Add support for automatic [`gtag.js` Google Analytics tracking](https://support.google.com/analytics/answer/7538414).
- Marked as tested with WordPress 5.0.

### 0.6.1 (July 29, 2018)

- Fix the missing `formEventEnabled()` function.

### 0.6.0 (July 27, 2018)

- Set the minimum supported version of WordPress [to 4.6 for the community translations to work](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#loading-text-domain).
- Reformat code to match the WordPress coding standards.
- Introduce developer tooling and automated checks for PHP syntax errors and coding standards.  

### 0.5.1 (July 26, 2018)

- Add support for community translations, [fixes #19](https://github.com/kasparsd/contact-form-7-extras/issues/19).
- Mark as tested with WordPress 4.9.7.

### 0.5.0 (January 11, 2018)

- Add support for Matomo (formerly Piwik) event tracking, props [@KZeni](https://github.com/kasparsd/contact-form-7-extras/pull/16).
- Confirm that the plugin works with WordPress 4.9.1.

### 0.4.0 (September 17, 2017)

- Fix Google Analytics tracking and redirect logic.
- Confirm that the plugin works with WordPress 4.8.1.

### 0.3.5 (April 6, 2017)

- Confirm that the plugin works with WordPress 4.7.3.

### 0.3.4 (January 2, 2017)

- Fix redirect URL escaping for JS too.

### 0.3.3 (January 2, 2017)

- Fix redirect URL escaping.
- Tested with WordPress 4.7.

### 0.3.2 (November 24, 2016)

- Fix redirect functionality for both AJAX and non-AJAX form submissions.

### 0.3.1 (October 19, 2016)

- Test with the latest version of WordPress

### 0.3 (October 8, 2015)

- Add the Google Recaptcha language setting.
- Update the translation source file.

### 0.2 (September 6, 2015)

- Make compatible with Contact Form 7 version 4.3 and later.
- Rename the plugin to "Contact Form 7 Controls" in the repository.

### 0.1.5

- Bugfix: display the correct number of entries submitted for each contact form.
- Update translation POT file.

### 0.1.4

- Check if `_gaq` Google Analytics global exists before adding an event.

### 0.1.3

- Fix Google Analytics tracking for sent event too.

### 0.1.2

- Fix Google Analytics tracking with automatic GA version detection.

### 0.1.1

- Update readme.
- Use stable tags instead of trunk for releases.

### 0.1

- Initial release.
