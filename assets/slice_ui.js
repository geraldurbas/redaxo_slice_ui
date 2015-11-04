(function($){

if($.browser === undefined) {
  $.browser = {};
  (function () {
      jQuery.browser.msie = false;
      jQuery.browser.version = 0;
      if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
          jQuery.browser.msie = true;
          jQuery.browser.version = RegExp.$1;
      }
  })();
}

$(function(){
  var lastSorted = null,
      startPosition = 0;

  /* add button if javascript is enableb */
  $('.btn-move-up-n-down').removeClass('hide').click(function(e){
    e.preventDefault();
  });

  if($.ui !== undefined) {
    /* Remove buttons if javascript is enabled, we don't need'em */
    $('.rex-icon-up,.rex-icon-down').parents('.btn-group').remove();
    $('.rex-main-content > .rex-slices').sortable({
      axis: 'y',
      scroll: true,
      handle: ".btn-move-up-n-down",
      start: function(event,ui) {
        startPosition = (((ui.item.index()-1)/2)+1);
        console.log(startPosition);
        $('.rex-page-main').addClass('noSelect');
        lastSorted = ui.item.prev();
        $('.rex-slice-select').stop().animate({opacity:0});
      },
      update: function(event,ui) {
        if(ui.item.prev().is('.rex-slice-select'))
          ui.item.insertBefore(ui.item.prev());
      },
      stop: function(event,ui) {
        var endPosition = 0,
            dir = 0;
        $('.rex-page-main').removeClass('noSelect');
        lastSorted.insertBefore(ui.item);
        $('.rex-slice-select').stop().animate({opacity:1});

        endPosition = (((ui.item.index()-1)/2)+1);

        if(endPosition !== startPosition) {
          if(endPosition > startPosition) {
            dir = 0;
            endPosition++;
          } else if(endPosition < startPosition) {
            dir = 1;
            endPosition--;
          }

          $.ajax({
            url: ui.item.find('.btn-move-up-n-down').prop('href'),
            data: {dir:dir,prio:endPosition},
          });
        }
      },
    });
  }
});})(jQuery);