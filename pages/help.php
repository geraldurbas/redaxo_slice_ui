<?php

$fragment = new rex_fragment();
$content = $fragment->parse('page/help.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', $this->i18n('help'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');