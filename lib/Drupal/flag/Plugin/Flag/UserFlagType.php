<?php
/**
 * Created by PhpStorm.
 * User: tess
 * Date: 10/27/13
 * Time: 9:32 PM
 */

namespace Drupal\flag\Plugin\Flag;

use Drupal\flag\Plugin\Flag\FlagTypeBase;

/**
 * Class UserFlagType
 * @package Drupal\flag\Plugin\Flag
 *
 * @FlagType(
 *   id = "flagtype_user",
 *   title = @Translation("Flag Type User")
 * )
 */
class UserFlagType extends FlagTypeBase {

  public $access_uid;

  public $show_on_profile;

  public static function entityTypes() {
    return array(
      'user' => array(
        'title' => t('Users'),
        'description' => t('Users who have created accounts on your site.'),
      ),
    );
  }

  function options() {
    $options = parent::options();
    $options += array(
      'show_on_profile' => TRUE,
      'access_uid' => '',
    );
    return $options;
  }

  /**
   * Options form extras for user flags.
   */
  function options_form(&$form) {
    parent::options_form($form);
    $form['access']['types'] = array(
      // A user flag doesn't support node types.
      // TODO: Maybe support roles instead of node types.
      '#type' => 'value',
      '#value' => array(0 => 0),
    );
    $form['access']['access_uid'] = array(
      '#type' => 'checkbox',
      '#title' => t('Users may flag themselves'),
      '#description' => t('Disabling this option may be useful when setting up a "friend" flag, when a user flagging themself does not make sense.'),
      '#default_value' => $this->access_uid ? 0 : 1,
    );
    $form['display']['show_on_profile'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display link on user profile page'),
      '#description' => t('Show the link formatted as a user profile element.'),
      '#default_value' => $this->show_on_profile,
      // Put this above 'show on entity'.
      '#weight' => -1,
    );
  }

  function type_access_multiple($entity_ids, $account) {
    $access = array();

    // Exclude anonymous.
    if (array_key_exists(0, $entity_ids)) {
      $access[0] = FALSE;
    }

    // Prevent users from flagging themselves.
    if ($this->access_uid == 'others' && array_key_exists($account->uid, $entity_ids)) {
      $access[$account->uid] = FALSE;
    }

    return $access;
  }

} 