# EmailVerificationEnroller

## Introduction

This is an Enrollment Flow Wedge meant to replace the standard
email verification method during an enrollment flow.

The standard email verification method send an email containing a
link which verifies that the user controls the email address and
also resumes the enrollment flow. The EmailVerificationEnroller
wedge sends the user an email containing a alphanumeric code which
must be entered into the web browser to continue the enrollment
flow. This has two advantages. First, the email template for the
verification email can contain zero hyperlinks which sometimes
cause recipient mail systems to classify the verification email as
spam. Second, the enrollment flow remains in the current web
browser session instead of stopping and resuming in a second
session like the default email verification.

## Installation

Install the plugin as you would any 
[COmanage Registry plugin](https://spaces.at.internet2.edu/x/ZwEZBg).

## Configuration

These instructions assume you already have an enrollment flow
configured using the standard email verification method.

1.  Select Configuration from the left naviation column.
1.  Select Message Templates.
1.  Click the "Add Message Template" link.
1.  Configure the Message Template.
    a.  Enter a description such as "Email verification via Code".
    a.  Set Message Context to Enrollment Flow Verification.
    a.  Set Message Subject such as "Please verify your registration
    email".
    a.  Set Message Format to Plain Text and HTML. 
    a.  Enter Message Body such as:
    ```
    <p>Hello (@CO_PERSON),</p>
    <p>Here is your code:</p>
    <p style="margin-left:50px;font:bold 1.5em monospace">(@TOKEN)</p>
    <p>Please enter this code on the web page to continue the registration process.</p>
    <p>DO NOT CLOSE YOUR WEB BROWSER UNTIL THE REGISTRATION PROCESS IS COMPLETE. </p>
    <p>Thank you,</p>
    <p>Registry Team</p>
    ```
    a.  Set Status to Active.
    a.  Click the SAVE button.
1.  Select Configuration from the left naviation column.
1.  Select Enrollment Flows.
1.  Click the "Edit" button for the enrollment flow. 
1.  Click the "Attach Enrollment Flow Wedges" link.
1.  Click the "Add Enrollment Flow Wedge" link.
1.  Configure the Enrollment Flow Wedge.
    a.  Enter a description such as "Email Verification Using
    Code".
    a.  Set the Plugin drop-down to EmailVerificationEnroller.
    a.  Set the Status to Active.
    a.  Set the Order to 1.
    a.  Click the ADD button.
1.  Back on the Enrollment Flow Wedges page, click the Configure
    button for the newly added Enrollment Flow Wedge.
1.  Configure the Enrollment Flow Wedge.
    a.  Set the Verification Email Message Template to the "Email
    verification via Code" template you created earlier.
    a.  The rest of the fields can be left as is in order to accept
    the default values. Or you can change the values as you like.
    a.  The default Verification Code Set does not contain
    ambiguous characters (e.g. one '1', uppercase 'I', lowercase
    'l'), or vowels (to reduce the possibility of offensive words).
    The code set allows only uppercase letters and numbers.
    a.  The default Verification code length is 8, but can be
    configured to 4, 8, 12, 16, or 20.
    a.  The default Verification Validity (in Minutes) is 480 (8
    hours), but can be set to any positive number.
    a.  Click the SAVE button.
1.  Use the Breadcrumbs to navigate back to the Enrollment Flow.
    a.  Set Email Confirmation Mode to None. The Wedge will then be
        used.
    a.  Remove any text in the Confirmatikon Redirect URL field. It
        is not used when the Wedge is active.
    a.  Set the Finalization Redirect URL if desired.
    a.  Click the SAVE button.

