<?php

$fields = [
  'start_wrapper'=>'text NULL',
  'end_wrapper'=>'text NULL',
  'option_min'=>'int(10) unsigned NOT NULL',
  'option_max'=>'int(10) unsigned NOT NULL',
];
  
$cols = rex_sql::showColumns(rex::getTablePrefix().'slice_groups');
foreach($fields as $fieldname => $fieldtype) {
  $found = false;
  foreach($cols as $field) {
    if($field['name'] === $fieldname) {
      $found = true;
      break;
    }
  }

  if(!$found) {
    $sql = rex_sql::factory();
    $sql->setQuery("ALTER TABLE `".rex::getTablePrefix().'slice_groups'."` ADD ".$fieldname." ".$fieldtype,array());
  }
}