<?php
function json_response($values, $code, $text) {
  return json_encode(array("responseValues" => $values,
                           "responseCode"  => $code,
                           "responseText"  => $text
                         ));
}

function json_success_response($values) {
  return json_response($values, "0", "OK");
}

?>