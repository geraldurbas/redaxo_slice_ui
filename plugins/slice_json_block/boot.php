<?php

rex_extension::register('BE_STYLE_LESS_FILES', function (rex_extension_point $ep) {
    $subject = $ep->getSubject();
    array_unshift($subject, rex_plugin::get('slice_ui', 'slice_json_block')->getPath('less/styles.less|sourceMap|compress'));
    return $subject;
}, rex_extension::EARLY);

if(!rex_plugin::exists('be_style','lessphp')) {
  rex_view::addCssFile($this->getPath().'less/styles.min.css');
}

rex_view::addJsFile($this->getAssetsUrl('slice_skin.js'));