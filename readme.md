# Contact Form 7 Controls

[![Build Status](https://travis-ci.org/kasparsd/contact-form-7-extras.svg?branch=master)](https://travis-ci.org/kasparsd/contact-form-7-extras)

Source of the [Contact Form 7 Controls plugin](https://formcontrols.com) for WordPress.


## 🚀 Get PRO

Support the continued development of this plugin by [pre-ordering the PRO version](https://formcontrols.com/pro) that will include advanced analytics and tracking features. [Learn more →](https://formcontrols.com/pro)


## Install

Search for "Contact Form 7 Controls" under "Plugins → Add New" in your WordPress dashboard.

Install as a [Composer dependancy](https://packagist.org/packages/kasparsd/contact-form-7-extras):

	composer require kasparsd/contact-form-7-extras


## Contribute

We use [Composer](https://getcomposer.org) for managing PHP related dependencies and linting while [Node.js](https://nodejs.org) is used for the triggering the `pre-commit` hook, building the plugin release and deploying to WP.org via [Grunt](https://gruntjs.com).

1. Clone the plugin repository:

	   git clone https://github.com/kasparsd/contact-form-7-extras.git
	   cd widget-context-wporg

2. Setup the development environment and tools:

	   composer install

3. Prepare a release in the `dist` directory:

	   composer build


## Screenshot

![Contact Form 7 Controls](screenshot-1.png)
