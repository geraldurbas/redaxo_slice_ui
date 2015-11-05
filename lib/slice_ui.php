<?php

class slice_ui {

  public static function modifySliceEditMenu(rex_extension_point $ep) {

    $Config = rex_config::get('slice_ui');
    // rex_extension_point Object ( [name:rex_extension_point:private] => ART_SLICE_MENU [subject:rex_extension_point:private] => Array ( ) [params:rex_extension_point:private] => Array ( [article_id] => 1 [clang] => 1 [ctype] => 1 [module_id] => 1 [slice_id] => 1 [perm] => 1 ) [extensionParams:rex_extension_point:private] => Array ( ) [readonly:rex_extension_point:private] => ) rex_extension_point Object ( [name:rex_extension_point:private] => ART_SLICE_MENU [subject:rex_extension_point:private] => Array ( ) [params:rex_extension_point:private] => Array ( [article_id] => 1 [clang] => 1 [ctype] => 1 [module_id] => 1 [slice_id] => 2 [perm] => 1 ) [extensionParams:rex_extension_point:private] => Array ( ) [readonly:rex_extension_point:private] => )
    // print_r($ep);

    if(!empty($Config['general']['copy_n_cut']) && $Config['general']['copy_n_cut']) {
      $Icons = array(
        array(
          'hidden_label' => rex_i18n::msg('slice_ui_copy'),
          'url' => 'index.php?page=content/copy&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
          'attributes' => array(
            'class' => array('btn-copy'),
            'title' =>rex_i18n::msg('slice_ui_copy'),
            'data-title-online' => rex_i18n::msg('slice_ui_slice_ui_copied')
          ),
          'icon' => 'copy'
        ),
        array(
          'hidden_label' => rex_i18n::msg('slice_ui_cut'),
          'url' => 'index.php?page=content/cut&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
          'attributes' => array(
            'class' => array('btn-cut'),
            'title' => rex_i18n::msg('slice_ui_cut'),
            'data-title-online' => rex_i18n::msg('slice_ui_slice_ui_cutted')
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
          'hidden_label' => rex_i18n::msg('slice_ui_paste'),
          'url' => 'index.php?page=content/pasteAfter&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
          'attributes' => array(
            'class' => array('btn-paste'),
            'title' => rex_i18n::msg('slice_ui_paste'),
            'data-title-online' => rex_i18n::msg('slice_ui_slice_ui_pasted')
          ),
          'icon' => 'paste',
        );
      }
    }

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere(array('id'=>$ep->getParam('slice_id')));
    $sql->select();

    $mode = 'visible';
    if($sql->getValue('active') == 0)
      $mode = 'invisible';

    if(!empty($Config['general']['slice_status']) && $Config['general']['slice_status']) {
      $Icons[] = array(
        'hidden_label' => rex_i18n::msg('slice_ui_toggle_'.$mode),
        'url' => 'index.php?page=content/toggleSlice&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype').'&visible='.$sql->getValue('active'),
        'attributes' => array(
          'class' => array('btn-'.$mode),
          'title' => rex_i18n::msg('slice_ui_toggle_'.$mode),
          'data-state' => $mode,
          'data-title-online' => rex_i18n::msg('slice_ui_slice_toggled')
        ),
        'icon' => $mode,
      );
    }

    if(!empty($Config['general']['drag_n_drop']) && $Config['general']['drag_n_drop']) {
      $Icons[] = array(
        'hidden_label' => rex_i18n::msg('slice_ui_move'),
        'url' => 'index.php?page=content/move&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
        'attributes' => array(
          'class' => array('btn-move-up-n-down','hide',!empty($Config['general']['keep_move_arrows']) && $Config['general']['keep_move_arrows']?'keep_arrows':'remove_arrows'),
          'title' => rex_i18n::msg('slice_ui_toggle_move'),
          'data-prio' => '',
          'data-title-online' => rex_i18n::msg('slice_ui_slice_moved')
        ),
        'icon' => 'move-up-n-down',
      );
    }

    return $Icons;
  }

