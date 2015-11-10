<?php

rex_extension::register('BE_STYLE_LESS_FILES', function (rex_extension_point $ep) {
    $subject = $ep->getSubject();
    array_unshift($subject, rex_plugin::get('be_style', 'slice_skin')->getPath('less/styles.less|sourceMap|compress'));
    
    return $subject;
}, rex_extension::EARLY);

rex_view::addJsFile($this->getAssetsUrl('slice_skin.js'));