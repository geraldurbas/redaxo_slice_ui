<?php

if(rex::isBackend()) {
  rex_view::addCssFile($this->getAssetsUrl('slice_footer.css'));
  rex_view::addJsFile($this->getAssetsUrl('slice_footer.js'));
}

if(rex::isBackend() && is_object(rex::getUser()))
  rex_perm::register('slice_ui[editall]', null, rex_perm::OPTIONS);

if(rex::getUser()->hasPerm('slice_ui[editall]')) {
  rex_extension::register('ADD_AFTER_SLICE','slice_footer::editAll');
  // slice_footer::extendSliceButtons();

  if(strpos(rex_request('page'),'content/editall') !== false)
    slice_footer::showForm();
  if(strpos(rex_request('page'),'content/delete') !== false)
    slice_footer::deleteSlices();

  rex_extension::register('STRUCTURE_CONTENT_BEFORE_SLICES','slice_footer::addFooterForm');
  rex_extension::register('STRUCTURE_CONTENT_AFTER_SLICES','slice_footer::addFooterForm');
}