<?php

namespace Drupal\Liquidador2\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term; 
use Drupal\node\Entity\Node; 
use Drupal\file\Entity\File;
/**
 * Implements a codimth Simple Form API.
 */
class Liquidador2_Form extends FormBase
{

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
  $form['intro'] = [
      '#type' => 'markup',
      '#markup' => '<div class="cruzar_t_intro"><h2>Instrumento que permite comparar los registros de Asobancaria cargados al sistema para compararlos masivamente, al dar clic en cruzar tablas la herramienta comparará los registros y las liquidaciones, cambiando de estado las liquidaciones que coincidan.</h2> </div>',
    ];
    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cruzar Tablas'),
    ];

	  
	  
	    $form['alert'] = [
      '#type' => 'markup',
      '#markup' => '<div class="alert_cruzar"><h1>Esta acción no se puede revertir.!</h1><div/>',
    ];
	  
    $form['list'] = [
      '#type' => 'markup',
      '#markup' => '<hr><br/>',
    ];

    return $form;
  }

  /**
   * @return string
   */
  public function getFormId()
  {
    return 'Liquidador2_Form';
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {



  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
      $query = \Drupal::entityQuery('taxonomy_term');
	  $query->condition('vid','btpat574');
      $query->condition('field_comparado',false);//consultar solo los no comparados
      $entity_ids = $query->execute();
	  $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($entity_ids);//se carga el vector
	  if (isset($terms)){ 
		 
	
		foreach ($terms as $term) { 
		   $title_asobancaria = $term->getName();//obtener el titulo de la asobancaria
			$this->messenger()->addStatus($this->t('si existen datos en Asobancarias por Comparar '.  $term->getName()));
				     
		    $nids = \Drupal::entityQuery('node')
			->condition('type','liquidacion')
           ->condition('title', $title_asobancaria)
           ->execute();
			
			$nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
			
			  foreach ($nodes as $node) {
				  
				 $testok= $node->id();//obteniendo el id de la liquidacion a actualizar
				  $node->set('field_comparado_factura', TRUE)->save();
				  $term->set('field_comparado', TRUE)->save();
				
				  // set to comparado
				  
				  //create a log
				  
				  
				  Term::create([
 'name' => time(),
 'vid' => 'log_transaccional',
]);
				  
				
				  
				  
				  
			  }
			
		}  //end for each
		   

		   $this->messenger()->addStatus($this->t('si existen datos en Asobancarias por Comparar '. $testok));
	 }else{	
		    $this->messenger()->addStatus($this->t('no existen datos en Asobancarias por Comparar'));

	  }
	
	   
 }//end submint


}//end class