## ⚠️ DEPRECATED

**This setting and feature has been removed from the codebase.**

The `customer_registration_verify` business setting has been deprecated and removed in favor of the standard "
register-then-verify" flow using Laravel's built-in email verification (with the new `EnsureEmailIsVerified`
middleware).

### Removal Summary

The following components related to this feature have been removed:

1. **Routes:** All routes related to `/registration/verification` were removed from `routes/web.php`.
2. **Controllers:** The `VerificationFirstController.php` file was deleted.
3. **Admin Panel:** The admin toggle for `customer_registration_verify` was removed from the activation settings.
4. **Database Seed:** The SQL insert statement for `customer_registration_verify` was removed from database seeders.
5. **Frontend Views:** All conditional registration links that checked `customer_registration_verify` were simplified to
   directly use `route('user.registration')`.
6. **Test Cases:** Test setup related to `customer_registration_verify` was cleaned up.

### Current Registration Flow

The application now uses the standard Laravel registration flow:

1. User visits `/users/registration` and fills out the registration form.
2. User account is created.
3. If `email_verification` is enabled, the user is required to verify their email via the `EnsureEmailIsVerified`
   middleware.
4. Unverified users are automatically redirected to the email verification notice page until they verify.

This approach is simpler, follows Laravel conventions, and still prevents unverified users from accessing protected
resources.

---

*Document updated: February 2026*
