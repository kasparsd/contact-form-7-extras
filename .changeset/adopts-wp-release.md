---
'contact-form-7-extras': patch
---

Replace the Grunt build and deploy stack with `@wpelevator/wp-release`: npm scripts plus the Release PR flow (prepare-release, tag-release) and the `wp-release` zips and WordPress.org deploys (deploy.yml). `grunt-wp-deploy` no longer keeps wp.org trunk in sync from master; trunk only gets readme updates, and tags are built from the release zip.
