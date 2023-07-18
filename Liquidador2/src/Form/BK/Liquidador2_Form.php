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
 
   // $form['Name_Asobancaria'] = [
   //   '#type' => 'textfield',
   //   '#title' => 'Ingresar Nombre del Archivo Asobancaria',
   //   '#required' => TRUE,
  //  ];


    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cruzar Tablas'),
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
  {//ini submit
	  
  
	   $vid = 'asobancaria_test';
      $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

	  if (empty($terms)){ $this->messenger()->addStatus($this->t('No existen datos en asobancaria por Comparar'));   }
      foreach ($terms as $term) { $term_data[] = array('id' => $term->tid, 'name' => $term->name,'field_modalidad' => $term->field_modalidad,  );  } // end foreach term data 
						  
	  $node_array = array();
      $query = \Drupal::entityQuery('node');
      $query->condition('field_estado',false);//consultar solo los nodos liquidados
      //$query->condition('field_pendiente',false);//consultar solo los nodos   que no tienen error
      $query->condition('type', 'factura');
      $entity_ids = $query->execute();
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($entity_ids);

      if ( empty($nodes)){ $this->messenger()->addStatus($this->t('No existen datos en liquidaciones por Comparar'));  }
	  foreach ($nodes as $node) { $title = $node->getTitle();$node_array[] = array( 'node_title' => $title );   }
	  
	  //validado si exiten liquidaciones o asobancarias
	  //crear ciclo for con condiciones
	  
	 // $result=$term_data[$i]['name'];
	 // $result2=$node_array[$i]['node_title'];
	 for ($i =1; $i <= count($term_data); $i++){



 $x=$term_data[$i]['name'];
 $tid=$tid. $term_data[$i]['id'];
		  $field_modalidad =$term_data[$i]['field_modalidad'];
	$rest = substr( $x, 2, -4);
				if($rest == null) {	 $rest = "0000";}
  $nodex = \Drupal\node\Entity\Node::load($rest); //Accedemos a la información del nodo

   if (!empty($nodex) ) {
	$title="";
		$title=$term_data[$i]['name'];
	   
   $nodex->set(field_estado,true);//cambiar estado a pagado
  $nodex->set(field_fecha_pago, "hoy"); //Accedemos al campo que queremos editar/modificar
  $nodex->set(field_modalidad, "PSE"); //Accedemos al campo que queremos editar/modificar
  $nodex->save(); //Guarda el cambio una vez realizado
	// crear un tipo de contenido registro ok
	   $node1 = Node::create(['type' => 'registro_asobancaria']);
       $node1->set('title',$title );
	$node1->set('field_estado_registro',true);
	      $node2->set('field_modalidad',$field_modalidad);
	$node1->save(); //Guarda el cambio una vez realizado
	   if($tid !== 540){
	if ($term = \Drupal\taxonomy\Entity\Term::load($tid)) {
  // Delete the term itself
	
  $term->delete();}
		  } 
		   //end delete}

  }	else {
	// crear un tipo de contenido factura con error
	$title ="error:". $tid;
	//$val  = $term_data[$i]['id'];
	   $node2 = Node::create(['type' => 'registro_asobancaria']);
       $node2->set('title',$title );
	$node2->set('field_estado_registro',false);
	   $node2->set('field_modalidad',$field_modalidad);
	$node2->set('field_situacion',"Error Código Liquidación".$x);
	$node2->save(); //Guarda el cambio una vez realizado
	//evaluar si el valor recibido, concepto ambiental ok para regstrar como exitoso o no exitoso
		if($tid != 540){ 
	   if ($term = \Drupal\taxonomy\Entity\Term::load($tid)) {
  // Delete the term itself//
			//validar si es oo no borrar
			$term->delete();//end delete}
	    }










		}//end else
       }//end iff
	 }//end for  
	  //$this->messenger()->addStatus($this->t('Registros Actualizados.'));
 }//end submint
}//end class