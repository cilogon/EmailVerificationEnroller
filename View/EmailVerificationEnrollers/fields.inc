<!--
/**
 * COmanage Registry Email Verification Enrollers Plugin Fields
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
-->
<?php
// Determine if fields are editable
$e = false;

if(($this->action == "add" && $permissions['add']) || ($this->action == "edit" && $permissions['edit']))
  $e = true;

// We shouldn't get here if we don't have at least read permission, but check just in case

if(!$e && !$permissions['view'])
  return false;

// Add breadcrumbs
print $this->element("coCrumb");

$args = array();
$args['plugin'] = null;
$args['controller'] = 'co_enrollment_flows';
$args['action'] = 'index';
$args['co'] = $cur_co['Co']['id'];
$this->Html->addCrumb(_txt('ct.co_enrollment_flows.pl'), $args);

$args = array();
$args['plugin'] = null;
$args['controller'] = 'co_enrollment_flows';
$args['action'] = 'edit';
$args[] = $email_verification_enrollers[0]['CoEnrollmentFlowWedge']['co_enrollment_flow_id'];
$this->Html->addCrumb($email_verification_enrollers[0]['CoEnrollmentFlowWedge']['CoEnrollmentFlow']['name'], $args);

$args = array();
$args['plugin'] = null;
$args['controller'] = 'co_enrollment_flow_wedges';
$args['action'] = 'index';
$args['coef'] = $email_verification_enrollers[0]['CoEnrollmentFlowWedge']['co_enrollment_flow_id'];
$args[] = $email_verification_enrollers[0]['CoEnrollmentFlowWedge']['id'];
$this->Html->addCrumb(_txt('ct.co_enrollment_flow_wedges.pl'), $args);

$args = array();
$args['plugin'] = null;
$args['controller'] = 'co_enrollment_flow_wedges';
$args['action'] = 'edit';
$args[] = $email_verification_enrollers[0]['CoEnrollmentFlowWedge']['id'];
$this->Html->addCrumb($email_verification_enrollers[0]['CoEnrollmentFlowWedge']['description'], $args);

$crumbTxt = _txt('op.config');
$this->Html->addCrumb($crumbTxt);

if($this->action != 'view') {
  // Populate cross references
  print $this->Form->hidden('co_enrollment_flow_wedge_id', array('default' => $email_verification_enrollers[0]["EmailVerificationEnroller"]["co_enrollment_flow_wedge_id"])) . PHP_EOL;
}
?>

<script type="text/javascript">
  const seconds = <?php print (int)($vv_verification_request[0]['attempts_count'] ?? 0) * 1?>;

  $(document).ready(function() {
    // Allow only alphanumeric characters
    $('#EmailVerificationEnrollerVerificationCodeCharset').bind('keypress', function (event) {
      // Allow for regular characters and include these special few:
      // comma, period, explanation point, new line
      var regex = new RegExp("^[a-zA-Z0-9]+$");
      var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
      if (!regex.test(key)) {
        event.preventDefault();
        return false;
      }
    });
    // We only allow upper case
    $('#EmailVerificationEnrollerVerificationCodeCharset').on('keyup', function() {
      $(this).val (function () {
        let $value = this.value.toUpperCase();
        let $arrayValue = Array.from(new Set($value.split('')));
        return $arrayValue.join('');
      }).trigger('change');
    })
  });

</script>


<ul id="<?php print $this->action; ?>_email_verification_enrollers" class="fields form-list">
  <li>
    <div class="field-name">
      <div class="field-title"><?php print _txt('fd.ef.vmt'); ?></div>
      <div class="field-desc"><?php print _txt('fd.ef.vmt.desc'); ?></div>
    </div>
    <div class="field-info">
      <?php
      $attrs = array();
      $attrs['value'] = isset($email_verification_enrollers[0]['EmailVerificationEnroller']['verification_template_id'])
                        ? $email_verification_enrollers[0]['EmailVerificationEnroller']['verification_template_id']
                        : "";
      $attrs['empty'] = false;

      if($e) {
        print $this->Form->select('verification_template_id',
                                  $vv_message_templates,
                                  $attrs);

        if($this->Form->isFieldError('type')) {
          print $this->Form->error('type');
        }
      } else {
        print _txt('fd.ef.vmt', null, $vv_message_templates[$email_verification_enrollers[0]['EmailVerificationEnroller']['verification_template_id']]);
      }
      ?>
    </div>
  </li>
  <li>
    <div class="field-name">
      <div class="field-title">
        <?php print ($e ? $this->Form->label('verification_code_charset', _txt('fd.email_verification_enrollers.code.charset')) : _txt('fd.email_verification_enrollers.code.charset')); ?>
      </div>
      <div class="field-desc"><?php print _txt('fd.email_verification_enrollers.code.charset.desc'); ?></div>
    </div>
    <div class="field-info">
      <?php
      $verificationCodeCharset = filter_var($email_verification_enrollers[0]['EmailVerificationEnroller']['verification_code_charset'],FILTER_SANITIZE_SPECIAL_CHARS);
      if($e) {

        print $this->Form->input('verification_code_charset', array(
          'maxlength' => 36,
          'size' => 36,
          'placeholder' => $vv_default_charset,
          'value' => $verificationCodeCharset
        ));
      } else {
        print filter_var($email_verification_enrollers[0]['EmailVerificationEnroller']['verification_code_charset'],FILTER_SANITIZE_SPECIAL_CHARS);
      }
      ?>
    </div>
  </li>
  <li>
    <div class="field-name">
      <div class="field-title">
        <?php print ($e ? $this->Form->label('verification_code_length', _txt('fd.email_verification_enrollers.code.len')) : _txt('fd.email_verification_enrollers.code.len')); ?>
      </div>
      <div class="field-desc"><?php print _txt('fd.email_verification_enrollers.code.len.desc'); ?></div>
    </div>
    <div class="field-info">
      <?php print ($e
        ? $this->Form->input('verification_code_length', array(
          'type' => 'number',
          'min' => 4,
          'step' => 4,
          'max' => 20
        ))
        : filter_var($email_verification_enrollers[0]['EmailVerificationEnroller']['verification_code_length'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
    </div>
  </li>
  <li>
    <div class="field-name">
      <div class="field-title">
        <?php print ($e ? $this->Form->label('verification_validity', _txt('fd.email_verification_enrollers.validity')) : _txt('fd.email_verification_enrollers.validity')); ?>
      </div>
      <div class="field-desc"><?php print _txt('fd.email_verification_enrollers.validity.desc'); ?></div>
    </div>
    <div class="field-info">
      <?php print ($e
        ? $this->Form->input('verification_validity', array('type' => 'number'))
        : filter_var($email_verification_enrollers[0]['EmailVerificationEnroller']['verification_validity'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
    </div>
  </li>
  <?php if($e): ?>
    <li class="fields-submit">
      <div class="field-name">
        <span class="required"><?php print _txt('fd.req'); ?></span>
      </div>
      <div class="field-info">
        <?php print $this->Form->submit($submit_label); ?>
      </div>
    </li>
  <?php endif; ?>
</ul>

<?php
print $this->element("changelog");

