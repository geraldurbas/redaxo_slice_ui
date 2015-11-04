<?php
  $action = $this->getVar('action');
  $slice_id = $this->getVar('slice_id');
  $online_from = $this->getVar('online_from');
  $online_to = $this->getVar('online_to');
  $class = $this->getVar('class');
?><form class="panel-form<?php echo ($class?' '.$class:'');?>"action="<?php echo $action;?>" method="post">
  <fieldset>
    <input type="hidden" name="update_slice_status" value="1">
    <input type="hidden" name="slice_id" value="<?php echo $slice_id;?>">
    
    <div>
      <label for="">Online ab:</label>
      <input type="text" name="online_from" value="<?php echo $online_from?$online_from:'';?>">
    </div>

    <div>
      <label for="">Online bis:</label>
      <input type="text" name="online_to" value="<?php echo $online_to?$online_to:'';?>">
    </div>
    <input type="submit" value="OK">
  </fieldset>
</form>