(function($){

function updateIndex(newBlock,replace,step) {
  var concat = arguments[3]||false;
  newBlock.find('label,input,textarea,select').each(function(){
    for(var a=0;a<this.attributes.length;a++) {
      var attr = this.attributes[a].name,
          regex = new RegExp(attr=='name'?"\\[(["+replace+"]{"+replace.length+"})(?!\\d|\\]\\["+replace+")":"_(["+replace+"]{"+replace.length+"})(?!\\d|_"+replace+")"),
          matched = this.getAttribute(attr).match(regex);

      if(matched !== null)
        this.setAttribute(attr,this.getAttribute(attr).replace(regex,(attr=='name'?'[':'_')+(parseInt(replace,10)+step)));
    }
  });

  if(newBlock.next('[data-json]').length > 0 && !concat)
    updateIndex(newBlock.next('[data-json]'),(parseInt(replace,10)+Math.abs(step)).toString(),step);
}
$(function(){
  var $json = $('fieldset [data-json]');

  $json.each(function(){
    var json = $(this);

    json.find('.btn-move:has(.rex-icon-up)').click(function(){
      var parent = $(this).parents('[data-json]'),
          replace = parent.index()-1;

      if(replace <= 0)
        return;

      updateIndex(parent.prev(),(parent.index()-2).toString(),1,1);
      parent.insertBefore(parent.prev());
      updateIndex(parent,replace.toString(),-1,1);
    });

    json.find('.btn-move:has(.rex-icon-down)').click(function(){
      var parent = $(this).parents('[data-json]'),
          replace = parent.index()-1;

      if($json.length-1 === replace)
        return;

      updateIndex(parent.next(),(parent.index()).toString(),-1,1);
      parent.insertAfter(parent.next());
      updateIndex(parent,replace.toString(),1,1);
    });

    json.find('.btn-add').click(function(){
      var parent = $(this).parents('[data-json]'),
          newBlock = parent.clone(1).insertAfter(parent),
          replace = parent.index() - 1;

      updateIndex(newBlock,replace.toString(),1);
      $json = $('fieldset [data-json]');
    });

    json.find('.btn-delete').click(function(){
      var obj = $(this).parents('[data-json]')[0],
          parent = $(this).parents('[data-json]'),
          check = true;

      if($(this).data('confirm') != '')
        check = confirm($(this).data('confirm'));

      if(!check) return;

      if($json.length > 1) {
        updateIndex(parent.next(),parent.index().toString(),-1);
        parent.remove();
      } else {
        var tags = ['input','select','textarea'];

        for(var t=0;t<tags.length;t++) {
          var Tag = obj.getElementsByTagName(tags[t]);
          for(var T=0;T<Tag.length;T++) {
            switch(Tag[T].tagName.toLowerCase()) {
              case 'textarea':
                Tag[T].value = '';
              break;
              case 'input':
                switch(Tag[T].type) {
                  case 'checkbox':
                  case 'radio':
                    Tag[T].checked = false;
                  break;
                  default:
                    Tag[T].value = '';
                  break;
                }
              break;
              case 'select':
                var options = Tag[T].getElementsByTagName('option');
                for(var o=0;o<options.length;o++) {
                  options[o].selected = false;
                }
              break;
            }
          }
        }
      }

      $json = $('fieldset [data-json]');
    });
  });

});})(jQuery);