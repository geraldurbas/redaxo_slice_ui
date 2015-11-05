<?php 
  $online_from = $this->getVar('online_from');
  $online_to = $this->getVar('online_to');
?><div>
  <label for="">Online ab:</label>
  <input type="text" name="online_from" value="<?php echo $online_from?$online_from:'';?>">
</div>
<div>
  <label for="">Online bis:</label>
  <input type="text" name="online_to" value="<?php echo $online_to?$online_to:'';?>">
</div>