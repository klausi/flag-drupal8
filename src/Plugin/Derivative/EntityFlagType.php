<?php
/**
 * @file
 * Contains \Drupal\flag\Plugin\Derivative\EntityFlagType.
 */

namespace Drupal\flag\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DerivativeBase;

/**
 * Derivative class for entity flag types plugin.
 */
class EntityFlagType extends DerivativeBase {
  /*
  public function __construct($base_plugin_id,
  EntityStorageControllerInterface $storageController) {
  }
  */

  /**
   * Ignored types to prevent duplicate occurrences.
   *
   * @var array
   */
  protected $ignoredEntities = array(
    'flag_flag',
    'flagging',
    'node',
    'user',
    'comment',
  );

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_def) {
    $derivatives = array();
    foreach (\Drupal::entityManager()->getDefinitions() as $entity_id => $entity_type) {
      if (in_array($entity_id, $this->ignoredEntities)) {
        continue;
      }
      $derivatives[$entity_id] = array(
        'title' => $entity_type->getLabel(),
        'entity_type' => $entity_id,
      ) + $base_plugin_def;
    }

    return $derivatives;
  }
} 