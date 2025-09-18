<?php

function glools_form_error(&$element, $message = '') {
  glools_form_set_error(implode('][', $element['#parents']), $message);
}

function glools_form_set_error($name = NULL, $message = '', $limit_validation_errors = NULL) {
  $form = &drupal_static('form_set_error', array());
  $sections = &drupal_static('form_set_error' . ':limit_validation_errors');
  if (isset($limit_validation_errors)) {
    $sections = $limit_validation_errors;
  }
  
  if (isset($name) && !isset($form[$name])) {
    $record = TRUE;
    if (!empty($sections)) {
      // #limit_validation_errors is an array of "sections" within which user
      // input must be valid. If the element is within one of these sections,
      // the error must be recorded. Otherwise, it can be suppressed.
      // #limit_validation_errors can be an empty array, in which case all
      // errors are suppressed. For example, a "Previous" button might want its
      // submit action to be triggered even if none of the submitted values are
      // valid.
      $record = FALSE;
      foreach ($sections as $section) {
        // Exploding by '][' reconstructs the element's #parents. If the
        // reconstructed #parents begin with the same keys as the specified
        // section, then the element's values are within the part of
        // $form_state['values'] that the clicked button requires to be valid,
        // so errors for this element must be recorded. As the exploded array
        // will all be strings, we need to cast every value of the section
        // array to string.
        if (array_slice(explode('][', $name), 0, count($section)) === array_map('strval', $section)) {
          $record = TRUE;
          break;
        }
      }
    }
    
    if ($record) {
      $form[$name] = $message;
      if ($message) {
        drupal_set_message($message, 'error');
      }
    }
  }
  
  return $form;
}