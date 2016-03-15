<?php

/**
 * @class slice_ui
 * @file slice_ui.php
 * @author Sascha Weidner <sascha.weidner@factorylabs.com>
 * @brief Generelle UI-Verbesserung
 */

class slice_ui {

  private $environment = [];

  public static function is_ajax() {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
  }

  public static function extendSliceIconBar(rex_extension_point $ep) {

    if(!rex::getUser()->hasPerm('slice_ui[]'))
      return;

    $Icons = array();
    $Config = rex_config::get('slice_ui');

    if(!empty($Config['general']['copy_n_cut']) && $Config['general']['copy_n_cut'] && rex::getUser()->hasPerm('slice_ui[copy]')) {
      $Icons = array(
        array(
          'hidden_label' => rex_i18n::msg('slice_ui_copy'),
          'url' => 'index.php?page=content/copy&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
          'attributes' => array(
            'class' => array('btn-copy'),
            'title' =>rex_i18n::msg('slice_ui_copy'),
          ),
          'icon' => 'copy'
        ),
        array(
          'hidden_label' => rex_i18n::msg('slice_ui_cut'),
          'url' => 'index.php?page=content/cut&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
          'attributes' => array(
            'class' => array('btn-cut'),
            'title' => rex_i18n::msg('slice_ui_cut'),
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
        $Icons[0]['attributes']['style'] = 'display:none';

      if(!empty($_SESSION['slice_ui']['slice_id']) && $_SESSION['slice_ui']['slice_id'] !== $ep->getParam('slice_id') || ($_SESSION['slice_ui']['slice_id'] === $ep->getParam('slice_id') && $_SESSION['slice_ui']['cut'] !== true)) {
        $Icons[0] = array(
          'hidden_label' => rex_i18n::msg('slice_ui_paste'),
          'url' => 'index.php?page=content/pasteAfter&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
          'attributes' => array(
            'class' => array('btn-paste'),
            'title' => rex_i18n::msg('slice_ui_paste'),
            'data-pjax' => 'true',
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

    if(!empty($Config['general']['slice_status']) && $Config['general']['slice_status'] && rex::getUser()->hasPerm('slice_ui[status]') && rex::getUser()->getComplexPerm('modules')->hasPerm($ep->getParam('module_id'))) {
      $Icons[] = array(
        'hidden_label' => rex_i18n::msg('slice_ui_toggle_'.$mode),
        'url' => 'index.php?page=content/toggleSlice&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype').'&visible='.$sql->getValue('active'),
        'attributes' => array(
          'class' => array('btn-'.$mode),
          'title' => rex_i18n::msg('slice_ui_toggle_'.$mode),
          'data-state' => $mode,
        ),
        'icon' => $mode,
      );
    }

    if(!empty($Config['general']['drag_n_drop']) && $Config['general']['drag_n_drop'] && rex::getUser()->hasPerm('slice_ui[move]')) {
      $Icons[] = array(
        'hidden_label' => rex_i18n::msg('slice_ui_move'),
        'url' => 'index.php?page=content/move&article_id='.$ep->getParam('article_id').'&mode=edit&module_id='.$ep->getParam('module_id').'&slice_id='.$ep->getParam('slice_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype'),
        'attributes' => array(
          'class' => array('btn-move-up-n-down',!empty($Config['general']['keep_move_arrows']) && $Config['general']['keep_move_arrows']?'keep_arrows':'remove_arrows'),
          'title' => rex_i18n::msg('slice_ui_toggle_move'),
          'data-prio' => '',
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

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere(array('id'=>$ep->getParam('slice_id')));
    $sql->select();

    $arrEP = [
      'slice_id' => $ep->getParam('slice_id'),
      'article_id' => $ep->getParam('article_id'),
      'clang' => $ep->getParam('clang'),
      'ctype' => $ep->getParam('ctype'),
      'slice_data' => $sql,
    ];

    if(rex::isBackend() && !empty($Config['online_from_to']) && (in_array($ep->getParam('module_id'),$Config['online_from_to']) || in_array('all',$Config['online_from_to']))) {
      $online_from = $sql->getValue('online_from');
      $online_to = $sql->getValue('online_to');

      $fragment = new rex_fragment();
      $fragment->setVar('online_from', $online_from?date('d.m.Y',$online_from):null, false);
      $fragment->setVar('online_to', $online_to?date('d.m.Y',$online_to):null, false);
      $content = $fragment->parse('status/status.php');
    }

    $strContent = rex_extension::registerPoint(new rex_extension_point('SLICE_UI_EXTEND_SLICE_FORMS', '', array_merge($arrEP,[
      'content' => $Subject
    ])));

    if($strContent)
      $content .= $strContent;

    if($content) {
      $fragment = new rex_fragment();
      $fragment->setVar('action', 'index.php?page=content/status&article_id='.$article_id.'&clang='.$clang.'&ctype='.$ctype, false);
      $fragment->setVar('slice_id', $ep->getParam('slice_id'), false);
      $fragment->setVar('body', $content, false);
      $content = $fragment->parse('status/slice_form.php');
    }

    $strContent = rex_extension::registerPoint(new rex_extension_point('SLICE_UI_ADD_AFTER_SLICE_FORMS', '', array_merge($arrEP,[
      'content' => $Subject
    ])));

    if($strContent)
      $content .= $strContent;
    
    $strContent = '';
    $Subject = str_replace('<div class="panel-body">',$content.'<div class="panel-body">',$Subject);

    $strContent = rex_extension::registerPoint(new rex_extension_point('SLICE_UI_SLICE_FOOTER', '', array_merge($arrEP,[
      'content' => $Subject
    ])));

    if($strContent)
      $content .= $strContent;

    if($strContent) {
      $fragment = new rex_fragment();
      $fragment->setVar('body', $strContent, false);
      $strContent = $fragment->parse('panel/footer.php');
      $Subject = preg_replace('|(<\/div>)([^<]*<\/div>[^<]*<\/section>[^<]*<\/li>$)|is','$1'.$strContent.'$2',$Subject);
    }
    
    $strContent = rex_extension::registerPoint(new rex_extension_point('SLICE_UI_BEFORE_SLICE', '', array_merge($arrEP,[
      'content' => $Subject
    ])));

    if($strContent)
      $Subject = $strContent.$Subject;

    $strContent = rex_extension::registerPoint(new rex_extension_point('SLICE_UI_AFTER_SLICE', '', array_merge($arrEP,[
      'content' => $Subject
    ])));

    if($strContent)
      $Subject .= $strContent;

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
    ) {
      $Subject = $ep->getSubject();
      if($sql->getValue('active') != 1 || (!empty($online_from) && $online_from > time()) || (!empty($online_to) && $online_to <= time())) $Subject = str_replace('rex-slice-output','rex-slice-output inactive',$Subject);
      if(isset($_SESSION['slice_ui']) && $_SESSION['slice_ui']['slice_id'] == $ep->getParam('slice_id')) $Subject = str_replace('rex-slice-output','rex-slice-output copied',$Subject);
      return $Subject;
    }
    return '';
  }

  public static function copySlice($slice_id = false,$clang = false,$module_id = false,$ctype=false,$article_id=false) {
    if(!$slice_id) $slice_id = rex_get('slice_id');
    if(!$article_id) $article_id = rex_get('article_id');
    if(!$clang) $clang = rex_get('clang');
    if(!$module_id) $module_id = rex_get('module_id');
    if(!$ctype) $ctype = rex_get('ctype');

    $_SESSION['slice_ui'] = array('slice_id'=>$slice_id,'article_id'=>$article_id,'clang'=>$clang,'module_id'=>$module_id,'ctype'=>$ctype,'cut'=>(rex_get('page') === 'content/cut'),'new_slice_id'=>null);

    // ----- EXTENSION POINT
    rex_extension::registerPoint(new rex_extension_point('SLICE_UI_SLICE_COPIED', '', $_SESSION['slice_ui']));

    // Alle OBs schließen
    // while (@ob_end_clean());
    // header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.$clang.'&ctype='.$ctype);
    // exit;
  }

  public static function emptyClipboard($reset=false) {
    $_SESSION['slice_ui'] = ['slice_id'=>null,'article_id'=>null,'clang'=>null,'module_id'=>null,'ctype'=>null,'cut'=>null,'new_slice_id'=>null];

    if($reset)
      return;

    // Alle OBs schließen
    // while (@ob_end_clean());
    // header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.rex_get('clang').'&ctype='.rex_get('ctype'));
    // exit;
  }

  /**
   * @brief Slice aktivieren / deaktivieren.
   * Benötigt URL-Parameter slice_id, article_id, clang, module_id, ctype und visible
   * @Post-Parameter update_slice_status = true || NULL; NULL würde den Filter löschen.
   * @Post-Paramerter online_from UNIX_TIMESTAMP
   * @Post-Paramerter online_to UNIX_TIMESTAMP
   */
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

      rex_extension::registerPoint(new rex_extension_point('SLICE_UI_SLICE_TOGGLED', '', [
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
    // while (@ob_end_clean());
    // header("Location: ".rex_url::backendController().'?page=content/edit&article_id='.rex_get('article_id').'&clang='.rex_get('clang').'&ctype='.rex_get('ctype'));
    // exit;
  }

  /**
   * @brief Slice an eine neue Stelle bewegen.
   */
  public function moveSlice() {
    $prio = rex_get('prio');
    $slice_id = rex_get('slice_id');
    $dir = rex_get('dir');
    $article_id = rex_get('article_id');
    $clang = rex_get('clang');
    $ctype = rex_get('ctype');

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
      $priority = '0';
      // $prevSlice->setDebug();
      if ($function === 'content/paste') {
        $priority = 1;
      } else {
        $prevSlice = rex_sql::factory();
        $prevSlice->setTable($sliceTable);
        $prevSlice->setWhere(array('id'=>rex_get('slice_id')));
        $prevSlice->select();
        $priority = $prevSlice->getValue('priority')+1;
      }

      $copiedSlice = rex_sql::factory();
      $copiedSlice->setTable($sliceTable);
      $copiedSlice->setWhere(array('id'=>$cut_slice_id));
      $copiedSlice->select();

      $exclude = array('id','createdate','updatedate','createuser','updateuser','priority');

      // print_r($copiedSlice->getRow());
      foreach($copiedSlice->getRow() as $key => $value) {
        if(empty($value)) continue;
        $field = end((explode('.',$key)));
        if(in_array($field,$exclude)) continue;
        $newsql->setValue($field,$value);
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
          $_SESSION['slice_ui']['new_slice_id'] = $slice_id;


        rex_sql_util::organizePriorities(
          rex::getTable('article_slice'),
          'priority',
          'article_id='.$article_id.' AND clang_id='.$clang.' AND ctype_id='.$ctype.' AND revision='.$slice_revision,
          'priority, updatedate DESC'
        );

        rex_article_cache::deleteContent($article_id, $clang);

        $function = '';

        // ----- EXTENSION POINT
        rex_extension::registerPoint(new rex_extension_point('SLICE_UI_SLICE_PASTED', '', [
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
        echo rex_view::warning($e->getMessage());
      }
      // die();

      // Alle OBs schließen
      while (@ob_end_clean());
      header("Location: ".rex_url::backendController().'?article_id='.$article_id.'&clang='.$clang.'&page=content/edit&ctype='.$ctype);
      exit;
    }
  }

  /* Einfügen + Clipboard löschen */
  public static function extendSliceButtons() {
    // ----- EXTENSION POINT
    $hideButtons = rex_extension::registerPoint(new rex_extension_point('SLICE_UI_HIDE_COPY_BUTTONS', '', []));

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
    } elseif (!(rex::getUser()->isAdmin() || rex::getUser()->getComplexPerm('modules')->hasPerm($ep['module_id']))) {
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
