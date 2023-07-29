<?php

namespace Drupal\custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;

class CustomController extends ControllerBase {
	/**
   * Returns a List of url.
   *
   * @return array
   *   A simple renderable array.
   */

	public function list_url() {
    $baseUrl = Url::fromRoute('<front>', array(), array('absolute' => TRUE))->toString();
		$output ='';
    $query = \Drupal::database()->select('custom_url_data', 't');
    $query->fields('t', ['id','source','short_url','visit_url']);
    $query->distinct();
    $result = $query->execute()->fetchAll();
		$output.='<table>
                <tr><th>ID</th>
                    <th>Source</th>
                    <th>Short Url</th>
                    <th>Number of Visit on url</th>
                </tr>';
    foreach ($result as $row) {
        $output.='<tr>
                    <td>'.$row->id.'</td>
                    <td>'.$row->source.'</td>
                    <td><a href="'.$baseUrl.'view-details/'.$row->short_url.'">'.$row->short_url.'</a></td>
                    <td>'.$row->visit_url.'</td>
                  </tr>';
        
    }
    $output.='</table>';
  
		$element = array(
   		'#markup' => $output,
   		'#cache' => array(
   			'max-age' => 0,
   		),
   		'#allowed_tags' => ['table','th','tr','td','<','>','a'],
   	);
   	return $element;
	}


  public function detail_page($arg) {
    $output ='';
    $query = \Drupal::database()->select('custom_url_data', 't')->condition('short_url', $arg)->fields('t', ['visit_url'])->execute()->fetchAll();
    \Drupal::database()->update('custom_url_data')->fields(array('visit_url' => $query[0]->visit_url+1,))->condition('short_url', $arg)->execute();
    
    $query = \Drupal::database()->select('custom_url_data', 't')->condition('short_url', $arg);
    $query->fields('t', ['id','source','short_url','visit_url']);
    $query->distinct();
    $result = $query->execute()->fetchAll();
    $output.='<table>
                <tr><th>ID</th>
                    <th>Source</th>
                    <th>Short Url</th>
                    <th>Visitor count</th>
                </tr>';
    foreach ($result as $row) {

        $output.='<tr>
                    <td>'.$row->id.'</td>
                    <td>'.$row->source.'</td>
                    <td>'.$row->short_url.'</td>
                    <td>'.$row->visit_url.'</td>
                  </tr>';
        
    }
    $output.='</table>';


    $element = array(
      '#markup' => $output,
      '#cache' => array(
        'max-age' => 0,
      ),
      '#allowed_tags' => ['table','th','tr','td','<','>','a'],
    );
    return $element;
  }
}