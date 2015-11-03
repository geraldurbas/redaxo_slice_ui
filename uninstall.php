<?php

$found = false;
$cols = rex_sql::showColumns(rex::getTablePrefix().'article_slice');
foreach($cols as $field) {
  if($field['name'] === 'active') {
    $found = true;
    break;
  }
}

if($found) {
  $sql = rex_sql::factory();
  $sql->setQuery("ALTER TABLE `".rex::getTablePrefix().'article_slice'."` DROP active",array());
}