  public static function extendBackendSlices(rex_extension_point $ep) {
    $Config = rex_config::get('slice_ui');

    $article_id = rex_get('article_id');
    $clang = rex_get('clang');
    $ctype = rex_get('ctype');

    $content = '';
    $Subject = $ep->getSubject();
    if(rex::isBackend() && !empty($Config['online_from_to']) && (in_array($ep->getParam('module_id'),$Config['online_from_to']) || in_array('all',$Config['online_from_to']))) {
      

      $sql = rex_sql::factory();
      $sql->setTable(rex::getTablePrefix().'article_slice');
      $sql->setWhere(array('id'=>$ep->getParam('slice_id')));
      $sql->select();

      $online_from = $sql->getValue('online_from');
      $online_to = $sql->getValue('online_to');

      $fragment = new rex_fragment();
      $fragment->setVar('online_from', $online_from?date('d.m.Y',$online_from):null, false);
      $fragment->setVar('online_to', $online_to?date('d.m.Y',$online_to):null, false);
      $content = $fragment->parse('status/status.php');
    }

    $strContent = rex_extension::registerPoint(new rex_extension_point('EXTEND_SLICE_FORMS', '', [
      'slice_id' => $ep->getParam('slice_id'),
      'article_id' => $ep->getParam('article_id'),
      'clang' => $ep->getParam('clang'),
      'ctype' => $ep->getParam('ctype'),
      'content' => $Subject
    ]));

    if($strContent)
      $content .= $strContent;

    if($content) {
      $fragment = new rex_fragment();
      $fragment->setVar('action', 'index.php?page=content/status&article_id='.$article_id.'&clang='.$clang.'&ctype='.$clang, false);
      $fragment->setVar('slice_id', $ep->getParam('slice_id'), false);
      $fragment->setVar('body', $content, false);
      $content = $fragment->parse('status/slice_form.php');
    }

    $strContent = rex_extension::registerPoint(new rex_extension_point('ADD_AFTER_SLICE_FORMS', '', [
      'slice_id' => $ep->getParam('slice_id'),
      'article_id' => $ep->getParam('article_id'),
      'clang' => $ep->getParam('clang'),
      'ctype' => $ep->getParam('ctype'),
      'content' => $Subject
    ]));

    if($strContent)
      $content .= $strContent;
    
    $Subject = str_replace('<div class="panel-body">',$content.'<div class="panel-body">',$Subject);

    $strContent = rex_extension::registerPoint(new rex_extension_point('ADD_AFTER_SLICE', '', [
      'slice_id' => $ep->getParam('slice_id'),
      'article_id' => $ep->getParam('article_id'),
      'clang' => $ep->getParam('clang'),
      'ctype' => $ep->getParam('ctype'),
      'content' => $Subject
    ]));

    if($strContent) {
      $fragment = new rex_fragment();
      $fragment->setVar('body', $strContent, false);
      $strContent = $fragment->parse('panel/footer.php');
      $Subject = preg_replace('|(<\/div>)([^<]*<\/div>[^<]*<\/section>[^<]*<\/li>$)|is','$1'.$strContent.'$2',$Subject);
    }

    return $Subject;
  }

  public static function isActive(rex_extension_point $ep) {

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere(array('id'=>$ep->getParam('slice_id')));
    $sql->select();

    $online_from = $sql->getValue('online_from');
    $online_to = $sql->getValue('online_to');

    if(rex::isBackend() ||
      ($sql->getValue('active') == 1 && (empty($online_from) || (!empty($online_from) && $online_from < time())) && (empty($online_to) || (!empty($online_to) && $online_to >= time())))
    ) return $ep->getSubject();
    return '';
  }

