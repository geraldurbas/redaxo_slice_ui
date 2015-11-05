<?php
  $action = $this->getVar('action');
  $slice_id = $this->getVar('slice_id');
  $class = $this->getVar('class');
?><form class="panel-form<?php echo ($class?' '.$class:'');?>"action="<?php echo $action;?>" method="post">
  <fieldset>
    <input type="hidden" name="update_slice_status" value="1">
    <input type="hidden" name="slice_id" value="<?php echo $slice_id;?>">
    
    <?php echo $this->getVar('body');?>
    <input type="submit" value="OK">
  </fieldset>
</form>