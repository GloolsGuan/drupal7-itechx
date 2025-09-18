<?php
/**
 * Glools Ajax component
 * 
 * Description:
 *     The glools-ajax-component is a stand alone component.
 *     
 * */

function glools_ajax_deliver($page_callback_result) {
  
  // Browsers do not allow JavaScript to read the contents of a user's local
  // files. To work around that, the jQuery Form plugin submits forms containing
  // a file input element to an IFRAME, instead of using XHR. Browsers do not
  // normally expect JSON strings as content within an IFRAME, so the response
  // must be customized accordingly.
  // @see http://malsup.com/jquery/form/#file-upload
  // @see Drupal.ajax.prototype.beforeSend()
  $iframe_upload = !empty($_POST['ajax_iframe_upload']);

  // Emit a Content-Type HTTP header if none has been added by the page callback
  // or by a wrapping delivery callback.
  if (is_null(drupal_get_http_header('Content-Type'))) {
    if (!$iframe_upload) {
      // Standard JSON can be returned to a browser's XHR object, and to
      // non-browser user agents.
      // @see http://www.ietf.org/rfc/rfc4627.txt?number=4627
      drupal_add_http_header('Content-Type', 'application/json; charset=utf-8');
    }
    else {
      // Browser IFRAMEs expect HTML. With most other content types, Internet
      // Explorer presents the user with a download prompt.
      drupal_add_http_header('Content-Type', 'text/html; charset=utf-8');
    }
  }
  
  // Print the response.
  $json = glools_ajax_render($page_callback_result);
  if (!$iframe_upload) {
    // Standard JSON can be returned to a browser's XHR object, and to
    // non-browser user agents.
    print $json;
  }
  else {
    // Browser IFRAMEs expect HTML. Browser extensions, such as Linkification
    // and Skype's Browser Highlighter, convert URLs, phone numbers, etc. into
    // links. This corrupts the JSON response. Protect the integrity of the
    // JSON data by making it the value of a textarea.
    // @see http://malsup.com/jquery/form/#file-upload
    // @see http://drupal.org/node/1009382
    print '<textarea>' . $json . '</textarea>';
  }

  // Perform end-of-request tasks.
  ajax_footer();
}

/**
 * keys:
 *     status: success, failed
 *     response: data
 *     format: text, html, json 
 * */
function glools_ajax_render($response){
	$keys = array('status', 'response', 'format');
    foreach($keys as $key) {
    	if (!isset($response[$key])) {
    		$response['status']   = 'failed';
            $response['response'] = 'Invalid ajax response data format.';
            $response['format']   = 'text';
    	}
    }
    
    return json_encode($response);
}