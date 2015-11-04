<?php

$fields = [
  'active'=>'char(1) NOT NULL default "1"',
  'online_from' => 'int(11) UNSIGNED NOT NULL DEFAULT "0"',
  'online_to' => 'int(11) UNSIGNED NOT NULL DEFAULT "0"',
];
foreach($fields as $fieldname => $fieldtype) {
  $found = false;
  $cols = rex_sql::showColumns(rex::getTablePrefix().'article_slice');
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