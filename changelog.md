# Changelog

## 0.8.1 (July 11, 2024)

- Confirm that plugin works with WordPress 6.5.
- Tooling: update development tooling.

## 0.8.0 (July 23, 2020)

- Fix: Use the suggested Google Global Site Tag (gtag.js) [event structure](https://developers.google.com/analytics/devguides/collection/gtagjs/events). This will make the "Contact Form" events appear in both Google Analytics and Google Tag Manager.
- Invite users to subscribe to the [🚀 PRO version](https://formcontrols.com/pro) of the plugin. This allows me to continue updating and supporting the plugin for all the 10,000+ active users of the plugin.

## 0.7.3 (July 22, 2020)

- Compatibility with the Javascript event changes in the latest [version 5.2 of the Contact Form 7 plugin](https://contactform7.com/2020/07/04/contact-form-7-52/).
- Mark as tested with WordPress 5.4.

## 0.7.2 (September 19, 2019)

- Rename the plugin to "Controls for Contact Form 7" for trademark compliance.
- Mark as tested with WordPress 5.2.

## 0.7.1 (April 3, 2019)

- Documentation update to link to our [new homepage](https://formcontrols.com).

## 0.7.0 (March 22, 2019)

- Add support for automatic [Facebook Pixel event tracking](https://developers.facebook.com/docs/facebook-pixel/implementation/conversion-tracking/).
- Marked as tested with WordPress 5.1.

## 0.6.2 (December 4, 2018)

- Add support for automatic [`gtag.js` Google Analytics tracking](https://support.google.com/analytics/answer/7538414).
- Marked as tested with WordPress 5.0.

## 0.6.1 (July 29, 2018)

- Fix the missing `formEventEnabled()` function.

## 0.6.0 (July 27, 2018)

- Set the minimum supported version of WordPress [to 4.6 for the community translations to work](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#loading-text-domain).
- Reformat code to match the WordPress coding standards.
- Introduce developer tooling and automated checks for PHP syntax errors and coding standards.  

## 0.5.1 (July 26, 2018)

- Add support for community translations, [fixes #19](https://github.com/kasparsd/contact-form-7-extras/issues/19).
- Mark as tested with WordPress 4.9.7.

## 0.5.0 (January 11, 2018)

- Add support for Matomo (formerly Piwik) event tracking, props [@KZeni](https://github.com/kasparsd/contact-form-7-extras/pull/16).
- Confirm that the plugin works with WordPress 4.9.1.

## 0.4.0 (September 17, 2017)

- Fix Google Analytics tracking and redirect logic.
- Confirm that the plugin works with WordPress 4.8.1.

## 0.3.5 (April 6, 2017)

- Confirm that the plugin works with WordPress 4.7.3.

## 0.3.4 (January 2, 2017)

- Fix redirect URL escaping for JS too.

## 0.3.3 (January 2, 2017)

- Fix redirect URL escaping.
- Tested with WordPress 4.7.

## 0.3.2 (November 24, 2016)

- Fix redirect functionality for both AJAX and non-AJAX form submissions.

## 0.3.1 (October 19, 2016)

- Test with the latest version of WordPress

## 0.3 (October 8, 2015)

- Add the Google Recaptcha language setting.
- Update the translation source file.

## 0.2 (September 6, 2015)

- Make compatible with Contact Form 7 version 4.3 and later.
- Rename the plugin to "Contact Form 7 Controls" in the repository.

## 0.1.5

- Bugfix: display the correct number of entries submitted for each contact form.
- Update translation POT file.

## 0.1.4

- Check if `_gaq` Google Analytics global exists before adding an event.

## 0.1.3

- Fix Google Analytics tracking for sent event too.

## 0.1.2

- Fix Google Analytics tracking with automatic GA version detection.

## 0.1.1

- Update readme.
- Use stable tags instead of trunk for releases.

## 0.1

- Initial release.
