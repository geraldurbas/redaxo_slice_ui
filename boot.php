<?php

$Config = rex_config::get('slice_ui');

if(empty($_SESSION['slice_ui']))
  slice_ui::emptyClipboard(1);

if(rex::isBackend() && is_object(rex::getUser())) {
  rex_perm::register('copy[]');
  rex_perm::register('slice_ui[]', null, rex_perm::OPTIONS);
  rex_perm::register('slice_ui[settings]', null, rex_perm::OPTIONS);
}

if(rex::isBackend()) {
  rex_view::addCssFile($this->getAssetsUrl('slice_ui.css'));
  rex_view::addCssFile($this->getAssetsUrl('jquery-ui.datepicker.css'));

  rex_view::addJsFile($this->getAssetsUrl('slice_ui.js'));
  rex_view::addJsFile($this->getAssetsUrl('jquery-ui.datepicker.js'));
}

if(rex_get('function') == '')
  rex_extension::register('SLICE_SHOW','slice_ui::extendBackendSlices');
rex_extension::register('SLICE_SHOW','slice_ui::isActive');

if(strpos(rex_request('page'),'content/emptyclipboard') !== false)
  slice_ui::emptyClipboard();

if(!empty($Config['general']['sticky_slice_nav']) && $Config['general']['sticky_slice_nav'])
rex_view::addJsFile($this->getAssetsUrl('sticky_header.js'));

if(strpos(rex_request('page'),'content/paste') !== false)
  slice_ui::addSlice();

if(strpos(rex_request('page'),'content/move') !== false)
  slice_ui::moveSlice();

if(strpos(rex_request('page'),'content/toggleSlice') !== false || strpos(rex_request('page'),'content/status') !== false)
  slice_ui::toggleSlice();

if(is_object(rex::getUser()) && ((!rex::getUser()->hasPerm('editContentOnly[]') && rex::getUser()->hasPerm('slice_ui[]') || rex::getUser()->isAdmin()))) {
  rex_extension::register('ART_SLICE_MENU','slice_ui::modifySliceEditMenu');
}

if(is_object(rex::getUser()) && (rex_request('page','string') === 'content/copy' || rex_request('page','string') === 'content/cut'))
  slice_ui::copySlice();


/* Slice-Menü überschreiben */
if(!empty($_SESSION['slice_ui']['slice_id']) && !empty($Config['general']['copy_n_cut']) && $Config['general']['copy_n_cut']) {
  slice_ui::extendSliceButtons();
}