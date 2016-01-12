<?php

if(rex::isBackend()) {
  rex_view::addCssFile($this->getAssetsUrl('slice_footer.css'));
  rex_view::addJsFile($this->getAssetsUrl('slice_footer.js'));
}

if(is_object(rex::getUser())) {
  if(rex::isBackend() && is_object(rex::getUser()))
    rex_perm::register('slice_ui[editall]', null, rex_perm::OPTIONS);

  if(rex::getUser()->hasPerm('slice_ui[editall]')) {
    rex_extension::register('SLICE_UI_SLICE_FOOTER','slice_footer::editAll');

    if(strpos(rex_request('page'),'content/editall') !== false)
      slice_footer::showForm();
    if(strpos(rex_request('page'),'content/delete') !== false)
      slice_footer::deleteSlices();

    rex_extension::register('STRUCTURE_CONTENT_BEFORE_SLICES','slice_footer::addFooterForm');
    rex_extension::register('STRUCTURE_CONTENT_AFTER_SLICES','slice_footer::addFooterForm',rex_extension::LATE);
  }
}