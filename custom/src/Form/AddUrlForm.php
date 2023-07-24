<?php
/**
 * @file
 * Contains \Drupal\custom\Form\RatingCustomForm
 */
namespace Drupal\custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Component\Utility\UrlHelper;


class AddUrlForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'addurl_custom_form';
  }

   /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $string = NULL) {



    $form['source'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Source url'),
      '#plceholder' => $this->t('Enter Source url here.'),
      '#required' => TRUE,
      '#maxlength' => 100,
      '#default_value' =>  '',
    ];
    
      $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );

    return $form;
  }

    public function validateForm(array &$form, FormStateInterface $form_state) {
      $baseUrl = Url::fromRoute('<front>', array(), array('absolute' => TRUE))->toString();
        $field = $form_state->getValues();
        $url = $field['source'];

        if (empty($url)) {
            $form_state->setErrorByName('source', $this->t('Please enter source Url.'));
        }
    
        if (strpos($url, $baseUrl) !== 0) {
          $form_state->setErrorByName('source', $this->t('External url not allowed.'));
      }
    }

  public function submitForm(array &$form, FormStateInterface $form_state) {
      $baseUrl = Url::fromRoute('<front>', array(), array('absolute' => TRUE))->toString();

    try{
          $conn = Database::getConnection();
    
          $field = $form_state->getValues();
          $fields["source"] = $field['source'];
          $r_url = explode($baseUrl,$fields["source"]);
     
          $fields["source"] = $field['source'];
          $fields["short_url"] = $this->random_strings(9,$r_url[1]);
          $fields["visit_url"] = 0;

          
          $conn->insert('custom_url_data')->fields($fields)->execute();
          \Drupal::messenger()->addMessage($this->t('Your soruce url data has been succesfully saved.'));
           
          $form_state->setRedirect('custom.url_list');
          return;
        } catch(Exception $ex){
          \Drupal::logger('custom_url_data')->error($ex->getMessage());
        }
              
   }

   public function random_strings($length_of_string,$genurl) {
     
       $str_result = '0123456789'.$genurl;
        return substr(str_shuffle($str_result),0, $length_of_string);
    }
}