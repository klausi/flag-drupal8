<?php
/**
 * Created by PhpStorm.
 * User: tess
 * Date: 1/3/14
 * Time: 9:24 PM
 */

namespace Drupal\flag\Form;

use Drupal\Core\Entity\EntityFormController;
use Drupal\flag\Handlers\AbstractFlag;

abstract class FlagFormBase extends EntityFormController {

  protected function getRoleOptions() {
    $role_options = array();

    foreach (user_roles() as $rid => $role_info) {
      $role_options[$rid] = $role_info->label;
    }

    return $role_options;
  }

  public function buildForm(array $form, array &$form_state, $entity_type = NULL) {
    $form = parent::buildForm($form, $form_state);

    $flag = $this->entity;

    $type_info = flag_fetch_definition($entity_type);

    $form['#flag'] = $flag;
    $form['#flag_name'] = $flag->id;

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#default_value' => $flag->label,
      '#description' => t('A short, descriptive title for this flag. It will be used in administrative interfaces to refer to this flag, and in page titles and menu items of some <a href="@insite-views-url">views</a> this module provides (theses are customizable, though). Some examples could be <em>Bookmarks</em>, <em>Favorites</em>, or <em>Offensive</em>.', array('@insite-views-url' => url('admin/structure/views'))),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#weight' => -3,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#title' => t('Machine name'),
      '#default_value' => $flag->id,
      '#description' => t('The machine-name for this flag. It may be up to 32 characters long and may only contain lowercase letters, underscores, and numbers. It will be used in URLs and in all API calls.'),
      '#weight' => -2,
      '#machine_name' => array(
        'exists' => 'flag_load_by_id',
      ),
      '#disabled' => !$flag->isNew(),
    );

    $form['is_global'] = array(
      '#type' => 'checkbox',
      '#title' => t('Global flag'),
      '#default_value' => $flag->isGlobal(),
      '#description' => t('If checked, flag is considered "global" and each entity is either flagged or not. If unchecked, each user has individual flags on entities.'),
      '#weight' => -1,
    );

    $form['messages'] = array(
      '#type' => 'fieldset',
      '#title' => t('Messages'),
    );

    $form['messages']['flag_short'] = array(
      '#type' => 'textfield',
      '#title' => t('Flag link text'),
      '#default_value' => !empty($flag->flag_short) ? $flag->flag_short : t('Flag this item'),
      '#description' => t('The text for the "flag this" link for this flag.'),
      '#required' => TRUE,
    );

    $form['messages']['flag_long'] = array(
      '#type' => 'textfield',
      '#title' => t('Flag link description'),
      '#default_value' => $flag->flag_long,
      '#description' => t('The description of the "flag this" link. Usually displayed on mouseover.'),
    );

    $form['messages']['flag_message'] = array(
      '#type' => 'textfield',
      '#title' => t('Flagged message'),
      '#default_value' => $flag->flag_message,
      '#description' => t('Message displayed after flagging content. If JavaScript is enabled, it will be displayed below the link. If not, it will be displayed in the message area.'),
    );

    $form['messages']['unflag_short'] = array(
      '#type' => 'textfield',
      '#title' => t('Unflag link text'),
      '#default_value' => !empty($flag->unflag_short) ? $flag->unflag_short : t('Unflag this item'),
      '#description' => t('The text for the "unflag this" link for this flag.'),
      '#required' => TRUE,
    );

    $form['messages']['unflag_long'] = array(
      '#type' => 'textfield',
      '#title' => t('Unflag link description'),
      '#default_value' => $flag->unflag_long,
      '#description' => t('The description of the "unflag this" link. Usually displayed on mouseover.'),
    );

    $form['messages']['unflag_message'] = array(
      '#type' => 'textfield',
      '#title' => t('Unflagged message'),
      '#default_value' => $flag->unflag_message,
      '#description' => t('Message displayed after content has been unflagged. If JavaScript is enabled, it will be displayed below the link. If not, it will be displayed in the message area.'),
    );

    $form['access'] = array(
      '#type' => 'fieldset',
      '#title' => t('Flag access'),
      '#tree' => FALSE,
      '#weight' => 10,
    );

    $flag_type_plugin = $flag->getFlagTypePlugin();
    $flag_type_def = $flag_type_plugin->getPluginDefinition();

    $bundles = entity_get_bundles($flag_type_def['entity_type']);
    $entity_bundles = array();
    foreach ($bundles as $bundle_id => $bundle_row) {
      $entity_bundles[$bundle_id] = $bundle_row['label'];
    }

    // Flag classes will want to override this form element.
    $form['access']['types'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Flaggable types'),
      '#options' => $entity_bundles,
      '#default_value' => $flag->types,
      '#description' => t('Check any sub-types that this flag may be used on.'),
      '#required' => TRUE,
      '#weight' => 10,
    );

    $form['access']['roles'] = array(
      '#title' => t('Roles that may use this flag'),
      '#description' => t('Users may only unflag content if they have access to flag the content initially. Checking <em>authenticated user</em> will allow access for all logged-in users.'),
      '#theme' => 'flag_form_roles',
      '#theme_wrappers' => array('form_element'),
      '#weight' => -2,
      '#attached' => array(
        'js' => array(drupal_get_path('module', 'flag') . '/theme/flag-admin.js'),
        'css' => array(drupal_get_path('module', 'flag') . '/theme/flag-admin.css'),
      ),
    );

    $flag_permissions = $flag->getPermissions();

