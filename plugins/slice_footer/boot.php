<?php

if(rex::isBackend()) {
  rex_view::addCssFile($this->getAssetsUrl('slice_footer.css'));
  rex_view::addJsFile($this->getAssetsUrl('slice_footer.js'));
}

rex_extension::register('ADD_AFTER_SLICE','slice_footer::editAll');
// slice_footer::extendSliceButtons();

if(strpos(rex_request('page'),'content/editall') !== false)
  slice_footer::showForm();
if(strpos(rex_request('page'),'content/delete') !== false)
  slice_footer::deleteSlices();

rex_extension::register('PAGE_CONTENT','slice_footer::addFooterForm');
rex_extension::register('PAGE_CONTENT_BEFORE_SLICES','slice_footer::addFooterForm');