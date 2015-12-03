<?php

$fields = [
  'group_template'=>'int(10) NOT NULL default 0',
  'group_closed'=>'int(10) NOT NULL default 0',
];
  
$cols = rex_sql::showColumns(rex::getTablePrefix().'article_slice');
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
    $sql->setQuery("ALTER TABLE `".rex::getTablePrefix().'article_slice'."` ADD ".$fieldname." ".$fieldtype,array());
  }
}