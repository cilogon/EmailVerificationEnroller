<?php

/**
 * COmanage Registry Verification Request Model
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
 * @since         COmanage Registry v4.4.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */
class VerificationRequest extends AppModel {
  // Add behaviors
  public $actsAs = array('Containable', 'Changelog' => array('priority' => 5));

  // Association rules from this model to other models
  public $belongsTo = array(
    "EmailVerificationEnroller.EmailVerificationEnroller",
    "CoEnrollmentFlowWedge",
    "CoPetition",
    "EmailAddress"
  );

  // Document foreign keys
  public $cmPluginHasMany = array(
    "CoPetition" => array("VerificationRequest"),
    "CoEnrollmentFlowWedge" => array("VerificationRequest"),
    "EmailAddress" => array("VerificationRequest")
  );

  // Validation rules for table elements
  public $validate = array(
    'email_verification_enroller_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'allowEmpty' => false
    ),
    'co_enrollment_flow_wedge_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'allowEmpty' => false
    ),
    'co_petition_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'allowEmpty' => false
    ),
    'attempts_count' => array(
      'rule' => 'numeric',
      'required' => false,
      'allowEmpty' => true
    ),
    'email_address_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'allowEmpty' => false
    ),
    'verification_code' => array(
      'content' => array(
        'rule' => '/.*/',
        'required' => true,
        'allowEmpty' => false,
        'last' => 'true',
      ),
      'maxLength' => array(
        'rule' => array('maxLength', 20),
        'required' => true,
        'allowEmpty' => false,
        'message' => array('Code length must not exceed 20 characters.'),
        'last' => 'true',
      ),
      'minLength' => array(
        'rule' => array('minLength', 4),
        'required' => true,
        'allowEmpty' => false,
        'message' => array('Code length must not be less than 4 characters.'),
        'last' => 'true',
      ),
    )
  );
}