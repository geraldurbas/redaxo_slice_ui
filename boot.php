<?php


if(rex::isBackend() && is_object(rex::getUser())) {
  rex_perm::register('copy[]');
  rex_perm::register('slice_ui[]', null, rex_perm::OPTIONS);
  rex_perm::register('slice_ui[settings]', null, rex_perm::OPTIONS);
}

if(strpos(rex_request('page'),'content/emptyclipboard') !== false)
  slice_ui::emptyClipboard();

rex_view::addCssFile($this->getAssetsUrl('slice_ui.css'));

if(strpos(rex_request('page'),'content/paste') !== false)
  slice_ui::addSlice();

if(strpos(rex_request('page'),'content/toggleSlice') !== false)
  slice_ui::toggleSlice();

if(is_object(rex::getUser()) && ((!rex::getUser()->hasPerm('editContentOnly[]') && rex::getUser()->hasPerm('slice_ui[]') || rex::getUser()->isAdmin())))
  rex_extension::register('ART_SLICE_MENU','slice_ui::modifySliceEditMenu');

if(is_object(rex::getUser()) && (rex_request('page','string') === 'content/copy' || rex_request('page','string') === 'content/cut'))
  slice_ui::copySlice();

rex_extension::register('SLICE_SHOW','slice_ui::isActive');

/* Slice-Menü überschreiben */
if(!empty($_SESSION['slice_ui'])) {
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