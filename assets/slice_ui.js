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

function moveSlice($el,dir) {
  var other = 0,
      appendButton = null;
  $pel = $el.parents('.rex-slice-output');
  appendButton = $pel.prev();

  if(dir == 'up') {
    other = $pel.prevAll('.rex-slice-output').length;
    if(other > 1) {
      $pel.insertBefore($pel.prevAll('.rex-slice-select')[1]);
      appendButton.insertBefore($pel);
    } else if(other == 1) {
      $pel.prependTo($pel.parent());
      appendButton.prependTo($pel.parent());
    }
  } else {
    if($pel.nextAll('.rex-slice-output').length >= 1)
      $pel.insertAfter($pel.nextAll('.rex-slice-output')[0]);
    appendButton.insertBefore($pel);
  }

  $.ajax({
    url: $el.prop('href')
  });
}

$(function(){
  var lastSorted = null,
      startPosition = 0;

  /* add button if javascript is enableb */
  $('.btn-move-up-n-down').removeClass('hide').click(function(e){
    e.preventDefault();
  });

  $('.panel-form input[type="text"]').datepicker({
    dateFormat: 'dd.mm.yy'
  });

  $('.btn-move:has(.rex-icon-down)').click(function(e){
    moveSlice($(this),'down');
    e.preventDefault();
  });
  $('.btn-move:has(.rex-icon-up)').click(function(e){
    moveSlice($(this),'up');
    e.preventDefault();
  });

  $('.rex-slice-output btn.btn-delete').unbind().data('confirm','').click(function(e){
    var parents = $(this).parents('.rex-slice'),
        check = confirm('Block löschen?');
    if(check) {
      parents.prev().animate({height:'toggle',margin:'toggle',opacity:'toggle'},400,function(){
        $(this).remove();
      });
      parents.animate({height:'toggle',margin:'toggle',opacity:'toggle'},400,function(){
        $(this).remove();
      });

      $.ajax({url:this.href});
    }
    e.preventDefault();
    return false;
  });

  $('.btn-visible,.btn-invisible').click(function(e) {
    var state = this.getAttribute('data-state');
    e.preventDefault();

    $.ajax({
      url: this.href,
      data: {state: state}
    });

    if(state == 'visible') {
      $(this).parents('.rex-slice-output').addClass('inactive');
      this.className = 'btn btn-invisible';
      this.setAttribute('data-state','invisible');
      this.getElementsByTagName('i')[0].className = 'rex-icon rex-icon-invisible';
    } else {
      $(this).parents('.rex-slice-output').removeClass('inactive');
      this.className = 'btn btn-visible';
      this.setAttribute('data-state','visible');
      this.getElementsByTagName('i')[0].className = 'rex-icon rex-icon-visible';
    }
  });

  if($.ui !== undefined) {
    /* Remove buttons if javascript is enabled, we don't need'em */
    if($('.btn-move-up-n-down').is('.remove_arrows'))
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