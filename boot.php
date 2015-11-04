<?php

if(empty($_SESSION['slice_ui']))
  slice_ui::emptyClipboard(1);

if(rex::isBackend() && is_object(rex::getUser())) {
  rex_perm::register('copy[]');
  rex_perm::register('slice_ui[]', null, rex_perm::OPTIONS);
  rex_perm::register('slice_ui[settings]', null, rex_perm::OPTIONS);
}

if(strpos(rex_request('page'),'content/emptyclipboard') !== false)
  slice_ui::emptyClipboard();

rex_view::addCssFile($this->getAssetsUrl('slice_ui.css'));
rex_view::addCssFile($this->getAssetsUrl('jquery-ui.datepicker.css'));
rex_view::addJsFile($this->getAssetsUrl('slice_ui.js'));
rex_view::addJsFile($this->getAssetsUrl('jquery-ui.datepicker.js'));

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

rex_extension::register('SLICE_SHOW','slice_ui::addOnlineForm');
rex_extension::register('SLICE_SHOW','slice_ui::isActive');


$Config = rex_config::get('slice_ui');

/* Slice-Menü überschreiben */
if(!empty($_SESSION['slice_ui']) && !empty($Config['general']['copy_n_cut']) && $Config['general']['copy_n_cut']) {
  $Content = rex_plugin::get('structure','content');
  $ContentPages = $Content->getProperty('pages');
  $ContentPages['content']['subpages']['paste'] = array(
    'title'=>'Einfügen',
    'icon'=>'rex-icon rex-icon-paste',
  );
  $ContentPages['content']['subpages']['emptyclipboard'] = array(
    'title'=>'Clipboard löschen',
    'icon'=>'rex-icon rex-icon-emptyclipboard',
  );
  $Content->setProperty('pages',$ContentPages);
}