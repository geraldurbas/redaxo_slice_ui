<fieldset>
  <legend>JSON-Values</legend>

  <?php $index = 0;foreach('REX_JSON_VALUE[5]' as $key => $data) {?>
  <div class="block" data-json="1">

    <ul class="icons">
      <li class="btn btn-move">
        <i class="rex-icon rex-icon-up"></i>
      </li>
      <li class="btn btn-move">
        <i class="rex-icon rex-icon-down"></i>
      </li>
    </ul>

    <dl class="rex-form-group form-group">
      <dt><label for="json_value_5_<?=$index;?>_title">Titel:</label></dt>
      <dd><input class="form-control" id="json_value_5_<?=$index;?>_title" type="text" name="REX_INPUT_VALUE[5][<?=$index;?>][title]" value="<?=$data['title'];?>" ></dd>
    </dl>
    <dl class="rex-form-group form-group">
      <dt><label for="json_value_5_<?=$index;?>_eins">Eins</label></dt>
      <dd><input class="form-control" id="json_value_5_<?=$index;?>_eins" type="text" name="REX_INPUT_VALUE[5][<?=$index;?>][eins]" value="<?=$data['eins'];?>" ></dd>
    </dl>
    <dl class="rex-form-group form-group">
      <dt><label for="json_value_5_<?=$index;?>_zwei">Zwei</label></dt>
      <dd><input class="form-control" id="json_value_5_<?=$index;?>_zwei" type="text" name="REX_INPUT_VALUE[5][<?=$index;?>][zwei]" value="<?=$data['zwei'];?>" ></dd>
    </dl>
    <dl class="rex-form-group form-group">
      <dt><label for="json_value_5_<?=$index;?>_drei">Drei</label></dt>
      <dd><input class="form-control" id="json_value_5_<?=$index;?>_drei" type="text" name="REX_INPUT_VALUE[5][<?=$index;?>][drei]" value="<?=$data['drei'];?>" ></dd>
    </dl>

    <ul class="icons">
      <li class="btn btn-add btn-move">
        <i class="rex-icon rex-icon-add"></i>
      </li>
      <li class="btn btn-delete" data-confirm="Diesen Block wirklick entfernen?">
        <i class="rex-icon rex-icon-delete"></i>
      </li>
    </ul>
  </div>
  <?php $index++;}?>
</fieldset>