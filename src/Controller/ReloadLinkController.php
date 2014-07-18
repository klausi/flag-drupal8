<?php
/**
 * @file
 * Contains Drupal\flag\Controller\ReloadLinkController.
 */

namespace Drupal\flag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\flag\FlagInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ReloadLinkController extends ControllerBase {

  public function flag(Request $request, $flag_id, $entity_id) {

    $flagging = \Drupal::service('flag')->flag($flag_id, $entity_id);

    // Get the destination.
    $destination = $request->get('destination', $flagging->getFlaggable()->url());

    //@todo SECURITY HOLE. Please fix!
    return new RedirectResponse($destination);
  }

  public function unflag(Request $request, $flag_id, $entity_id) {
    $flagService = \Drupal::service('flag');
    $flagService->unflag($flag_id, $entity_id);

    $flag = $flagService->getFlagById($flag_id);
    $entity = $flagService->getFlaggableById($flag, $entity_id);

    $destination = \Drupal::request()->get('destination', $entity->url());

    //@todo SECURITY HOLE. Please fix!
    return new RedirectResponse($destination);
  }

} 