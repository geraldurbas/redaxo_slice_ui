<?php

$fragment = new rex_fragment();
$content = $fragment->parse('slice_group.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', $this->i18n('slice_group_templates'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');