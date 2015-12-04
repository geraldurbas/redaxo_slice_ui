<?php

if(rex::isBackend() && is_object(rex::getUser())) {
  rex_perm::register('slice_group[]', null, rex_perm::OPTIONS);
}

if(is_object(rex::getUser()) && (!rex::getUser()->hasPerm('slice_group[]') || rex::getUser()->isAdmin())) {
  rex_extension::register('SLICE_FOOTER','slice_group::addGroupForm');
}

if(rex_addon::get('assets')->isAvailable()) {
  rex_extension::register('BE_ASSETS',function($ep) {
    $Subject = $ep->getSubject()?$ep->getSubject():[];
    $Subject[$this->getPackageId()] = [
      'files' => [
        $this->getPath('assets/slice_group.less'),
      ],
      'addon' => $this->getPackageId(),
    ];
    return $Subject;
  }, rex_extension::EARLY);
} elseif(rex::isBackend()) {
  rex_view::addCssFile($this->getAssetsUrl('slice_group.less.min.css'));
}

if(rex_post('change_group_template','string') === '1') {
  $sql = rex_sql::factory();
  $sql = $sql->setTable(rex::getTablePrefix().'article_slice');
  $sql->setWhere(array('id'=>rex_post('slice_id')));
  $sql->setValue('group_template',rex_post('group_template','int'));
  $sql->setValue('group_closed',rex_post('group_closed','int'));

  try {
    $sql->update();
  } catch (rex_sql_exception $e) {
    rex_view::warning($e->getMessage());
  }
}

if(rex::isBackend() && rex_post('update_slice_status') != 1 && rex_get('function') == '') {
  rex_extension::register('BEFORE_SLICE','slice_group::setupGroup');
  rex_extension::register('AFTER_SLICE','slice_group::closeGroup');
  rex_extension::register('STRUCTURE_CONTENT_AFTER_SLICES','slice_group::closeGroups');
} elseif(!rex::isBackend()) {
  rex_extension::register('SLICE_OUTPUT','slice_group::fronendGroups');
}
