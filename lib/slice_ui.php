<?php

class slice_ui {

  public static function modifySliceEditMenu(rex_extension_point $ep) {


    // rex_extension_point Object ( [name:rex_extension_point:private] => ART_SLICE_MENU [subject:rex_extension_point:private] => Array ( ) [params:rex_extension_point:private] => Array ( [article_id] => 1 [clang] => 1 [ctype] => 1 [module_id] => 1 [slice_id] => 1 [perm] => 1 ) [extensionParams:rex_extension_point:private] => Array ( ) [readonly:rex_extension_point:private] => ) rex_extension_point Object ( [name:rex_extension_point:private] => ART_SLICE_MENU [subject:rex_extension_point:private] => Array ( ) [params:rex_extension_point:private] => Array ( [article_id] => 1 [clang] => 1 [ctype] => 1 [module_id] => 1 [slice_id] => 2 [perm] => 1 ) [extensionParams:rex_extension_point:private] => Array ( ) [readonly:rex_extension_point:private] => )

    if(rex_request('clang','string') !== '')
      $params->clang = rex_request('clang');
    if(rex_request('ctype','string') !== '')
      $params->ctype = rex_request('ctype');

    $Icons = array(
      array(
        'hidden_label' => 'translate:copy',
        'url' => 'index.php?page=content/copy&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
        'attributes' => array(
          'class' => array('btn btn-copy'),
          'title' => 'translate:copy',
          'data-title-online' => 'translate:slice_ui_copied'
        ),
        'icon' => 'copy'
      ),
      array(
        'hidden_label' => '',
        'url' => 'index.php?page=content/cut&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
        'attributes' => array(
          'class' => array('btn btn-cut'),
          'title' => 'translate:copy',
          'data-title-online' => 'translate:slice_ui_cutted'
        ),
        'icon' => 'cut'
      ),
    );
  
    if(!self::checkPermissions(array(
      'article_id' => $ep->getParam('article_id'),
      'clang' => $ep->getParam('clang'),
      'ctype' => $ep->getParam('ctype'),
      'module_id' => $ep->getParam('module_id')
    ))) unset($Icons); // Einfügen soll möglich bleiben

    if($_SESSION['slice_ui']['slice_id'] === $ep->getParam('slice_id') && $_SESSION['slice_ui']['cut'] === true)
      unset($Icons[0]);

    if(!empty($_SESSION['slice_ui']['slice_id']) && $_SESSION['slice_ui']['slice_id'] !== $ep->getParam('slice_id') || ($_SESSION['slice_ui']['slice_id'] === $ep->getParam('slice_id') && $_SESSION['slice_ui']['cut'] !== true)) {
      $Icons[0] = array(
        'hidden_label' => '',
        'url' => 'index.php?page=content/pasteAfter&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
        'attributes' => array(
          'class' => array('btn btn-paste'),
          'title' => 'translate:copy',
          'data-title-online' => 'translate:slice_ui_pasted'
        ),
        'icon' => 'paste',
      );
    }


    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere(array('id'=>$ep->getParam('slice_id')));
    $sql->select();

    $mode = 'visible';
    if($sql->getValue('active') == 0)
      $mode = 'invisible';

    $Icons[] = array(
      'hidden_label' => '',
      'url' => 'index.php?page=content/toggleSlice&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
      'attributes' => array(
        'class' => array('btn btn-'.$mode),
        'title' => 'translate:'.$mode,
        'data-pjax-container' => '#rex-js-page-container',
        'data-title-online' => 'translate:slice_ui_'.$mode
      ),
      'icon' => $mode,
    );

    return $Icons;
  }

  public static function isActive(rex_extension_point $ep) {

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere(array('id'=>$ep->getParam('slice_id')));
    $sql->select();

    if($sql->getValue('active') == 1 || rex::isBackend())
      return $ep->content;
    return '';
  }

