---
"contact-form-7-extras": patch
---

Security fix: only store the declared form-tag fields and neutralize Shortcode brackets in values stored in TablePress, preventing unauthenticated Shortcode injection into public table render output. Reported by [kta1kri](https://github.com/kta1kri) through responsible disclosure.
