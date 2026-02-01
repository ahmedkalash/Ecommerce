## Overview

The `customer_registration_verify` business setting in this project toggles a **"Verification First"** registration flow
for new customers.

Instead of the standard "register then verify" approach, this setting forces users to verify their identity (Email or
Phone) **before** they are allowed to fill out the registration form.

### **How it Works**

#### **1. When Disabled (Value = 0 or NULL - Default)**

* **Access:** Users go directly to the registration page (`/users/registration`).
* **Process:** They fill in their Name, Email/Phone, and Password all at once.
* **Verification:** If `email_verification` is enabled globally, the user is asked to verify their email **after** the
  account is already created.

#### **2. When Enabled (Value = 1)**

* **Access:** The standard registration page (`/users/registration`) is **disabled** (returns a 404 error). All "
  Register" links on the site are dynamically changed to point to `/registration/verification`.
* **Step 1 (Pre-Verification):** The user is presented with a simple form asking for their **Email or Phone**.
* **Step 2 (Code Delivery):** A verification code is sent via email or SMS (if the OTP addon is active).
* **Step 3 (Verification):** The user must enter the correct code.
* **Step 4 (Final Registration):** Only after successful verification is the user redirected to the full registration
  form to enter their Name and Password.
* **Auto-Verification:** Since the identity was verified upfront, the resulting user account is **automatically marked
  as verified** (`email_verified_at` is set immediately).

### **Key Technical Implementation**

* **Admin Toggle:** You can find this setting in the Admin Panel under `Setup Configurations > Activation` labeled as *
  *"Customer Registration Verification"**.
* **`HomeController.php`**:
    * `registration()`: Aborts with 404 if this setting is enabled.
    * `verifyRegEmailorPhone()`: Displays the initial verification-first view.
    * `sendRegVerificationCode()`: Handles sending the OTP/Email code.
    * `regVerifyCodeConfirmation()`: Validates the code and finally unlocks the registration form.
* **`RegisterController.php`**:
    * In the `register()` and `create()` methods, it checks this setting to decide whether to skip the usual
      post-registration verification logic, as the user is already "pre-verified".
* **Frontend Views:** Files like `nav.blade.php`, `user_login.blade.php`, and various headers use a ternary check:
  ```php
  route(get_setting('customer_registration_verify') === '1' ? 'registration.verification' : 'user.registration')
  ```

### **Why use it?**

This setting is used to **prevent spam registrations** and ensure that every account in the database belongs to a
reachable email or phone number from the very first second of its existence. It is a stricter, more secure onboarding
flow.
<br>
