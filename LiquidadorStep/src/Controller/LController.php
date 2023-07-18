<?php
/**
 * @file
 * Contains \Drupal\LiquidadorStep\Controller\LController.
 */
namespace Drupal\LiquidadorStep\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Link;
use Drupal\Core\Url;
class LController {




  public function content()   {


    //$en = $entity->id();

    $vocabulary_name = 'Firmas_Factura';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);

    $output = '<ul>';
    foreach ($terms as $term) {
      $name = $term->getName();;
      $id = $term->getDescription();
      $id2 = $term->getFields();

      $value = $term->get('field_cargo')->getValue();
      $value2 = $term->get('field_nombre')->getValue();
      $value3 = $term->get('description')->getValue();

  //print '<pre>';
 //var_dump(array_keys($id2));
  // var_dump(array_keys($id2[0][0]));
  //var_dump(array_keys($id2[0]));
  //$en);
    //print '</pre>';



      $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->id()]);


      $link = Link::fromTextAndUrl($name, $url);
      $link = $link->toRenderable();
      $output .= '<li>'.$value[0]["value"].'<br/>'.$value2[0]["value"].'<br/>'. $value3[0]["value"].'</li>';
    }


    $output .= '</ul>';
    $output .= '<br/> <hr> test';
    return array(
      '#type' => 'markup',
      '#markup' => $output
    );
  }
}