# Contact Form 7 Controls

[![Lint and Test](https://github.com/kasparsd/contact-form-7-extras/actions/workflows/test.yml/badge.svg)](https://github.com/kasparsd/contact-form-7-extras/actions/workflows/test.yml)

Source of the [Contact Form 7 Controls plugin](https://formcontrols.com) for WordPress.


## 🚀 Get PRO

Support the continued development of this plugin by [pre-ordering the PRO version](https://formcontrols.com/pro) that will include advanced analytics and tracking features. [Learn more →](https://formcontrols.com/pro)


## Install

Search for "Contact Form 7 Controls" under "Plugins → Add New" in your WordPress dashboard.

Install as a [Composer dependancy](https://packagist.org/packages/kasparsd/contact-form-7-extras):

	composer require kasparsd/contact-form-7-extras


## Contribute

We use [Composer](https://getcomposer.org) for managing PHP development dependencies while [Node.js](https://nodejs.org) is used for most scripting needs, building the plugin release and deploying to WP.org via [Grunt](https://gruntjs.com).

1. Clone the plugin repository:

	   git clone https://github.com/kasparsd/contact-form-7-extras.git
	   cd widget-context-wporg

2. Setup the development environment and tools:

	   npm install
	   composer install

3. Prepare a release in the `dist` directory:

	   npm run build


## Screenshot

![Contact Form 7 Controls](screenshot-1.png)

## Sample Analytics Scripts

Note: all scripts use a fake account ID `abc123`.

Google Tag Manager (GTM):

```html
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-abc123');</script>
```

Google Analytics 4 (gtag.js):

```html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-abc123"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-abc123');
</script>
```

Facebook Pixel:

```html
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', 'abc123');
</script>
```
