REDAXO Factory Design Labs Theme

<code>
  <fieldset>
  <legend>JSON-Values</legend>

  &lt;?php $index = 0;foreach('REX_JSON_VALUE[5]' as $key => $data) {?&gt;
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
      <dt><label for="json_value_5_&lt;?=$index;?&gt;_title">Titel:</label></dt>
      <dd><input class="form-control" id="json_value_5_&lt;?=$index;?&gt;_title" type="text" name="REX_INPUT_VALUE[5][&lt;?=$index;?&gt;][title]" value="&lt;?=$data['title'];?&gt;" ></dd>
    </dl>
    <dl class="rex-form-group form-group">
      <dt><label for="json_value_5_&lt;?=$index;?&gt;_eins">Eins</label></dt>
      <dd><input class="form-control" id="json_value_5_&lt;?=$index;?&gt;_eins" type="text" name="REX_INPUT_VALUE[5][&lt;?=$index;?&gt;][eins]" value="&lt;?=$data['eins'];?&gt;" ></dd>
    </dl>
    <dl class="rex-form-group form-group">
      <dt><label for="json_value_5_&lt;?=$index;?&gt;_zwei">Zwei</label></dt>
      <dd><input class="form-control" id="json_value_5_&lt;?=$index;?&gt;_zwei" type="text" name="REX_INPUT_VALUE[5][&lt;?=$index;?&gt;][zwei]" value="&lt;?=$data['zwei'];?&gt;" ></dd>
    </dl>
    <dl class="rex-form-group form-group">
      <dt><label for="json_value_5_&lt;?=$index;?&gt;_drei">Drei</label></dt>
      <dd><input class="form-control" id="json_value_5_&lt;?=$index;?&gt;_drei" type="text" name="REX_INPUT_VALUE[5][&lt;?=$index;?&gt;][drei]" value="&lt;?=$data['drei'];?&gt;" ></dd>
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
  &lt;?php $index++;}?&gt;
</fieldset>
</code>