    $form['access']['roles']['flag'] = array(
      '#type' => 'checkboxes',
      '#title' => 'Roles that may flag',
      '#options' => $this->getRoleOptions(),
      '#default_value' => $flag_permissions['flag'],
      '#parents' => array('roles', 'flag'),
    );
    $form['access']['roles']['unflag'] = array(
      '#type' => 'checkboxes',
      '#title' => 'Roles that may unflag',
      '#options' => $this->getRoleOptions(),
      '#default_value' => $flag_permissions['unflag'],
      '#parents' => array('roles', 'unflag'),
    );

    $form['access']['unflag_denied_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Unflag not allowed text'),
      '#default_value' => $flag->unflag_denied_text,
      '#description' => t('If a user is allowed to flag but not unflag, this text will be displayed after flagging. Often this is the past-tense of the link text, such as "flagged".'),
      '#weight' => -1,
    );

    $form['display'] = array(
      '#type' => 'fieldset',
      '#title' => t('Display options'),
      '#description' => t('Flags are usually controlled through links that allow users to toggle their behavior. You can choose how users interact with flags by changing options here. It is legitimate to have none of the following checkboxes ticked, if, for some reason, you wish <a href="@placement-url">to place the the links on the page yourself</a>.', array('@placement-url' => 'http://drupal.org/node/295383')),
      '#tree' => FALSE,
      '#weight' => 20,
      // @todo: Move flag_link_type_options_states() into controller?
//      '#after_build' => array('flag_link_type_options_states'),
    );

    $form = $flag_type_plugin->buildConfigurationForm($form, $form_state);

    $form['display']['link_type'] = array(
      '#type' => 'radios',
      '#title' => t('Link type'),
      '#options' => \Drupal::service('plugin.manager.flag.linktype')->getAllLinkTypes(),
//      '#after_build' => array('flag_check_link_types'),
      '#default_value' => $flag->getLinkTypePlugin()->getPluginId(),
      // Give this a high weight so additions by the flag classes for entity-
      // specific options go above.
      '#weight' => 18,
      '#attached' => array(
        'js' => array(drupal_get_path('module', 'flag') . '/theme/flag-admin.js'),
      ),
      '#attributes' => array(
        'class' => array('flag-link-options'),
      ),
    );
    // Add the descriptions to each ratio button element. These attach to the
    // elements when FormAPI expands them.
    $action_link_plugin_defs = \Drupal::service('plugin.manager.flag.linktype')->getDefinitions();
    foreach ($action_link_plugin_defs as $key => $info) {
      $form['display']['link_type'][$key]['#description'] = $info['description'];
    }

    $action_link_plugin = $flag->getLinkTypePlugin();
    $form = $action_link_plugin->buildConfigurationForm($form, $form_state);

    return $form;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::validate().
   */
  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);

    $form_state['values']['label'] = trim($form_state['values']['label']);
    $form_values = $form_state['values'];

    if ($form_values['link_type'] == 'confirm') {
      if (empty($form_values['flag_confirmation'])) {
        form_set_error('flag_confirmation', t('A flag confirmation message is required when using the confirmation link type.'));
      }
      if (empty($form_values['unflag_confirmation'])) {
        form_set_error('unflag_confirmation', t('An unflag confirmation message is required when using the confirmation link type.'));
      }
    }
    /*
        if (!preg_match('/^[a-z_][a-z0-9_]*$/', $form_values['id'])) {
          form_set_error('label', t('The flag name may only contain lowercase letters, underscores, and numbers.'));
        }
    */
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   */
  public function save(array $form, array &$form_state) {
    $flag = $this->entity;

    $flag->getFlagTypePlugin()->submitConfigurationForm($form, $form_state);
    $flag->getLinkTypePlugin()->submitConfigurationForm($form, $form_state);

    $flag->enable();
    $status = $flag->save();
    $uri = $flag->uri();
    if ($status == SAVED_UPDATED) {
      drupal_set_message(t('Flag %label has been updated.', array('%label' => $flag->label())));
      watchdog('flag', 'Flag %label has been updated.', array('%label' => $flag->label()), WATCHDOG_NOTICE, l(t('Edit'), $uri['path'] . '/edit'));
    }
    else {
      drupal_set_message(t('Flag %label has been added.', array('%label' => $flag->label())));
      watchdog('flag', 'Flag %label has been added.', array('%label' => $flag->label()), WATCHDOG_NOTICE, l(t('Edit'), $uri['path'] . '/edit'));
    }

    // We clear caches more vigorously if the flag was new.
//    _flag_clear_cache($flag->entity_type, !empty($flag->is_new));

    // Save permissions.
    // This needs to be done after the flag cache has been cleared, so that
    // the new permissions are picked up by hook_permission().
    // This may need to move to the flag class when we implement extra permissions
    // for different flag types: http://drupal.org/node/879988

    // If the flag machine name as changed, clean up all the obsolete permissions.
    if ($flag->id != $form['#flag_name']) {
      $old_name = $form['#flag_name'];
      $permissions = array("flag $old_name", "unflag $old_name");
      foreach (array_keys(user_roles()) as $rid) {
        user_role_revoke_permissions($rid, $permissions);
      }
    }
    /*
        foreach (array_keys(user_roles(!module_exists('session_api'))) as $rid) {
          // Create an array of permissions, based on the checkboxes element name.
          $permissions = array(
            "flag $flag->name" => $flag->roles['flag'][$rid],
            "unflag $flag->name" => $flag->roles['unflag'][$rid],
          );
          user_role_change_permissions($rid, $permissions);
        }
    */
    // @todo: when we add database caching for flags we'll have to clear the
    // cache again here.

    $form_state['redirect'] = 'admin/structure/flags';
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::delete().
   */
  public function delete(array $form, array &$form_state) {
    $form_state['redirect'] = 'admin/structure/flags';
  }

} 