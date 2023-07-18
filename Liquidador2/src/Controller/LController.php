<?php
/**
 * @file
 * Contains \Drupal\Liquidador1\Controller\LController.
 */
namespace Drupal\Liquidador2\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Link;
use Drupal\Core\Url;
class LController {




  public function content()   {


    $output .= '</ul>';
    $output .= '<br/> <hr> test';
    return array(
      '#type' => 'markup',
      '#markup' => $output
    );
  }
}