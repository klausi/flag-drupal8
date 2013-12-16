<?php
/**
 * Created by PhpStorm.
 * User: tess
 * Date: 11/5/13
 * Time: 10:01 PM
 */

namespace Drupal\flag;

use Drupal\Component\Plugin\PluginBase;
use Drupal\flag\ActionLinkTypePluginInterface;

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
   * @return string
   */
  public function buildLink() {
    return "";
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