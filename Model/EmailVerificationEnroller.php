<?php

/**
 * COmanage Registry Email Verification Enroller Model
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
class EmailVerificationEnroller extends AppModel {
  // Required by COmanage Plugins
  public $cmPluginType = "enroller";

  // Document foreign keys
  public $cmPluginHasMany = array(
    "CoEnrollmentFlowWedge" => "EmailVerificationEnroller",
    "CoMessageTemplate" => array(
      "VerificationTemplateId" => array(
        'className' => 'CoMessageTemplate',
        'foreignKey' => 'verification_template_id'
      )
    ),
  );

  // Add behaviors
  public $actsAs = array('Containable',
                         'Changelog' => array('priority' => 5));

  // Association rules from this model to other models
  public $belongsTo = array(
    "CoEnrollmentFlowWedge",
    "CoEnrollmentFlowVerMessageTemplate" => array(
      // "Verification" makes the label too long
      'className' => 'CoMessageTemplate',
      'foreignKey' => 'verification_template_id'
    ),
  );

  public $hasMany = array(
    "EmailVerificationEnroller.VerificationRequest" => array('dependent' => true)
  );

  // Default display field for cake generated views
  public $displayField = "co_enrollment_flow_wedge_id";

  // Validation rules for table elements
  public $validate = array(
    'co_enrollment_flow_wedge_id' => array(
        'content' => array(
        'rule' => 'numeric',
        'required' => true,
        'allowEmpty' => false
      )
    ),
    'verification_template_id' => array(
      'content' => array(
        'rule' => 'numeric',
        'required' => false,
        'allowEmpty' => true
      )
    ),
    'verification_validity' => array(
      'content' => array(
        'rule' => 'numeric',
        'required' => false,
        'allowEmpty' => true
      )
    ),
    'verification_code_length' => array(
      'content' => array(
        'rule' => 'numeric',
        'required' => false,
        'allowEmpty' => true,
        'last' => 'true',
      ),
      'comparison_max' => array(
        'rule' => array('comparison', 'greaterorequal', 4),
        'required' => false,
        'allowEmpty' => true,
        'message' => array('Code length must not be less than 4 characters.'),
        'last' => 'true',
      ),
      'comparison_less' => array(
        'rule' => array('comparison', 'lessorequal', 20),
        'required' => false,
        'allowEmpty' => true,
        'message' => array('Code length must not exceed 20 characters.'),
        'last' => 'true',
      ),
      'step_four' => array(
        'rule' => array('increaseStep', 4),
        'required' => false,
        'allowEmpty' => true,
        'message' => array('Code length must be increase by 4 characters, e.g. 8, 12, 16, 20.'),
        'last' => 'true',
      )
    )
  );

  /**
   * Determine if the string length can be divided by $num_of_chars.
   *
   * @since  COmanage Registry v4.3.0
   * @param array  $check        Array of fields to validate
   * @param int    $num_of_chars Length multiplier
   * @return bool
   */
  public function increaseStep($check, $num_of_chars) {
    return (int)$check["verification_code_length"]%$num_of_chars == 0;
  }
}