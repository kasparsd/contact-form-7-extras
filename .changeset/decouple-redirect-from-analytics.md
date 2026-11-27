---
'contact-form-7-extras': patch
---

Fix the thank-you page redirect not firing when Google Analytics tracking is disabled for the form, and bail out safely when a form event arrives without a matching form config (both regressions introduced in 0.10.1).