  public static function regenerateArticle($slice_id = false,$clang = false,$module_id = false) {
    if(!$slice_id) $slice_id = rex_get('slice_id');
    if(!$article_id) $article_id = rex_get('article_id');
    if(!$clang) $clang = rex_get('clang');
    if(!$module_id) $module_id = rex_get('module_id');
    if(!$ctype) $ctype = rex_get('ctype');

    $newsql = rex_sql::factory();
    $action = new rex_article_action($module_id, $function, $newsql);
    $action->setRequestValues();
    $action->exec(rex_article_action::PRESAVE);

    // ----- artikel neu generieren
    $EA = rex_sql::factory();
    $EA->setTable(rex::getTablePrefix() . 'article');
    $EA->setWhere(['id' => $article_id, 'clang_id' => $clang]);
    $EA->addGlobalUpdateFields();
    $EA->update();
    rex_article_cache::delete($article_id, $clang);

    rex_extension::registerPoint(new rex_extension_point('ART_CONTENT_UPDATED', '', [
      'id' => $article_id,
      'clang' => $clang,
    ]));

    // ----- POST SAVE ACTION [ADD/EDIT/DELETE]
    $action->exec(rex_article_action::POSTSAVE);
    if ($messages = $action->getMessages()) {
      $info .= '<br />' . implode('<br />', $messages);
    }

    if (rex_post('btn_save', 'string')) {
      $function = '';
    }
  }

