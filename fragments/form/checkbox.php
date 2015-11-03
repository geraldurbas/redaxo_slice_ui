<?php
$value = $this->getVar('value');
$classes = $this->getVar('classes');
$label = $this->getVar('label');
$name = $this->getVar('name');
?><dl class="rex-form-group form-group">
  <dd>
    <div class="checkbox">
      <label class="control-label" for="<?php echo rex_string::normalize($name,'');?>">
        <input type="checkbox" name="<?php echo $this->getVar('name');?>" value="<?php echo ($value?$value:1);?>" id="<?php echo rex_string::normalize($name,'');?>"<?php echo ($this->getVar('checked')?' checked="checked"':'');?>>
        <?php echo $label;?>
      </label>
      <?php if(($fields = $this->getVar('toggleFields'))) {?>
      <script type="text/javascript">
        jQuery(function($) {
          $("#<?php echo rex_string::normalize($name,'');?>").click(function() {
            $("<?php echo str_replace('.','.group_',$fields);?>").slideToggle("slow");
          });
          if($("#<?php echo rex_string::normalize($name,'');?>").is(":checked"))
            $("<?php echo str_replace('.','.group_',$fields);?>").hide();
        });
        </script>
      <?php }?>
    </div>
  </dd>
</dl>