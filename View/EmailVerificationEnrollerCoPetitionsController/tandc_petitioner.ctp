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
 * @since         COmanage Registry v4.4.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */


$this->Html->css('EmailVerificationEnroller.tandc_petitioner', array('inline' => false));
$this->Html->script('EmailVerificationEnroller.timer', array('inline' => false));


$model = $this->name;
$req = Inflector::singularize($model);

$submit_label = _txt('op.submit');
print $this->Form->create(
  $req,
  array(
    'id' => 'verification-code-form',
    'inputDefaults' => array(
      'label' => false,
      'div' => false
    )
  )
);

print $this->Form->hidden('co_id', array('default' => $cur_co['Co']['id'])) . PHP_EOL;
print $this->Form->hidden('co_enrollment_flow_wedge_id', array('default' => $vv_efwid)) . PHP_EOL;

?>

<script type="text/javascript">
  const seconds = <?php print (int)($vv_verification_request[0]['attempts_count'] ?? 0) * 1?>;

  $(document).ready(function() {
    if(seconds > 0.5) {
      appendTimerToDocument(seconds)
      startTimer(seconds, seconds)
    }
    setTimeout(() => {
      // i should hide the boxes here and present the spinner by default
      $("#app-timer").hide()
      $("#verification-code-card").show();
    }, seconds*1000);

    $("#verification-code-form").on("submit", function() {
      $("#verification-code-card").addClass('d-none');
      appendTimerToDocument(seconds)
      startTimer(seconds, seconds)
    })
  });

</script>

<div id="verification-code-container" class="d-flex justify-content-center align-items-center container mt-n12">
  <div id="app-timer" class="display: none"></div>
  <div id="verification-code-card"
       class="card py-5 px-3"
       style="display: none">
    <h5 class="m-0 text-center"><?php print _txt('pl.verification_request.verify.title');?></h5>
    <span class="mobile-text"><?php print _txt('pl.verification_request.verify.mail', array($vv_to_email["mail"]));?></span>
    <span class="mobile-text"><?php print _txt('fd.email_verification_enrollers.verification.attemp', array((int)($vv_verification_request[0]['attempts_count'] ?? 0)));?></span>
    <div id="verification-code-input"
         class="d-flex flex-row mt-5 justify-content-center">
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

          print $this->Form->label( $req . '.verification_code.' . $i, "verification code part", array("class" => "visually-hidden"));
          print $this->Form->input( $req . '.verification_code.' . $i, $options);
        }
      ?>
    </div>
    <div id="verification-code-submit"
         class="text-center mt-5">
      <?php
      print $this->Form->submit($submit_label);
      print $this->Form->end();
      ?>
<!--      <span class="d-block mobile-text">Don't receive the code?</span>-->
<!--      <span class="font-weight-bold text-danger cursor">Resend</span>-->
    </div>
  </div>
</div>