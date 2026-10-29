---
'contact-form-7-extras': patch
---

Pass the form error statuses (`validation_failed`, `acceptance_missing`, `spam`, `aborted`, `mail_failed`) as analytics event actions during the `wpcf7submit` event, keep the dataLayer defined before pushing events, and rely only on the native Contact Form 7 JS event API (the legacy jQuery event support has been removed).
