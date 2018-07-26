# Contact Form 7 Controls

Contributors: kasparsd, buzztone   
Tags: Contact Form 7, cf7, admin, backend, redirect, tracking, google analytics, ga, simple, interface, dashboard, recaptcha   
Requires at least: 3.0   
Tested up to: 4.9.7   
Stable tag: {{ version }}   

Simple controls for some of the advanced Contact Form 7 plugin functionality.


## Description

This plugin enables simple controls for some of the advanced features of the [Contact Form 7](http://wordpress.org/plugins/contact-form-7/) plugin:

- Disable AJAX form submissions
- Disable default form CSS
- Disable automatic paragraph formatting
- Disable HTML5 input field types or enable the HTML5 input type fallback
- Track form submissions, errors and completions with Google Analytics and Matomo (formerly Piwik)
- Redirect to URL on form submission
- Specify the Google Recaptcha language

Please note that some settings work on per-page level and will inherit for all forms on the same page. For example, disabling AJAX form submissions for one form will disable AJAX submissions on all forms on the same page.

## Requirements

- [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) version 4.3 or later for features related to submission tracking and redirects.


### How to Contribute

- Report issues and submit improvements in the [GitHub repository](https://github.com/kasparsd/contact-form-7-extras).
- Add [a translation to your language](https://translate.wordpress.org/projects/wp-plugins/contact-form-7-extras).


## Installation

Search for "Contact Form 7 Controls" using the standard plugin installer.


## Frequently Asked Questions

### How to save Contact Form 7 submissions in the WordPress database?

The "[Storage for Contact Form 7](https://codecanyon.net/item/storage-for-contact-form-7-/7806229)" plugin stores all contact form submissions (including attachments) securely in the WordPress database. It also provides a CSV export of the form entries.



## Screenshots

1. Contact Form 7 Advanced Controls


## Changelog

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
