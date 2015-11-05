<?php

class slice_footer {

  public function __construct() {
    if(empty($_SESSION[__CLASS__]))
      $_SESSION[__CLASS__] = array('active'=>0);
  }

  public static function editAll($ep) {
    // if($_SESSION[__CLASS__]['active'] == 1) {
      $fragment = new rex_fragment();
      $fragment->setVar('slice_id', $ep->getParam('slice_id'), false);
      return $fragment->parse('form/check.php');
    // }
  }

  public static function showForm() {

    $article_id = rex_request('article_id', 'int');
    $clang      = rex_get('clang');
    $ctype      = rex_get('ctype');

    $_SESSION[__CLASS__]['active'] = 1;

    // Alle OBs schließen
    while (@ob_end_clean());
    header("Location: ".rex_url::backendController().'?article_id='.$article_id.'&clang='.$clang.'&page=content/edit&ctype='.$ctype);
    exit;
  }
  
  public static function deleteSlices() {
    $IDs = rex_get('slices');
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere("id IN('".implode("','",$IDs)."')",array());
    $sql->delete();

    $_SESSION[__CLASS__]['active'] = 0;

    die();
  }

  // public static function extendSliceButtons() {
  //   // ----- EXTENSION POINT
  //   $hideButtons = rex_extension::registerPoint(new rex_extension_point('HIDE_EDIT_ALL_BUTTONS', '', []));

  //   if($_SESSION[__CLASS__]['active'] == 0 && !$hideButtons && (rex_get('page_buttons') == '' || strpos(rex_get('page_buttons'),__CLASS__) !== false)) {
  //     $Content = rex_plugin::get('structure','content');
  //     $ContentPages = $Content->getProperty('pages');
  //     $ContentPages['content']['subpages']['editall'] = array(
  //       'title'=>'Mehrere bearbeiten',
  //       'icon'=>'rex-icon rex-icon-edit',
  //     );
  //     $Content->setProperty('pages',$ContentPages);
  //   }
  // }

  public static function addFooterForm(rex_extension_point $ep) {
    $fragment = new rex_fragment();
      $fragment->setVar('deletePath', 'index.php?page=content/delete&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'), false);
    return $fragment->parse('page/footer.php');
  }

}