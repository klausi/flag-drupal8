<?php
/**
 * Created by PhpStorm.
 * User: tess
 * Date: 10/27/13
 * Time: 9:03 PM
 */

namespace Drupal\flag\Plugin\Flag;

use Drupal\flag\Plugin\Flag\FlagTypeBase;

/**
 * Class EntityFlagType
 * @package Drupal\flag\Plugin\Flag
 *
 * Base entity flag handler.
 *
 * @FlagType(
 *   id = "flagtype_entity",
 *   title = @Translation("Flag Type Entity")
 * )
 */
class EntityFlagType extends FlagTypeBase {

  public $types = array();

  public $show_in_links = array();

  public $show_as_field;

  public $show_on_form;

  public $show_contextual_link;

  public static function entityTypes() {
    $entity_types = array();
    foreach (entity_get_info() as $entity_id => $entity_info) {
      $entity_types[$entity_id] = array(
        'title' => $entity_info['label'],
        'description' => t('@entity-type entity', array('@entity-type' => $entity_info['label'])),
      );
    }

    return $entity_types;
  }

  function options() {
    $options = parent::options();
    $options += array(
      // Output the flag in the entity links.
      // This is empty for now and will get overriden for different
      // entities.
      // @see hook_entity_view().
      'show_in_links' => array(),
      // Output the flag as individual pseudofields.
      'show_as_field' => FALSE,
      // Add a checkbox for the flag in the entity form.
      // @see hook_field_attach_form().
      'show_on_form' => FALSE,
      'access_author' => '',
      'show_contextual_link' => FALSE,
    );
    return $options;
  }

  /**
   * Options form extras for the generic entity flag.
   */
  function options_form(&$form) {
    $bundles = array();
    $bundle_info =  entity_get_bundles($this->entity_type);
    foreach ($bundle_info as $bundle_key => $info) {
      $bundles[$bundle_key] = $info['label'];
    }
    $form['access']['types'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Bundles'),
      '#options' => $bundles,
      '#description' => t('Select the bundles that this flag may be used on. Leave blank to allow on all bundles for the entity type.'),
      '#default_value' => $this->types,
    );

    // Add checkboxes to show flag link on each entity view mode.
    $options = array();
    $defaults = array();
    $view_modes = entity_get_view_modes($this->entity_type);
    foreach ($view_modes as $name => $view_mode) {
      $options[$name] = t('Display on @name view mode', array('@name' => $view_mode['label']));
      $defaults[$name] = !empty($this->show_in_links[$name]) ? $name : 0;
    }

    $form['display']['show_in_links'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Display in entity links'),
      '#description' => t('Show the flag link with the other links on the entity.'),
      '#options' => $options,
      '#default_value' => $defaults,
    );

    $form['display']['show_as_field'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display link as field'),
      '#description' => t('Show the flag link as a pseudofield, which can be ordered among other entity elements in the "Manage display" settings for the entity type.'),
      '#default_value' => isset($this->show_as_field) ? $this->show_as_field : TRUE,
    );
    if (empty($entity_info['fieldable'])) {
      $form['display']['show_as_field']['#disabled'] = TRUE;
      $form['display']['show_as_field']['#description'] = t("This entity type is not fieldable.");
    }

    $form['display']['show_on_form'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display checkbox on entity edit form'),
      '#default_value' => $this->show_on_form,
      '#weight' => 5,
    );

    // We use FieldAPI to put the flag checkbox on the entity form, so therefore
    // require the entity to be fielable. Since this is a potential DX
    // headscratcher for a developer wondering where this option has gone,
    // we disable it and explain why.
    if (empty($entity_info['fieldable'])) {
      $form['display']['show_on_form']['#disabled'] = TRUE;
      $form['display']['show_on_form']['#description'] = t('This is only possible on entities which are fieldable.');
    }
    $form['display']['show_contextual_link'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display in contextual links'),
      '#default_value' => $this->show_contextual_link,
      '#description' => t('Note that not all entity types support contextual links.'),
      '#access' => module_exists('contextual'),
      '#weight' => 10,
    );
  }

} 