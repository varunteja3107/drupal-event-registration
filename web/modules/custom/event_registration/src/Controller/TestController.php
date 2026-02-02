<?php

namespace Drupal\event_registration\Controller;

use Drupal\Core\Controller\ControllerBase;

class TestController extends ControllerBase {

  public function test() {
    return [
      '#markup' => '<h1>EVENT REGISTRATION MODULE WORKS</h1>',
    ];
  }

}

