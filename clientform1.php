<?php

namespace DF;

function clientform1 () {  
  
  $page1 = array(
    '@properties' => array(      
      '@weight' => array(
        'field1' => '1',
        'field2' => '2',
        'field3' => '3',
      ),
      '@required' => array(
        'field1' => TRUE,
        //               ( ename, condition, value )
        'field2' => array('field1', '==', 'checkbox'),
        'field3' => array('field1', '==', 'textfield'),
      )
    ),
    'field1' => array(
      '@name' => 'field1',
      '#title' => 'What type of field do you want?',
      '@L:fra#title' => 'French: What type of field do you want?',
      '#type' => 'select',
      '#options' => array('none' => 'None', 'checkbox' => 'Checkbox', 'textfield' => 'Textfield'),
      '@L:fra#options' => array('none' => 'F(None)', 'checkbox' => 'F(Checkbox)', 'textfield' => 'F(Textfield)'),
      '#default_value' => 'none',
    ),
    'field2' => array(
      '@name' => 'field2',
      '#title' => 'Here is a checkbox',
      '@L:fra#title' => 'French: Here is a checkbox',
    ),
    'field3' => array(
      '@name' => 'field3',
      '#title' => 'Here is a textfield',
      '@L:fra#title' => 'French: Here is a textfield',
    )
  );
  
  $form = array(
    '@properties' => array(
      '@weight' => array(
        'page1' => 1,
      )
    ),
    'page1' => $page1,
    //$page2,
    //$page3,
  );
  
  /*
  $form['field_type_select'] = array(
    '#title' => t('What type of field do you want?'),
    '#type' => 'select',
    '#options' => array('none' => 'None', 'checkbox' => 'Checkbox', 'textfield' => 'Textfield'),
    '#default_value' => $default,
    '#ajax' => array(
      'callback' => 'dynamic_forms_generator_callback',
      'wrapper' => 'dynamic-fields-div',
      'method' => 'replace',
      'effect' => 'fade',
    ),
  );
   * 
   */
  
  return $form;
}
