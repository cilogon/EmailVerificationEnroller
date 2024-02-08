<?php
/**
 * COmanage Registry Email Verification Enrollers Controller
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
 * @package       registry
 * @since         COmanage Registry v4.4.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */

App::uses("SEWController", "Controller");

class EmailVerificationEnrollersController extends SEWController {
  // Class name, used by Cake
  public $name = "EmailVerificationEnrollers";

  // Establish pagination parameters for HTML views
  public $paginate = array(
    'limit' => 25,
    'order' => array(
      'co_enrollment_flow_wedge_id' => 'asc',
    ),
  );

  public $edit_contains = array(
    'CoEnrollmentFlowWedge' => array('CoEnrollmentFlow'),
  );

  public $view_contains = array(
    'CoEnrollmentFlowWedge' => array('CoEnrollmentFlow'),
  );

  /**
   * Callback before views are rendered.
   *
   * @since  COmanage Registry v4.4.0
   */

  public function beforeRender() {
    if ($this->request->is('restful')) {
      // It inherits from HttpException as a result it will return correctly in case of a RESTFULL request
      throw new BadRequestException();
    }

    // Provide a list of message templates
    $args                          = array();
    $args['conditions']['co_id']   = $this->cur_co['Co']['id'];
    $args['conditions']['status']  = SuspendableStatusEnum::Active;
    $args['conditions']['context'] = MessageTemplateEnum::EnrollmentVerification;
    $this->set(
      'vv_message_templates',
      $this->EmailVerificationEnroller->CoEnrollmentFlowVerMessageTemplate->find('list', $args)
    );

    $this->set('vv_default_charset', EmailVerificationEnroller::DEFAULT_CHARSET);

    parent::beforeRender();
  }

  /**
   * Authorization for this Controller, called by Auth component
   * - precondition: Session.Auth holds data used for authz decisions
   * - postcondition: $permissions set with calculated permissions
   *
   * @return Array Permissions
   * @since  COmanage Registry v4.4.0
   */

  function isAuthorized()
  {
    $roles = $this->Role->calculateCMRoles();

    // Construct the permission set for this user, which will also be passed to the view.
    $p = array();

    // Determine what operations this user can perform

    // Delete an existing Identifier Enroller?
    $p['delete'] = ($roles['cmadmin'] || $roles['coadmin']);

    // Edit an existing Identifier Enroller?
    $p['edit'] = ($roles['cmadmin'] || $roles['coadmin']);

    // View all existing Identifier Enroller?
    $p['index'] = ($roles['cmadmin'] || $roles['coadmin']);

    // View an existing Identifier Enroller?
    $p['view'] = ($roles['cmadmin'] || $roles['coadmin']);

    $this->set('permissions', $p);

    return ($p[$this->action]);
  }

  /**
   * Perform a redirect back to the controller's default view.
   * - postcondition: Redirect generated
   *
   * @since  COmanage Registry v4.4.0
   */

  public function performRedirect()
  {
    $target               = array();
    $target['plugin']     = 'email_verification_enroller';
    $target['controller'] = "email_verification_enrollers";
    $target['action']     = 'edit';
    $target[]             = $this->request->params["pass"][0];

    $this->redirect($target);
  }
}