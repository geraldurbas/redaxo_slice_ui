<?php

/**
 * @Index:
 * - Permissions
 * - Assets
 * - Buttons: Einfügen / Clipboard löschen
 * - Clipboard löschen
 * - Slice hinzufügen
 * - Slice verschieben
 * - Slice aktivieren/deaktivieren
 * - Slice-Aktionenleiste (Copy, Move,...)
 * - Slice kopieren
 * - Slice-Menü überschreiben
 */

$Config = rex_config::get('slice_ui');

if(empty($_SESSION['slice_ui']))
  slice_ui::emptyClipboard(1);

rex_extension::register('PAGE_BODY_ATTR',function($ep) {
  $Subject = $ep->getSubject();
  if($_SESSION['slice_ui']['slice_id'] != 0) {
    $Subject['class'][] = 'copy';
  }
  return $Subject;
});

/* Permissions */
if(rex::isBackend() && is_object(rex::getUser())) {
  rex_perm::register('slice_ui[]', null);
  rex_perm::register('slice_ui[copy]', null, rex_perm::OPTIONS);
  rex_perm::register('slice_ui[status]', null, rex_perm::OPTIONS);
  rex_perm::register('slice_ui[move]', null, rex_perm::OPTIONS);
  rex_perm::register('slice_ui[settings]', null, rex_perm::OPTIONS);
}

/* Assets */
if(rex_addon::get('assets')->isAvailable()) {
  rex_extension::register('BE_ASSETS',function($ep) {
    $Subject = $ep->getSubject()?$ep->getSubject():[];
    $Subject[$this->getPackageId()] = [
      'files' => [
        $this->getPath('assets/slice_ui.less'),
        $this->getPath('assets/jquery-ui.datepicker.less'),
        $this->getPath('assets/slice_ui.js'),
        $this->getPath('assets/jquery-ui.datepicker.js|compressed'),
      ],
      'addon' => $this->getPackageId(),
    ];
    return $Subject;
  });
} elseif(rex::isBackend()) {
  rex_view::addCssFile($this->getAssetsUrl('slice_ui.less.min.css'));
  rex_view::addCssFile($this->getAssetsUrl('jquery-ui.datepicker.less.min.css'));

  rex_view::addJsFile($this->getAssetsUrl('slice_ui.jsmin.min.js'));
  rex_view::addJsFile($this->getAssetsUrl('jquery-ui.datepicker.jsmin.min.js'));
}

/* Einfügen / Clipboard löschen */
if(rex_post('update_slice_status') != 1 && rex_post('btn_update') != 1 && rex_get('function') == '')
  rex_extension::register('SLICE_SHOW','slice_ui::extendBackendSlices');
rex_extension::register('SLICE_SHOW','slice_ui::isActive');

/* Clipboard löschen */
if(strpos(rex_request('page'),'content/emptyclipboard') !== false)
  slice_ui::emptyClipboard();

/* Slice hinzufügen */
if(strpos(rex_request('page'),'content/paste') !== false)
  slice_ui::addSlice();

/* Slice verschieben */
if(strpos(rex_request('page'),'content/move') !== false)
  slice_ui::moveSlice();

/* Slice aktivieren/deaktivieren */
if(strpos(rex_request('page'),'content/toggleSlice') !== false || strpos(rex_request('page'),'content/status') !== false)
  slice_ui::toggleSlice();

/* Slice-Aktionenleiste (Copy, Move,...) */
if(is_object(rex::getUser()) && ((!rex::getUser()->hasPerm('editContentOnly[]') && rex::getUser()->hasPerm('slice_ui[]') || rex::getUser()->isAdmin()))) {
  rex_extension::register('STRUCTURE_CONTENT_SLICE_MENU','slice_ui::extendSliceIconBar');
}

/* Slice kopieren */
if(is_object(rex::getUser()) && (rex_request('page','string') === 'content/copy' || rex_request('page','string') === 'content/cut'))
  slice_ui::copySlice();


/* Slice-Menü überschreiben */
if(!empty($Config['general']['copy_n_cut']) && $Config['general']['copy_n_cut']) {
  slice_ui::extendSliceButtons();
}