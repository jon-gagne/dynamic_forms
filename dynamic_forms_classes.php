<?php

namespace DF;

/**
 * Generic Form Class
 */
class Form {
  public static $default_lang = 'eng';
  public static $lang_list = array('eng', 'fra');
  public $translations = array();
  public $name = '';
  public $default_page;
  
  public $pages = array();
  public $page_properties = array();
  public $form_properties;
  
  private $page_id_counter = 0;
  private static $form_id_counter = 0;
  
  /**
   * Form Constructor
   */
  public function __construct() {
    // Create array for language translation
    foreach (Form::$lang_list as $lang) {
      if ($lang != Form::$default_lang) {
        $this->translations[$lang] = array();
      }
    }
  }
  
  /**
   * Builds a form from a script (Factory Function)
   */
  public static function build(array $formArray) {
    $formObj = new Form();
    
    // Build form from array
    foreach ($formArray as $key => $value) {
      // Do not add @properties (or anything that starts with @)
      if ($key[0] != '@') {
        // set page name if not set
        if (!isset($value['@name']) OR $value['@name'] != '') {
          $value['@name'] = $key;
        }
        // Build page from array inside $value
        $page = Page::build($value);
        $formObj->addPage($page);
      }
    }
   
    // Add properties
    if (array_key_exists('@properties', $key)) {
      // Add properties to form
      $formObj->form_properties = $value;
      // Set form name if exists
      if (array_key_exists('@name', $value)) {
        $formObj->setName($value['@name']);
      }
    }
    // If form name not set...
    if ($formObj->getName() == '') {
      // then create a generic name
      $formObj->setName('form' . Form::$form_id_counter++);      
    }
    
    // Return new form
    return $formObj;
  }
  
  /**
   * Adds a group to the form
   */
  public function addPage(Page $page) {
    // Add form reference to page
    $page->form_ref = $this;
    // If page name is set
    if (($element->getName() != FALSE) AND ($page->getName() != '')) {
      $this->pages[$page->getName()] = $page;
    }
    // If name not set, generate a name
    else {
      $this->pages['page' . $this->page_id_counter++] = $page;
    }
    // Set default page if not set
    $this->default_page = $page;    
  }
  
  // Get & Set Functions
  public function getName() { return $this->name; }
  public function setName($newName) {
    $this->name = $newName;
    $this->form_properties['@name'] = $newName;
  } 
}



/**
 * Generic Page Class
 */
class Page {
  public $elements = array();  
  public $page_element_properties = array();
  public $page_properties = array();
  public $name = '';
  public $form_ref;
  public $translations;
  
  public $dependencies = array();
  public $required_by = array();
  
  private $element_id_counter = 0;
  
  /** 
   * Group Constructor
   */
  public function __construct() {
    // Create array for language translation
    foreach (Form::$lang_list as $lang) {
      if ($lang != Form::$default_lang) {
        $this->translations[$lang] = array();
      }
    }
  }
  
  /**
   * Builds a Page Instance (Factory Function)
   */
  public static function build(array $pageArray) {
    $pageObj = new Page();
    
    // Build page from array
    foreach ($pageArray as $key => $value) {
      // Do not add @properties (or anything that starts with @)
      if ($key[0] != '@') {
        // set page name if not set
        if (!isset($value['@name']) OR $value['@name'] != '') {
          $value['@name'] = $key;
        }
        // Build page from array inside $value
        $element = Element::build($value);
        $pageObj->addElement($element);
      }
    }
   
    // Add properties
    if (array_key_exists('@properties', $key)) {
      // Add properties to form
      $pageObj->page_properties = $value;
      // Set form name if exists
      if (array_key_exists('@name', $value)) {
        $pageObj->setName($value['@name']);
      }
    }
    // If form name not set...
    if ($pageObj->getName() == '') {
      // then create a generic name
      $pageObj->setName('page' . Page::$page_id_counter++);      
    }
    
    return $pageObj;
  }
  
  
  /**
   * Adds an element to the group
   */
  public function addElement(Element $element) {
    //$this->elements[$this->element_id_counter++] = $element;
    if (($element->getName() != FALSE) AND ($element->getName() != '')) {
      $this->pages[$element->getName()] = $element;
    }
    // If name not set, generate a name
    else {     
      $this->pages['element' . $this->$element_id_counter++] = $element;
    }
  }
  
  
  /**
   * Renders Dynamic Form Page to Form API Form
   */
  public function render() {
    $page = array();
    
    // Render the page
    foreach ($this->elements as $name => $element) {
      $page[$name] = $element->render();
    }
    
    // Add default submit if not specified
    if (!isset($page['sumbit'])) {
      $page['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit'),
      );
    }
    
