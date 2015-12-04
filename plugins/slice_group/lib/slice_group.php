<?php

class slice_group {
  public static function addGroupForm(rex_extension_point $ep) {
    $Slice = $ep->getParam('slice_data');
    $GroupTemplate = $Slice->getValue('group_template');
    $GroupClosed = $Slice->getValue('group_closed');

    $Groups = rex_sql::factory();
    $Groups->setTable(rex::getTablePrefix().'slice_groups');
    $Groups->select();

    $Subject = $ep->getSubject();

    if($Groups->getRows()) {
      $Subject .= '<form data-pjax="true" method="post" action="'.rex_url::currentBackendPage().'&category_id='.rex_get('category_id').'&article_id='.$ep->getParam('article_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype').'&mode='.rex_get('mode').'">';
        $Subject .= '<input type="hidden" name="change_group_template" value="1">';
        $Subject .= '<input type="hidden" name="slice_id" value="'.$ep->getParam('slice_id').'">';
        if($Slice->getValue('group_template') == 0) $Subject .= '<label><input onchange="this.form.submit();" type="checkbox" name="group_closed" value="1"'.($GroupClosed?' checked="checked"':'').'> Gruppe nach diesem Slice schließen?</label><br>';
        $Subject .= '<select onchange="this.form.submit();" name="group_template" size="1" id="slice_group_template">';
          $Subject .= '<option>Gruppe starten?</option>';
          while($Groups->hasNext()) {
            $Subject .= '<option value="'.$Groups->getValue('id').'"'.($GroupTemplate == $Groups->getValue('id')?' selected="selected"':'').'>'.$Groups->getValue('name').'</option>';
            $Groups->next();
          }
        $Subject .= '</select>';
      $Subject .= '</form>';
    }

    return $Subject;
  }

  public static function setupGroup(rex_extension_point $ep) {
    $Subject = $ep->getSubject();

    if(($GroupTemplate = $ep->getParam('slice_data')->getValue('group_template')) != '0') {

      $Groups = rex_sql::factory();
      $Groups->setTable(rex::getTablePrefix().'slice_groups');
      $Groups->setWhere(array('id'=>$GroupTemplate));
      $Groups->select();

      $Subject .= '<li class="rex-slice rex-slice-group"><h3>'.rex_i18n::msg('slice_group_group').' '.$Groups->getValue('name').'</h3><ul class="rex-slices">';
    }

    return $Subject;
  }

  public static function closeGroup(rex_extension_point $ep) {
    $Subject = $ep->getSubject();

    $Slice = $ep->getParam('slice_data');
    $Prio = $Slice->getValue('priority');

    if($ep->getParam('slice_data')->getValue('group_closed') != '0') {
      $sql = rex_sql::factory();
      $sql->setTable(rex::getTablePrefix().'article_slice');
      $sql->setWhere('article_id = '.$ep->getParam('article_id').' AND priority < '.$Prio.' AND group_template != 0');
      $sql->select();

      if($sql->getRows()) {
        $Subject .= '</ul></li>';
      }
    }

    return $Subject;
  }

  public static function closeGroups(rex_extension_point $ep) {
    $Subject = $ep->getSubject();

    if(($groups = self::getOpenGroups($ep->getParam('article_id'))))
      for($c=0;$c<$groups;$c++)
        $Subject .= "</ul></li>\n";

    return $Subject;
  }

  public static function fronendGroups(rex_extension_point $ep) {
    $Subject = $ep->getSubject();
    $Slice = $ep->getParam('slice_data');
    $isLast = $Slice->getRows() === ($Slice->key()+1);
    $article_id = $ep->getParam($article_id);
    $Plugin = rex_plugin::get('slice_ui','slice_group');

    $Config = $Plugin->getProperty('groups');

    if(empty($Config[$article_id]))
      $Config[$article_id] = [];

    $Config = $Config[$article_id];

    $Template = [];

    if(($GroupTemplate = $Slice->getValue('group_template')) != '0') {

      $Groups = rex_sql::factory();
      $Groups->setTable(rex::getTablePrefix().'slice_groups');
      $Groups->setWhere(array('id'=>$GroupTemplate));
      $Groups->select();


      $Template = $Groups->getArray()[0];
      if(!empty($Template['output']))
        $Subject = str_replace('%CONTENT%',$Subject,$Template['output'])."\n";

      $Subject = $Template['start_wrapper'].$Subject;
      $Config[] = json_encode($Template);
    }

    if(empty($Template)) {
      $Template = json_decode(end($Config),true);
      if(!empty($Template['output']))
        $Subject = str_replace('%CONTENT%',$Subject,$Template['output']);
    }


    if($Slice->getValue('group_closed') == 1) {
      $Subject .= "\n".$Template['end_wrapper']."\n";
      array_pop($Config); 
    }

    $Plugin->setProperty('groups',[$article_id=>$Config]);

    if($isLast) {
      if(($groups = count($Config)))
        for($c=$groups;$c>0;$c++) {
          $Subject .= "\n".$Config[$c]['end_wrapper']."\n";
        }
    }

    return $Subject;
  }

  public static function getOpenGroups($article_id) {
    $open = $closed = 0;

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere(array('article_id'=>$article_id));
    $sql->select();

    if(!$sql->getRows()) return $Subject;

    while($sql->hasNext()) {
      if($sql->getValue('group_template'))
        $open++;
      if($sql->getValue('group_closed'))
        $closed++;
      $sql->next();
    }

    return ($open > $closed);
  }
}