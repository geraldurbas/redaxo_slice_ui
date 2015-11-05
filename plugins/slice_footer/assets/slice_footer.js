(function($){

function deleteSlices(el,url) {
  var IDs = [];
  el.each(function(key){
    var parents = $(this).parents('.rex-slice');
    IDs[key] = this.value;
    parents.prev().animate({height:'toggle',margin:'toggle',opacity:'toggle'},400,function(){
      $(this).remove();
    });
    parents.animate({height:'toggle',margin:'toggle',opacity:'toggle'},400,function(){
      $(this).remove();
    });
  });

  $.ajax({
    url: url,
    data: {slices:IDs},
  });
}
$(function(){
  var $buttons = $('.slice_footer button');

  $buttons.click(function(){
    var $button = $(this),
        IDs = [],
        confirmation = false;
    if($button.is('.delete-all')) {
      confirmation = confirm("Wirklich alle Slices löschen?");
      if(confirmation) {
        deleteSlices($('.rex-slices').find('.sliceset.delete'),$button.data('href'));
      }
    }
    if($button.is('.delete-choosen') && $('.rex-slices').find('.sliceset.delete:checked').length > 0) {
      confirmation = confirm("Wirklich alle ausgewählten Slices löschen?");
      if(confirmation) {
        deleteSlices($('.rex-slices').find('.sliceset.delete:checked'),$button.data('href'));
      }
    }
  });
});})(jQuery);