    // Adds page level properties to elements
    foreach ($this->page_element_properties as $property_name => $item) {
      foreach ($item as $element_name => $val_cond) {
        if (isset($page[$element_name])) {
          if (is_array($val_cond)) {
            $cond_element = $val_cond[0];
            $cond_operator = $val_cond[1];
            $cond_value = $val_cond[2];
            if (dynamic_forms_value_submitted($cond_element)) {
              $cond_element_value = dynamic_forms_get_value($cond_element);
            }
          }
          else {
            $value = $val_cond;
          }
          //$page[$element_name]->attributes[]
        }
      }
    }
    
    // Return rendered page
    return $page;
  }
  
  
  // Get & Set Functions
  public function getName() { return $this->name; }
  public function setName($newName) { 
    $this->name = $newName;
    $this->page_properties['@name'] = $newName;
  } 
}


/**
 * Generic Element Class
 */
class Element {

  public $attributes = array();
  public $element_properties = array();
  public $dependencies = array();
  public $required_by = array();
  public $name;
  public $translations = array();
  public $value = '';
  public $value_set = false;

  /**
   * Element Constructor
   */
  public function __construct() {
    // Create array for language translation
    foreach (Form::$lang_list as $lang) {
      if ($lang != Form::$default_lang) {
        $this->translations[$lang] = array();
      }
    }
  }

  /**
   * Builds a Page Instance (Factory Function)
   */
  public static function build(array $elementArray) {
    // Create new element object
    $elementObj = new Element();

    // Add properties and attributes
    foreach ($elementArray as $key => $value) {
      // Add a standard Forms API attribute
      if ($key[0] == '#') {
        $elementObj->attributes[$key] = $value;
      }
      // Add a dynamics forms property/attribut
      elseif ($key[0] == '@') {
        // Add a translation element
        if (substr($key, 1, 3) == 'L:') {
          // Get position of Form API attribut
          $pos = strpos($key, '#');
          if ($pos AND $pos > 3) {
            // Get language
            $lang = substr($key, 3, $pos - 3);
            // Get translated attribute
            $attribute = substr($key, $pos);
            // Create translation, which is stored in $value
            $elementObj->translations[$lang][$attribute] = $value;
          }
        }
        /*
          elseif ($key == '@name') {
          // TODO: Add code
          }
         */
        // Build all other dynamic forms properties
        else {
          $elementObj->element_properties[$key] = $value;
        }
      }
    }

    // Return new element
    return $elementObj;
  }

  /**
   * Renders Dynamic Form Element to Form API Field Element
   */
  public function render() {
    $field = $this->attributes;
    // TODO: Add code here
    return $field;
  }

  // Get & Set Functions
  public function getName() {
    return $this->name;
  }

  public function setName($newName) {
    $this->name = $newName;
    //TODO: add name as property
  }

}

/**
 * Get any submitted values
 */
function dynamic_forms_get_value($field) {
  // TODO: Get value

  $value = '';
  // Return value;
  return $value;
}

/**
 * Get any submitted values
 */
function dynamic_forms_value_submitted($field) {
  // TODO: Check if value exists  
  $result = false;
  // Return value;
  return $result;
}

/**
 * Get any submitted values
 */
function dynamic_forms_evaluate_condition(array $condition) {
  // Get conditional components
  $cond_element = trim($condition[0]);
  $cond_operator = trim($condition[1]);
  $cond_value = trim($condition[2]);

  // Check if a field has been selected
  if (dynamic_forms_get_value()) {

    // Get the value
    $cond_element_value = dynamic_forms_value_submitted($cond_element);

    // Check conditional statement
    if ($cond_operator == 'no_value') {
      return FALSE;
    }
    elseif ($cond_operator == 'any_value') {
      return TRUE;
    }
    elseif ($cond_operator == '==') {
      return $cond_element_value == $cond_value;
    }
    elseif ($cond_operator == '!=') {
      return $cond_element_value != $cond_value;
    }
    elseif ($cond_operator == '<>') {
      return $cond_element_value <> $cond_value;
    }
    elseif ($cond_operator == '<') {
      return $cond_element_value < $cond_value;
    }
    elseif ($cond_operator == '>') {
      return $cond_element_value > $cond_value;
    }
    elseif ($cond_operator == '<=') {
      return $cond_element_value <= $cond_value;
    }
    elseif ($cond_operator == '>=') {
      return $cond_element_value >= $cond_value;
    }
  }
  // Field not submitted
  else {
    if ($cond_operator == 'no_value') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}

