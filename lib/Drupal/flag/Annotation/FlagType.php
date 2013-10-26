<?php
/**
 * Created by PhpStorm.
 * User: tess
 * Date: 10/6/13
 * Time: 10:40 AM
 */

namespace Drupal\flag\Annotation;

use Drupal\Component\Annotation\Plugin;


/**
 * Defines a FlagType annotation object.
 *
 * @Annotation
 */
class FlagType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The title of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

}
