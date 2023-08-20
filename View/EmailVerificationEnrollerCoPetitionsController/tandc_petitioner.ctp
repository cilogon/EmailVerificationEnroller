<?php
/**
 * COmanage Registry tandc Petitioner Fields (used to display both petitions and petition-based invitations)
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
 * @link          https://www.internet2.edu/comanage COmanage Project
 * @package       registry
 * @since         COmanage Registry v4.3.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */


$this->Html->css('EmailVerificationEnroller.tandc_petitioner', array('inline' => false));


$model = $this->name;
$req = Inflector::singularize($model);

$submit_label = _txt('op.submit');
print $this->Form->create(
  $req,
  array(
    'inputDefaults' => array(
      'label' => false,
      'div' => false
    )
  )
);

print $this->Form->hidden('co_id', array('default' => $cur_co['Co']['id'])) . PHP_EOL;
print $this->Form->hidden('co_enrollment_flow_wedge_id', array('default' => $vv_efwid)) . PHP_EOL;

?>

<div class="d-flex justify-content-center align-items-center container mt-n12">
  <div class="card py-5 px-3">
    <h5 class="m-0 text-center"><?php print _txt('pl.verification_request.verify.title');?></h5>
    <span class="mobile-text"><?php print _txt('pl.verification_request.verify.mail', array($vv_to_email["mail"]));?></span>
    <div class="d-flex flex-row mt-5 justify-content-center">
      <?php
        $code_parts = (int)$vv_email_verification_enroller["verification_code_length"]/4;

        for($i = 0; $i < $code_parts; $i++) {
          $options = array(
            'type' => 'text',
            'class' => "",
            'size' => 4,
            'maxLength' => 4);

          if($i === 0) {
            $options['class'] .= 'focusFirst';
          }

          if($i > 0) {
            print $this->Html->tag('span', '-', array('class' => 'mx-2'));
          }

          print $this->Form->input( $req . '.verification_code.' . $i, $options);
        }
      ?>
    </div>
    <div class="text-center mt-5">
      <?php
      print $this->Form->submit($submit_label);
      print $this->Form->end();
      ?>
<!--      <span class="d-block mobile-text">Don't receive the code?</span>-->
<!--      <span class="font-weight-bold text-danger cursor">Resend</span>-->
    </div>
  </div>
</div>