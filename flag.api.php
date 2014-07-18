<?php

/**
 * @file
 * Hooks provided by the Flag module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter flag type definitions provided by other modules.
 *
 * This hook may be placed in a $module.flag.inc file.
 *
 * @param $definitions
 *  An array of flag definitions returned by hook_flag_type_info().
 */
function hook_flag_type_info_alter(&$definitions) {

}

/**
 * Allow modules to alter a flag when it is initially loaded.
 *
 * @see flag_get_flags().
 */
function hook_flag_alter(&$flag) {

}

/**
 * Alter a flag's default options.
 *
 * Modules that wish to extend flags and provide additional options must declare
 * them here so that their additions to the flag admin form are saved into the
 * flag object.
 *
 * @param $options
 *  The array of default options for the flag type, with the options for the
 *  flag's link type merged in.
 * @param $flag
 *  The flag object.
 *
 * @see flag_flag::options()
 */
function hook_flag_options_alter(&$options, $flag) {

}

/**
 * Perform custom validation on a flag before flagging/unflagging.
 *
 * @param $action
 *  The action about to be carried out. Either 'flag' or 'unflag'.
 * @param $flag
 *  The flag object.
 * @param $entity_id
 *  The id of the entity the user is trying to flag or unflag.
 * @param $account
 *  The user account performing the action.
 * @param $flagging
 *  The flagging entity.
 *
 * @return
 *   Optional array: textual error with the error-name as the key.
 *   If the error name is 'access-denied' and javascript is disabled,
 *   drupal_access_denied will be called and a 403 will be returned.
 *   If validation is successful, do not return a value.
 */
function hook_flag_validate($action, $flag, $entity_id, $account, $skip_permission_check, $flagging) {
  // We're only operating on the "test" flag, and users may always unflag.
  if ($flag->name == 'test' && $action == 'flag') {
    // Get all flags set by the current user.
    $flags = flag_get_user_flags('node', NULL, $account->uid, $sid = NULL, $reset = FALSE);
    // Check if this user has any flags of this type set.
    if (isset($flags['test'])) {
      $count = count($flags[$flag->name]);
      if ($count >= 2) {
        // Users may flag only 2 nodes with this flag.
        return(array('access-denied' => t('You may only flag 2 nodes with the test flag.')));
      }
    }
  }
}

/**
 * Allow modules to allow or deny access to flagging for a single entity.
 *
 * Called when displaying a single entity view or edit page.  For flag access
 * checks from within Views, implement hook_flag_access_multiple().
 *
 * @param $flag
 *  The flag object.
 * @param $entity_id
 *  The id of the entity in question.
 * @param $action
 *  The action to test. Either 'flag' or 'unflag'.
 * @param $account
 *  The user on whose behalf to test the flagging action.
 *
 * @return
 *   One of the following values:
 *     - TRUE: User has access to the flag.
 *     - FALSE: User does not have access to the flag.
 *     - NULL: This module does not perform checks on this flag/action.
 *
 *   NOTE: Any module that returns FALSE will prevent the user from
 *   being able to use the flag.
 *
 * @see hook_flag_access_multiple()
 * @see flag_flag:access()
 */
function hook_flag_access($flag, $entity_id, $action, $account) {

}

/**
 * Allow modules to allow or deny access to flagging for multiple entities.
 *
 * Called when preparing a View or list of multiple flaggable entities.
 * For flag access checks for individual entities, see hook_flag_access().
 *
 * @param $flag
 *  The flag object.
 * @param $entity_ids
 *  An array of object ids to check access.
 * @param $account
 *  The user on whose behalf to test the flagging action.
 *
 * @return
 *   An array whose keys are the object IDs and values are booleans indicating
 *   access.
 *
 * @see hook_flag_access()
 * @see flag_flag:access_multiple()
 */
function hook_flag_access_multiple($flag, $entity_ids, $account) {

}

/**
 * Act when a flag is reset.
 *
 * @param $flag
 *  The flag object.
 * @param $entity_id
 *  The entity ID on which all flaggings are to be removed. May be NULL, in
 *  which case all of this flag's entities are to be unflagged.
 * @param $rows
 *  Database rows from the {flagging} table.
 *
 * @see flag_reset_flag()
 */
function hook_flag_reset($flag, $entity_id, $rows) {

}

/**
 * Alter the javascript structure that describes the flag operation.
 *
 * @param $flag
 *   The full flag object.
 * @param $entity_id
 *   The ID of the node, comment, user or other object being flagged.
 *
 * @see flag_build_javascript_info()
 */
function hook_flag_javascript_info_alter() {

}
