<?php
/**
 * FDL Zürich Uploader - Redaxo Addon
 *
 * @author sascha.weidner@factorylabs.com
 *
 * @package redaxo4
 * @version 1.0
 */

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$mypage = 'slice_ui';
$myroot = $REX['INCLUDE_PATH'].'/addons/'.$mypage;

$I18N_slice_ui = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

$REX['ADDON']['version'][$mypage] = $I18N_slice_ui->msg('slice_ui_version');
$REX['ADDON']['author'][$mypage] = 'Sascha Weidner';

$REX['ADDON']['rxid'][$mypage] = $mypage;
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = $I18N_slice_ui->msg('slice_ui_menu_link');
$REX['ADDON']['perm'][$mypage] = 'copySlice[]';
$REX['PERM'][] = 'copySlice[]';


$REX['ADDON'][$mypage]['backend'] = '1';
$REX['ADDON'][$mypage]['frontend'] = '1';

if(rex_request('reset_clipboard'))
  unset($_SESSION['slice_ui']);

require_once($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/settings.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/classes/class.rex_copy.inc.php');

if(rex_request('page') == 'content')
  rex_register_extension('PAGE_HEADER', 'rex_copy::appendToPageHeader');

if(rex_request('function') == 'insertSlice')
  rex_copy::addSlice(rex_get('article_id'),rex_get('slice_id'));

if (is_object($REX['USER']) && ((!$REX['USER']->hasPerm('editContentOnly[]') && $REX['USER']->hasPerm('copySlice[]') || $REX['USER']->isAdmin()))) {
  if(!empty($_SESSION['slice_ui']) || rex_request('function') == 'copySlice') {
    rex_register_extension('ART_SLICE_MENU', 'rex_copy::insertSlice');
    rex_register_extension('PAGE_CONTENT_MENU','rex_copy::insertPageSlice');
  }
  rex_register_extension('ART_SLICE_MENU', 'rex_copy::modifySliceEditMenu');
}

if (isset($REX['USER']) && rex_request('function') == 'copySlice')
  rex_copy::copySlice(rex_get('slice_id'),rex_get('clang'));
?>