  public static function copySlice($slice_id = false,$clang = false,$module_id = false) {
    if(!$slice_id) $slice_id = rex_get('slice_id');
    if(!$article_id) $article_id = rex_get('article_id');
    if(!$clang) $clang = rex_get('clang');
    if(!$module_id) $module_id = rex_get('module_id');
    if(!$ctype) $ctype = rex_get('ctype');

    $_SESSION['slice_ui'] = array('slice_id'=>$slice_id,'article_id'=>$article_id,'clang'=>$clang,'module_id'=>$module_id,'ctype'=>$ctype,'cut'=>(rex_get('page') === 'content/cut'));

    // Alle OBs schließen
    while (@ob_end_clean());
    header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.$clang.'&ctype='.$ctype);
    exit;
  }

  public function emptyClipboard() {
    unset($_SESSION['slice_ui']);

    // Alle OBs schließen
    while (@ob_end_clean());
    header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.rex_get('clang').'&ctype='.rex_get('ctype'));
    exit;
  }

  public static function toggleSlice() {

    $sql = rex_sql::factory();
    $sql->setQuery("UPDATE ".rex::getTablePrefix().'article_slice'." SET active = CASE WHEN active = 1 THEN 0 ELSE 1 END WHERE id = ?",array(rex_get('slice_id')));
    self::regenerateArticle();
    // Alle OBs schließen
    while (@ob_end_clean());
    header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.rex_get('clang').'&ctype='.rex_get('ctype'));
    exit;
  }

  public static function addSlice() {

    $article_id = rex_request('article_id', 'int');
    $function   = rex_request('page', 'string');

    $cut_slice_id = $slice_id   = $_SESSION['slice_ui']['slice_id'];
    $module_id  = $_SESSION['slice_ui']['module_id'];
    $clang      = rex_get('clang');
    $ctype      = rex_get('ctype');

    if(!self::checkPermissions(array(
      'article_id' => $article_id,
      'clang' => $clang,
      'ctype' => $ctype,
      'module_id' => $module_id
    ))) {
      // Alle OBs schließen
      while (@ob_end_clean());
      header("Location: ".rex_url::backendController().'?article_id='.$article_id.'&clang='.$clang.'&page=content/edit&ctype='.$ctype);
      exit;
    }

    $slice_revision = 0;
    $template_attributes = [];
    
    $newsql = rex_sql::factory();
    // $newsql->setDebug();

    $sliceTable = rex::getTablePrefix().'article_slice';
    $newsql->setTable($sliceTable);

    if (strpos($function,'content/paste') !== false && !empty($_SESSION['slice_ui'])) {
      // determine priority value to get the new slice into the right order
      $priority = '0';
      // $prevSlice->setDebug();
      if ($function === 'content/paste') {
        $priority = 1;
      } else {
        $prevSlice = rex_sql::factory();
        $prevSlice->setQuery('SELECT * FROM '.$sliceTable.' WHERE id='.rex_get('slice_id'));
        $priority = $prevSlice->getValue('priority')+1;
      }


      $newsql->setValue('article_id', $article_id);
      $newsql->setValue('module_id', $module_id);
      $newsql->setValue('clang_id', $clang);
      $newsql->setValue('ctype_id', $ctype);
      $newsql->setValue('revision', $slice_revision);
      $newsql->setValue('priority', $priority);
    
      $newsql->addGlobalUpdateFields();
      $newsql->addGlobalCreateFields();

      try {
        $newsql->insert();

        $info = $action_message.rex_i18n::msg('block_added');

        $slice_id = $newsql->getLastId();
        if($slice_id !== 0)
          $_SESSION['slice_ui']['slice_id'] = $slice_id;


        rex_sql_util::organizePriorities(
          rex::getTable('article_slice'),
          'priority',
          'article_id='.$article_id.' AND clang_id='.$clang.' AND ctype_id='.$ctype.' AND revision='.$slice_revision,
          'priority, updatedate DESC'
        );

        $function = '';

        // ----- EXTENSION POINT
        $info = rex_extension::registerPoint(new rex_extension_point('SLICE_PASTED', $info, [
          'article_id' => $article_id,
          'clang' => $clang,
          'function' => $function,
          'slice_id' => $slice_id,
          'page' => rex_be_controller::getCurrentPage(),
          'ctype' => $ctype,
          'category_id' => $category_id,
          'module_id' => $module_id,
          'article_revision' => &$article_revision,
          'slice_revision' => &$slice_revision,
        ]));


        if($_SESSION['slice_ui']['cut'] == 1) {

          $curr = rex_sql::factory();
          $curr->setDebug();
          $curr->setTable($sliceTable);
          $curr->setWhere(array('id' => $cut_slice_id));
          $curr->delete();

          rex_sql_util::organizePriorities(
            rex::getTable('article_slice'),
            'priority',
            'article_id='.$_SESSION['slice_ui']['article_id'].' AND clang_id='.$clang.' AND ctype_id='.$ctype.' AND revision='.$slice_revision,
            'priority, updatedate DESC'
          );
        }

      } catch (rex_sql_exception $e) {
        // echo rex_view::warning($e->getMessage());
      }

      // Alle OBs schließen
      while (@ob_end_clean());
      header("Location: ".rex_url::backendController().'?article_id='.$article_id.'&clang='.$clang.'&page=content/edit&ctype='.$ctype);
      exit;
    }
  }

  public static function checkPermissions($ep) {

    $AddonPerm = rex_config::get('slice_ui');

    $article = rex_sql::factory();
    // $article->setDebug();

    $articleTable = rex::getTablePrefix().'article';
    $article->setTable($articleTable);
    // $article->setDebug();

    $article->setQuery('
      SELECT article.*, template.attributes as template_attributes
      FROM '.rex::getTablePrefix().'article as article
      LEFT JOIN '.rex::getTablePrefix().'template as template ON template.id=article.template_id
      WHERE article.id = ? AND clang_id = ?',array($ep['article_id'],$ep['clang']));

    $ctype = 1;
    if(($c = rex_request('ctype')))
      $ctype = $c;

    $template_attributes = json_decode($article->getValue('template_attributes'),1);
    if($template_attributes === null)
      $template_attributes = array();

    $AddonPerm['ctypes'] = $AddonPerm['ctypes'][$article->getValue('template_id')];
    if(!rex_template::hasModule($template_attributes,$ep['ctype'],$ep['module_id'])) {
      return false;
    } elseif (!(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('module['.$ep['module_id'].']') || rex::getUser()->hasPerm('module[0]'))) {
      return false;
    }

    if(strpos(rex_get('page','string'),'content/paste') === false) {
      if(
        (!empty($AddonPerm['modules']) && !in_array('all',$AddonPerm['modules']) && !in_array($ep['module_id'],$AddonPerm['modules'])) || 
        (!empty($AddonPerm['ctypes']) && !in_array('all',$AddonPerm['ctypes']) && !in_array($ep['ctype'],$AddonPerm['ctypes']))) {
        return false;
      }
    }
    return true;
  }
}

?>