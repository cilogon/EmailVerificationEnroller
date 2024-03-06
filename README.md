# EmailVerificationEnroller

## Introduction

This is an Enrollment Flow Wedge meant to replace the standard
email verification method during an enrollment flow.

The standard email verification method sends an email containing a
link which verifies that the user controls the email address and
also resumes the enrollment flow. The EmailVerificationEnroller
wedge sends the user an email containing a alphanumeric code which
must be entered into the web browser to continue the enrollment
flow. This has two advantages. First, the email template for the
verification email can contain zero hyperlinks which sometimes
cause recipient mail systems to classify the verification email as
spam. Second, the enrollment flow remains in the current web
browser session instead of stopping and resuming in a second
session as with default email verification.

## Installation

Install the plugin as you would any
[COmanage Registry plugin](https://spaces.at.internet2.edu/x/ZwEZBg).

## Configuration

These instructions assume you already have an enrollment flow
configured using the standard email verification method.

1.  Select Configuration from the left navigation column.
1.  Select Message Templates.
1.  Click the "Add Message Template" link.
1.  Configure the Message Template.
    - Enter a description such as "Email verification via Code".
    - Set Message Context to Enrollment Flow Verification.
    - Set Message Subject such as "Please verify your registration
      email".
    - Set Message Format to Plain Text and HTML.
    - Enter Message Body such as:
    ```
    <p>Hello (@CO_PERSON),</p>
    <p>Here is your code:</p>
    <p style="margin-left:50px;font:bold 1.5em monospace">(@TOKEN)</p>
    <p>Please enter this code on the web page to continue the registration process.</p>
    <p>DO NOT CLOSE YOUR WEB BROWSER UNTIL THE REGISTRATION PROCESS IS COMPLETE. </p>
    <p>Thank you,</p>
    <p>Registry Team</p>
    ```
    - Set Status to Active.
    - Click the SAVE button.
1.  Select Configuration from the left naviation column.
1.  Select Enrollment Flows.
1.  Click the "Edit" button for the enrollment flow.
1.  Click the "Attach Enrollment Flow Wedges" link.
1.  Click the "Add Enrollment Flow Wedge" link.
1.  Configure the Enrollment Flow Wedge.
    -  Enter a description such as "Email Verification Using Code".
    -  Set the Plugin drop-down to EmailVerificationEnroller.
    -  Set the Status to Active.
    -  Set the Order to 1.
    -  Click the ADD button.
1.  Configure the Enrollment Flow Wedge.
    - Set the Verification Email Message Template to the "Email
      verification via Code" template created earlier.
    - The rest of the fields can be left untouched in order to accept
      the default values. Or you can change the values as you like.
    - The default Verification Code Set does not contain
      ambiguous characters (e.g. one `1`, uppercase `I`, lowercase
      `l`), or vowels (to reduce the possibility of offensive words).
      The code set allows only uppercase letters and numbers.
    - The default Verification code length is 8, but can be
      configured to 4, 8, 12, 16, or 20. Hyphens will be added to
      the code every 4 characters for readability (but are not
      required to be entered by the user).
    - The default Verification Validity is 480 minutes (8 hours),
      but can be set to any positive number.
    - Click the SAVE button.
1.  Use the Breadcrumbs to navigate back to the Enrollment Flow.
    - Set Email Confirmation Mode to None. The Wedge will then be
      used.
    - Remove any text in the Confirmation Redirect URL field. It
      is not used when the Wedge is active.
    - Set the Finalization Redirect URL if desired.
    - Click the SAVE button.
1.  (OPTIONAL) Change the default warning message in the "Enter
    Code to Verify Your Email Address" web form page.
    1.  Select `Configuration` from the left navigation column.
    1.  Select `Localizations`.
    1.  Click `Add Localization`.
    1.  Configure the Localization.
        - Set the Key to pl.verification\_request.verify.info
        - Set the Language to en\_US.
        - Enter the Text such as:
        ```
        DO NOT CLOSE YOUR BROWSER OR NAVIGATE AWAY FROM THIS PAGE.
        If you have problems, please
        <a target="_blank" href="https://identity.access-ci.org/help">Open a Help Ticket</a>.
        ```
        - Click the SAVE button.
1. ENVSource plugin Configuration
    - Set Enrollment Source [Mode](https://spaces.at.internet2.edu/display/COmanage/Enrollment+Sources) to `Authenticate` 