<?php
/**
 * Created by PhpStorm.
 * User: tess
 * Date: 11/5/13
 * Time: 10:01 PM
 */

namespace Drupal\flag;

use Drupal\Core\URL;
use Drupal\Core\Entity\EntityInterface;
use \Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\flag\FlagInterface;
use Drupal\flag\ActionLinkTypePluginInterface;
use Drupal\flag\FlagService;

/**
 * Class ActionLinkTypeBase
 * @package Drupal\flag
 */
abstract class ActionLinkTypeBase extends PluginBase implements ActionLinkTypePluginInterface {

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param array $plugin_definition
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->setConfiguration($configuration);
  }

  /**
   * Returns a route name given an $action.
   *
   * @param $action
   *  A string containing the action name.
   * @return string
   *  A string containing a route name.
   */
  abstract public function routeName($action = NULL);

  /**
   * @return string
   */
  public function buildLink($action, FlagInterface $flag, EntityInterface $entity) {
    $parameters = array(
      'flag_id' => $flag->id(),
      'entity_id' => $entity->id(),
    );

    return new URL($this->routeName($action), $parameters);
  }

  public function renderLink($action, FlagInterface $flag, EntityInterface $entity) {
    $url = $this->buildLink($action, $flag, $entity);

    $url->setRouteParameter('destination', \Drupal::request()->getRequestUri());

    $render = $url->toRenderArray();
    $render['#type'] = 'link';
    $render['#attributes']['id'] = 'flag-' . $flag->id() . '-id-' . $entity->id();

    //@todo check if flagged, assign flag or unflag text.
    if ($action === 'unflag') {
      $render['#title'] = $flag->unflag_short;
      $render['#alt'] = $flag->unflag_long;
    }
    else {
      $render['#title'] = $flag->flag_short;
      $render['#alt'] = $flag->flag_long;
    }

    return $render;
  }

  /**
   * Provides a form array for the action link plugin's settings form.
   * Derived classes will want to override this method.
   *
   * @param array $form
   * @param array $form_state
   * @return array
   *   The configuration form array.
   */
  public function buildConfigurationForm(array $form, array &$form_state) {
    return $form;
  }

  /**
   * Processes the action link setting form submit. Derived classes will want to
   * override this method.
   *
   * @param array $form
   * @param array $form_state
   */
  public function submitConfigurationForm(array &$form, array &$form_state) {
    // Override this.
  }

  /**
   * Validates the action link setting form. Derived classes will want to override
   * this method.
   *
   * @param array $form
   * @param array $form_state
   */
  public function validateConfigurationForm(array &$form, array &$form_state) {
    // Override this.
  }

  /**
   * Provides the action link plugin's default configuration. Derived classes
   * will want to override this method.
   *
   * @return array
   *   The plugin configuration array.
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * Provides the action link plugin's current configuraiton array.
   *
   * @return array
   *   An array containing the plugin's currnt configuration.
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * Replaces the plugin's current configuration with that given in the parameter.
   * @param array $configuration
   *   An array containing the plugin's configuration.
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
  }

} 