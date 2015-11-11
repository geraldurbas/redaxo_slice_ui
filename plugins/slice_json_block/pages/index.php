<?php

$fragment = new rex_fragment();
$content = $fragment->parse('json_help.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', $this->i18n('slice_json_block_json_help'), false);
$fragment->setVar('body', $content.'<code>'.highlight_file('json_example.php',1).'</code>', false);
echo $fragment->parse('core/page/section.php');
