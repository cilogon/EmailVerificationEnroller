<?php
/**
 * COmanage Registry Email Verification Enroller CoPetitions Controller
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

App::uses('CoPetitionsController', 'Controller');

class EmailVerificationEnrollerCoPetitionsController extends CoPetitionsController {
  // Class name, used by Cake
  public $name = "EmailVerificationEnrollerCoPetitionsController";

  public $uses = array(
    "CoPetition",
    "EmailVerificationEnroller.EmailVerificationEnroller",
    "CoMessageTemplate",
    "VerificationRequest"
  );

  /**
   * Plugin functionality following finalize step
   *
   * @param   Integer  $id        CO Petition ID
   * @param   Array    $onFinish  URL, in Cake format
   */

  protected function execute_plugin_finalize($id, $onFinish)
  {
    // Finished the updates. Return to the petition
    $this->redirect($onFinish);
  }

  /**
   * Plugin functionality following tandcPetitioner step
   * We just send the Confirmation Email with the token. We want to interact with the user
   * presenting a form where s/he will add the code we just sent.
   *
   * @param   Integer  $id        CO Petition ID
   * @param   Array    $onFinish  URL, in Cake format
   */

  protected function execute_plugin_tandcPetitioner($id, $onFinish) {
    // Just let any exceptions fall through

    $args = array();
    $args['conditions']['CoPetition.id'] = $id;
    $args['contain']['EnrolleeCoPerson'] = array(
      'EmailAddress',
      'PrimaryName'
    );
    $args['contain']['EnrolleeCoPerson']['CoPersonRole'][] = 'Cou';
    $args['contain']['EnrolleeCoPerson']['CoPersonRole']['SponsorCoPerson'][] = 'PrimaryName';
    $args['contain']['EnrolleeOrgIdentity'] = array(
      'EmailAddress',
      'OrgIdentitySourceRecord',
      'PrimaryName'
    );

    $pt = $this->CoPetition->find('first', $args);

    // The petition does not exist
    if(empty($pt)) {
      throw new InvalidArgumentException(_txt('er.notfound', array(_txt('ct.co_petitions.1'), $id)));
    }

    // Check the Wedge configuration
    $args = array();
    $args['conditions']['EmailVerificationEnroller.co_enrollment_flow_wedge_id'] = $this->viewVars['vv_efwid'];
    $args['contain'] = array(
      'CoEnrollmentFlowVerMessageTemplate',
      'CoEnrollmentFlowWedge',
      'VerificationRequest' => array(
        "conditions" => array(
          'VerificationRequest.deleted IS NOT TRUE',
          'VerificationRequest.verification_request_id IS NULL',
          "co_petition_id" => $id
        )
      )
    );

    $email_verification_enroller = $this->EmailVerificationEnroller->find('first', $args);
    $this->set('vv_email_verification_enroller', $email_verification_enroller['EmailVerificationEnroller']);
    $this->set('vv_verification_request', $email_verification_enroller['VerificationRequest']);
    $this->set('vv_petition', $pt);
    $this->set('vv_petition_id', $id);

    // Supporting self signup flows only (both authenticated/EnvSource and anonymous), without approval.
    $args = array();
    $args['conditions']['CoEnrollmentFlow.id'] = $this->enrollmentFlowID();
    $args['contain'] = false;

    $ef = $this->CoPetition->CoEnrollmentFlow->find('first', $args);

    if(!$ef) {
      throw new InvalidArgumentException(_txt('er.notfound', array(_txt('ct.co_enrollment_flows.1'),
        $pt['CoPetition']['co_enrollment_flow_id'])));
    }

    // Should we continue or abort due to unsupported configuration

    // Email verification is not required
    if ($ef["CoEnrollmentFlow"]["email_verification_mode"] != VerificationModeEnum::Review) {
      $this->log(__METHOD__ . "::message " . _txt('pl.email_verification_enrollers.verification.not.req'), LOG_DEBUG);
      $this->redirect($onFinish);
    }

    // Enrollment Authorization level is not supported
    $supported_authz_levels = array(EnrollmentAuthzEnum::AuthUser, EnrollmentAuthzEnum::None);
    if(!in_array($ef["CoEnrollmentFlow"]["authz_level"], $supported_authz_levels)) {
      $this->log(__METHOD__ . "::message " . _txt('pl.email_verification_enrollers.authz'), LOG_DEBUG);
      $this->redirect($onFinish);
    }

    // Not supported if approval mode is enabled
    if($ef["CoEnrollmentFlow"]["approval_required"] === true) {
      $this->log(__METHOD__ . "::message " . _txt('pl.email_verification_enrollers.appr.req'), LOG_DEBUG);
      $this->redirect($onFinish);
    }


    // We have a verification request for this petition that waits to be served
    if ($this->request->is('post')) {
      if(empty($email_verification_enroller['VerificationRequest'])) {
        // Something is wrong. We can not have a post request without a record waiting
        $this->redirect($onFinish);
      }

      // Start a transaction
      // Check the code

      // Verify the email

      // Delete the verfication code from the database
      // Close the transaction

      // todo: Redirect after the email confirmation.
      $this->redirect($onFinish);
    }

    // GET Request

    // Look for an email address to confirm. Pending the big email confirmation
    // rewrite currently scheduled for v4.0.0, we first look for an address
    // associated with the Org Identity, and then for an address associated with
    // the CO Person. We skip already verified addresses, as well as those
    // associated with Org Identity Sources (since we can't update those
    // Org Identities).

    $toEmail = null;
    $coPersonEmail = false;

    if(!empty($pt['EnrolleeOrgIdentity']['EmailAddress'])
      // If there's an OrgIdentitySourceRecord we can't write to any
      // associated EmailAddress, so skip this OrgIdentity
      && empty($pt['EnrolleeOrgIdentity']['OrgIdentitySourceRecord'])) {
      foreach($pt['EnrolleeOrgIdentity']['EmailAddress'] as $ea) {
        if(!$ea['verified']) {
          // Use this address
          $toEmail = $ea;
          break;
        }
      }
    }

    if(!$toEmail) {
      // No Org Identity Email, check the CO Person record

      if(!empty($pt['EnrolleeCoPerson']['EmailAddress'])) {
        foreach($pt['EnrolleeCoPerson']['EmailAddress'] as $ea) {
          if(!$ea['verified']) {
            // Use this address
            $toEmail = $ea;
            $coPersonEmail = true;
            break;
          }
        }
      }
    }

    // Should we proceed with Email Confirmation or not?
    if(!$toEmail) {
      throw new RuntimeException(_txt('er.pt.mail',
                                      array(!empty($pt['EnrolleeCoPerson']['PrimaryName'])
                                        ? generateCn($pt['EnrolleeCoPerson']['PrimaryName'])
                                        : _txt('fd.enrollee.new'))));
    }

    $verification_code = generateRandomToken($email_verification_enroller['EmailVerificationEnroller']["verification_code_length"] ?? 8);

    // Generate additional substitutions to supplement those handled by CoInvites.
    // This is separate from the substitutions managed by CoNotification.
    $subs = array(
      'CO_PERSON' => (!empty($pt['EnrolleeCoPerson']['PrimaryName'])
        ? generateCn($pt['EnrolleeCoPerson']['PrimaryName']) : _txt('fd.enrollee.new')),
      'NEW_COU'   => (!empty($pt['EnrolleeCoPerson']['CoPersonRole'][0]['Cou']['name'])
        ? $pt['EnrolleeCoPerson']['CoPersonRole'][0]['Cou']['name'] : null),
      'SPONSOR'   => (!empty($pt['EnrolleeCoPerson']['CoPersonRole'][0]['SponsorCoPerson']['PrimaryName'])
        ? generateCn($pt['EnrolleeCoPerson']['CoPersonRole'][0]['SponsorCoPerson']['PrimaryName']) : null),
      'TOKEN'     => $verification_code
    );

    // Save to verification request
    $data = array(
      'co_enrollment_flow_wedge_id' => $email_verification_enroller['EmailVerificationEnroller']['co_enrollment_flow_wedge_id'],
      'email_verification_enroller_id' => $email_verification_enroller['EmailVerificationEnroller']['id'],
      "co_petition_id" => $id,
      'verification_code' => $verification_code,
      'email_address_id' => $toEmail['id'],
      'attempts_count' => 1,
    );

    $this->VerificationRequest->clear();
    if(!$this->VerificationRequest->save($data)) {
      $this->log(__METHOD__ . "::invalid_fields::message" . print_r($this->VerificationRequest->invalidFields(), true), LOG_ERROR);
      throw new RuntimeException(_txt('er.verification_request.db.save'));
    }

    // Sent the email
    $this->CoMessageTemplate->templateSend($email_verification_enroller['EmailVerificationEnroller']["verification_template_id"],
                                           $toEmail['mail'],
                                           $subs);


    // Render the add code view

  }
}