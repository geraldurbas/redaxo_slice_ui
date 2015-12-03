<?php

class slice_group {
  public static function addGroupForm(rex_extension_point $ep) {
    $Slice = $ep->getParam('slice_data');
    $GroupTemplate = $Slice->getValue('group_template');
    $GroupClosed = $Slice->getValue('group_closed');

    $Subject = $ep->getSubject();
    $Subject .= '<form data-pjax="true" method="post" action="'.rex_url::currentBackendPage().'&category_id='.rex_get('category_id').'&article_id='.$ep->getParam('article_id').'&clang='.$ep->getParam('clang').'&ctype='.$ep->getParam('ctype').'&mode='.rex_get('mode').'">';
      $Subject .= '<input type="hidden" name="change_group_template" value="1">';
      $Subject .= '<input type="hidden" name="slice_id" value="'.$ep->getParam('slice_id').'">';
      $Subject .= '<label><input onchange="this.form.submit();" type="checkbox" name="group_closed" value="1"'.($GroupClosed?' checked="checked"':'').'> Gruppe nach diesem Slice schließen?</label><br>';
      $Subject .= '<select onchange="this.form.submit();" name="group_template" size="1" id="slice_group_template">';
        $Subject .= '<option>Gruppe starten?</option>';
        $Subject .= '<option value="1"'.($GroupTemplate == 1?'selected="selected"':'').'>Eins </option>';
        $Subject .= '<option value="2"'.($GroupTemplate == 2?'selected="selected"':'').'>Zwei</option>';
      $Subject .= '</select>';
    $Subject .= '</form>';

    return $Subject;
  }

  public static function setupGroup(rex_extension_point $ep) {
    $Subject = $ep->getSubject();

    if($ep->getParam('slice_data')->getValue('group_template') != '0')
      $Subject .= '<li class="rex-slice rex-slice-group"><ul>';

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

    $open = $closed = 0;

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'article_slice');
    $sql->setWhere(array('article_id'=>$ep->getParam('article_id')));
    $sql->select();

    if(!$sql->getRows()) return $Subject;

    while($sql->hasNext()) {
      if($sql->getValue('group_template'))
        $open++;
      if($sql->getValue('group_closed'))
        $closed++;
      $sql->next();
    }

    if($open > $closed)
      for($c=0;$c<$open-$closed;$c++)
        $Subject .= "</ul></li>\n";

    return $Subject;
  }
}