## 0.10.2 (2026-11-10)

- Security fix: only store the declared form-tag fields and neutralize Shortcode brackets in values stored in TablePress, preventing unauthenticated Shortcode injection into public table render output. Reported by [kta1kri](https://github.com/kta1kri) through responsible disclosure.

## 0.10.1 (2026-10-29)

- Replace the Grunt build and deploy stack with `@wpelevator/wp-release`: npm scripts plus the Release PR flow (prepare-release, tag-release) and the `wp-release` zips and WordPress.org deploys (deploy.yml). `grunt-wp-deploy` no longer keeps wp.org trunk in sync from master; trunk only gets readme updates, and tags are built from the release zip.
- Pass the form error statuses (`validation_failed`, `acceptance_missing`, `spam`, `aborted`, `mail_failed`) as analytics event actions during the `wpcf7submit` event, keep the dataLayer defined before pushing events, and rely only on the native Contact Form 7 JS event API (the legacy jQuery event support has been removed).