  public static function copySlice($slice_id = false,$clang = false,$module_id = false,$ctype=false,$article_id=false) {
    if(!$slice_id) $slice_id = rex_get('slice_id');
    if(!$article_id) $article_id = rex_get('article_id');
    if(!$clang) $clang = rex_get('clang');
    if(!$module_id) $module_id = rex_get('module_id');
    if(!$ctype) $ctype = rex_get('ctype');

    $_SESSION['slice_ui'] = array('slice_id'=>$slice_id,'article_id'=>$article_id,'clang'=>$clang,'module_id'=>$module_id,'ctype'=>$ctype,'cut'=>(rex_get('page') === 'content/cut'));

    // ----- EXTENSION POINT
    rex_extension::registerPoint(new rex_extension_point('SLICE_COPIED', '', $_SESSION['slice_ui']));

    // Alle OBs schließen
    while (@ob_end_clean());
    header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.$clang.'&ctype='.$ctype);
    exit;
  }

  public static function emptyClipboard($reset=false) {
    $_SESSION['slice_ui'] = ['slice_id'=>null,'article_id'=>null,'clang'=>null,'module_id'=>null,'ctype'=>null,'cut'=>null];

    if($reset)
      return;

    // Alle OBs schließen
    while (@ob_end_clean());
    header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.rex_get('clang').'&ctype='.rex_get('ctype'));
    exit;
  }

  public static function toggleSlice() {
    $slice_id = rex_get('slice_id');
    $article_id = rex_get('article_id');
    $clang = rex_get('clang');
    $module_id = rex_get('module_id');
    $ctype = rex_get('ctype');
    $visible = rex_get('visible');

    $update_slice_status = rex_post('update_slice_status');
    $online_from = rex_post('online_from');
    $online_to = rex_post('online_to');

    $sql = rex_sql::factory();
    // $sql->setDebug();

    if(empty($update_slice_status)) {
      $sql->setQuery("UPDATE ".rex::getTablePrefix().'article_slice'." SET active = CASE WHEN active = 1 THEN 0 ELSE 1 END WHERE id = ?",array($slice_id));
      self::regenerateArticle();

      rex_extension::registerPoint(new rex_extension_point('SLICE_TOGGLED', '', [
        'slice_id' => $slice_id,
        'article_id' => $article_id,
        'clang' => $clang,
        'module_id' => $module_id,
        'ctype' => $ctype,
        'visible' => $visible,
      ]));
    } else {
      $slice_id = rex_post('slice_id');
      if(!empty($online_from)) {
        $online_from = explode('.',$online_from);
        $online_from = mktime(0,0,0,$online_from[1],$online_from[0],$online_from[2]);
      } else $online_from = 0;

      if(!empty($online_to)) {
        $online_to = explode('.',$online_to);
        $online_to = mktime(0,0,0,$online_to[1],$online_to[0],$online_to[2]);
      } else $online_to = 0;

      $sql->setQuery("UPDATE ".rex::getTablePrefix().'article_slice'." SET online_from = ?, online_to = ? WHERE id = ?",array($online_from,$online_to,$slice_id));
      self::regenerateArticle($slice_id,$clang,$module_id,$ctype,$article_id);
    }

    // Alle OBs schließen
    while (@ob_end_clean());
    header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.rex_get('clang').'&ctype='.rex_get('ctype'));
    exit;
  }

  public function moveSlice() {
    $slice_id = rex_get('slice_id');
    $article_id = rex_get('article_id');
    $clang = rex_get('clang');
    $module_id = rex_get('module_id');
    $ctype = rex_get('ctype');
    $prio = rex_get('prio');
    $dir = rex_get('dir');

    $sql = rex_sql::factory();
    $sql->setQuery("UPDATE ".rex::getTablePrefix().'article_slice'." SET priority = ? WHERE id = ?",array($prio,$slice_id));

    $sort = 'DESC';
    if($dir == 1)
      $sort = 'ASC';

    rex_sql_util::organizePriorities(
      rex::getTable('article_slice'),
      'priority',
      'article_id='.$article_id.' AND clang_id='.$clang.' AND ctype_id='.$ctype.' AND revision=0',
      'priority, updatedate '.$sort
    );

    self::regenerateArticle();
    exit();
  }

  public static function addSlice() {

    $article_id = rex_request('article_id', 'int');
    $function   = rex_request('page', 'string');

    $cut_slice_id = $slice_id   = $_SESSION['slice_ui']['slice_id'];
    $module_id  = $_SESSION['slice_ui']['module_id'];
    $clang      = rex_get('clang');
    $ctype      = rex_get('ctype');
    $category_id = rex_get('category_id');

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
        rex_extension::registerPoint(new rex_extension_point('SLICE_PASTED', '', [
          'article_id' => $article_id,
          'clang' => $clang,
          'function' => $function,
          'slice_id' => $slice_id,
          'page' => rex_be_controller::getCurrentPage(),
          'ctype' => $ctype,
          'category_id' => $category_id,
          'module_id' => $module_id,
          'article_revision' => 0,
          'slice_revision' => 0,
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

    if(!empty($AddonPerm['ctypes']))
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

  public function extendSliceButtons() {
    // ----- EXTENSION POINT
    $hideButtons = rex_extension::registerPoint(new rex_extension_point('HIDE_COPY_BUTTONS', '', []));

    if(!$hideButtons && (rex_get('page_buttons') == '' || strpos(rex_get('page_buttons'),__CLASS__) !== false)) {
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
  }

  public static function regenerateArticle($slice_id = false,$clang = false,$module_id = false,$ctype = false,$article_id = false) {
    if(!$slice_id) $slice_id = rex_get('slice_id');
    if(!$article_id) $article_id = rex_get('article_id');
    if(!$clang) $clang = rex_get('clang');
    if(!$module_id) $module_id = rex_get('module_id');
    if(!$ctype) $ctype = rex_get('ctype');

    // ----- artikel neu generieren
    $EA = rex_sql::factory();
    $EA->setTable(rex::getTablePrefix() . 'article');
    $EA->setWhere(['id' => $article_id, 'clang_id' => $clang]);
    $EA->addGlobalUpdateFields();
    $EA->update();
    rex_article_cache::delete($article_id, $clang);

    if (rex_post('btn_save', 'string')) {
      $function = '';
    }
  }
}