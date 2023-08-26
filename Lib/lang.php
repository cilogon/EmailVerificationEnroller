<?php
/**
 * COmanage Registry Email Verification Enroller Plugin Language File
 *
 * Portions licensed to the University Corporation for Advanced Internet
 * Development, Inc. ("UCAID") under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.
 *
 * UCAID licenses this file to you under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with the
 * License. You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry-plugin
 * @since         COmanage Registry v4.3.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */

global $cm_lang, $cm_texts;

// When localizing, the number in format specifications (eg: %1$s) indicates the argument
// position as passed to _txt.  This can be used to process the arguments in
// a different order than they were passed.

$cm_email_verification_enroller_texts['en_US'] = array(
  // Titles, per-controller
  'ct.email_verification_enrollers.1'               => 'Email Verificiation Enroller',
  'ct.email_verification_enrollers.pl'              => 'Email Verificiation Enrollers',

  // Error texts
  'er.verification_request.db.save'                 => 'Failed to save Verification Request',
  'er.verification_request.code.validation'         => 'Verification Code does not match',
  'er.verification_request.max.attempts'            => 'Max verification attempts exceeded',
  'er.verification_request.validity.exc'            => 'Verification Validity time window exceeded',


  // Fields
  'fd.email_verification_enrollers.code.len'              => 'Verificiation Code Length',
  'fd.email_verification_enrollers.code.len.desc'         => 'Set the verification code length. Minimum/Default length is 8. Maximum is 20. Length step is 4.',
  'fd.email_verification_enrollers.msg.templ'             => 'Message Template',
  'fd.email_verification_enrollers.msg.templ.desc'        => 'Select the Verification Message Template to send to the Enrollee',
  'fd.email_verification_enrollers.validity'              => 'Verification Validity (Minutes)',
  'fd.email_verification_enrollers.validity.desc'         => 'When confirming an email address, the length of time (in minutes) the confirmation code is valid for (default is 8 hours = 480 minutes)',
  'fd.email_verification_enrollers.verification.attemp'   => 'Verification attempt #%1$s',

  // Actions

  // Plugin texts
  'pl.email_verification_enrollers.verification.not.req' => 'email verification not required',
  'pl.email_verification_enrollers.authz'                => 'authorization level not supported',
  'pl.email_verification_enrollers.appr.req'             => 'not supported if approval is required',
  'pl.email_verification_enrollers.verif.review'         => 'not supported if verification mode is not review',

  // Verify Request
  'pl.verification_request.verify.title'      => 'Please Verify your Email Address',
  'pl.verification_request.verify.mail'       => 'Enter the code we just send on your emai address <b class="text-danger">%1$s</b>',
);
