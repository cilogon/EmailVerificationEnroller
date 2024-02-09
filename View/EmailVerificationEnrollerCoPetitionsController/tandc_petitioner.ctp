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
  const seconds = <?php print (int)($vv_verification_request[0]['attempts_count'] ?? 0)?>;

  $(document).ready(function() {
    $('#EmailVerificationEnrollerCoPetitionsControllerVerificationCode').bind('keypress', function (event) {
      // Allow for regular characters and include these special few:
      // comma, period, explanation point, new line
      var regex = new RegExp("^[a-zA-Z0-9\-]+$");
      var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
      if (!regex.test(key)) {
        event.preventDefault();
        return false;
      }
    });

    $('#EmailVerificationEnrollerCoPetitionsControllerVerificationCode').on('keyup', function() {
      $(this).val (function () {
        return this.value.toUpperCase();
      }).trigger('change');
    })

    if(seconds > 0.5) {
      appendTimerToDocument(seconds)
      startTimer(seconds, seconds)
    }
    setTimeout(() => {
      // i should hide the boxes here and present the spinner by default
      $("#app-timer").hide()
      $("#verification-code-card").show();
      $("#EmailVerificationEnrollerCoPetitionsControllerVerificationCode").focus();
      $("#EmailVerificationEnrollerCoPetitionsControllerVerificationCode").val('').trigger('change');
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
       class="card py-5 px-3 mt-5 mt-sm-0"
       style="display: none">
    <h1 class="m-0 mb-2 text-center"><?php print _txt('pl.verification_request.verify.title');?></h1>
    <p>
      <span class="mobile-text"><?php print _txt('pl.verification_request.verify.mail', array($vv_to_email["mail"]));?></span>
    </p>
    <p>
      <span class="mobile-text"><?php print _txt('pl.verification_request.verify.info');?></span>
    </p>
    <div id="verification-code-input"
         class="d-flex flex-row mt-5 justify-content-center">
      <?php
        print $this->Form->label('verification_code', _txt('pl.verification_request.verify.label'), array(
          'class' => 'my-auto mr-2 mobile-text'
        ));
        print $this->Form->input('verification_code', array('type' => 'text'));
      ?>
    </div>
    <div id="verification-code-submit"
         class="text-center mt-5">
      <?php
      print $this->Form->submit($submit_label);
      print $this->Form->end();
      ?>

    </div>
    <?php if((int)($vv_verification_request[0]['attempts_count']) > 1): ?>
      <span class="font-weight-bold text-danger cursor mt-2"><?php print _txt('fd.email_verification_enrollers.verification.failed')?></span>
    <?php endif; ?>
  </div>
</div>