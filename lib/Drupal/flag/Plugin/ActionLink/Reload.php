<?php
/**
 * Created by PhpStorm.
 * User: tess
 * Date: 11/14/13
 * Time: 8:21 PM
 */

use Drupal\flag\ActionLinkTypeBase;

/**
 * Class Reload
 *
 * @ActionLinkType(
 *   id = "reload",
 *   label = @Translation("Normal link"),
 *   description = "A normal non-JavaScript request will be made and the current page will be reloaded."
 * )
 */
class Reload extends ActionLinkTypeBase {

  public function buildLink() {
    return "/flag/";
  }

} 