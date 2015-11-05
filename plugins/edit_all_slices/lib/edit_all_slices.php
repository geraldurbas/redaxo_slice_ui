<?php

class edit_all_slices {

  public static function editAll($ep) {
    $fragment = new rex_fragment();
    return $fragment->parse('form/check.php');
  }

}