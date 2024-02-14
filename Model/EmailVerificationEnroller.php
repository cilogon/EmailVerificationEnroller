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
 * @since         COmanage Registry v4.4.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */
class EmailVerificationEnroller extends AppModel {
  // Required by COmanage Plugins
  public $cmPluginType = "enroller";

  const DEFAULT_CHARSET = '234679CDFGHJKLMNPQRTVWXZ';
  const DEFAULT_VERIFICATION_VALIDITY = 480;

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

  /**
   * Actions to take before a validate operation is executed.
   *
   * @since  COmanage Registry v4.4.0
   */

  public function beforeValidate($options = array()) {
    if(empty($this->data["EmailVerificationEnroller"]["verification_code_charset"])) {
      $this->data["EmailVerificationEnroller"]["verification_code_charset"] = self::DEFAULT_CHARSET;
    }

    if(empty($this->data["EmailVerificationEnroller"]["verification_validity"])) {
      $this->data["EmailVerificationEnroller"]["verification_validity"] = self::DEFAULT_VERIFICATION_VALIDITY;
    }

    return parent::beforeValidate($options);
  }

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
    'verification_code_charset' => array(
      'content' => array(
        'rule' => 'alphaNumeric',
        'required' => false,
        'allowEmpty' => true,
        'message' => 'Letters and numbers only',
      ),
      'isUpperCase' => array(
        'rule' => array('isUpper'),
        'required' => false,
        'allowEmpty' => true,
        'message' => array('Charset must consist of UPPER case characters only'),
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
   * @since  COmanage Registry v4.4.0
   * @param array  $check        Array of fields to validate
   * @param int    $num_of_chars Length multiplier
   * @return bool
   */
  public function increaseStep($check, $num_of_chars) {
    return (int)$check["verification_code_length"]%$num_of_chars == 0;
  }

  /**
   * Does the character set consist of only UPPER case characters
   *
   * @since  COmanage Registry v4.4.0
   * @param array  $check        Array of fields to validate
   * @return bool
   */
  public function isUpper($check) {
    return ($check["verification_code_charset"] === strtoupper($check["verification_code_charset"]));
  }

  /**
   * The character set used to generate the code should be configurable, with the following limitations:
   *
   * - Only numbers and letters should be allowed.
   * - All letters must be upper case.
   * - The "default" character set should be "234679CDFGHJKLMNPQRTVWXZ". This character set was chosen using the following criteria:
   *   - No ambiguous characters, e.g., 1 (one), I (uppercase "eye"), and l (lowercase "el") look similar,
   *     depending on the font used for rendering the text.
   *   - All uppercase characters. The web browser will use JavaScript to automatically convert lowercase characters to uppercase,
   *     e.g., oninput="this.value = this.value.toUpperCase();". Additionally, the web server will ensure all uppercase characters when the web form is submitted.
   *   - No vowels. This reduces the possibility of generating offensive words.
   *
   * For readability, the code in the email may contain delimiters such as spaces or hyphens, but these extra characters
   * would not need to be entered by the user, and would be allowed (but ignored) if they were entered. An example code: 3MS-PW9C
   *
   * @param   int    $len  Requested token length, not counting dashes inserted for readability every four characters
   * @param   string $verficationCodeCharset Allowed set of characters
   *
   * @return array   [Token, Token with Dashes]
   * @throws \Random\RandomException
   * @since  COmanage Registry v4.4.0
   */

  public function generateRandomToken($len, $verficationCodeCharset) {
    // If not defined use the defautl charset
    $verficationCodeCharset = empty($verficationCodeCharset) ? self::DEFAULT_CHARSET : $verficationCodeCharset;

    // Do not allow vowels regex "/[^-b-df-hj-np-tv-z0-9]+/"
    $token = substr(preg_replace("/[^-{$verficationCodeCharset}]+/", '', base64_encode(random_bytes(500))), 0, $len);

    // Insert some dashes to improve readability
    $dtoken = $this->tokenToD($token);

    return array(strtoupper($token), strtoupper($dtoken));
  }

  /**
   * The character set used to generate the code should be configurable, with the following limitations:
   *
   * @param   string $token Token string
   *
   * @return  string        Token in human-readable form, added a dash every 4 characters
   * @throws \Random\RandomException
   * @since  COmanage Registry v4.4.0
   */

  public function tokenToD($token) {
    // Insert some dashes to improve readability
    $dtoken = '';

    for($i = 0, $iMax = strlen($token); $i < $iMax; $i++) {
      $dtoken .= $token[$i];

      if((($i + 1) % 4 == 0)
        && ($i + 1 < strlen($token))) {
        $dtoken .= '-';
      }
    }

    return $dtoken;
  }
}