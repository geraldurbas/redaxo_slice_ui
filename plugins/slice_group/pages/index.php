<?php

/**
 * @package redaxo5
 */

$OUT = true;

$function = rex_request('function', 'string');
$function_action = rex_request('function_action', 'string');
$save = rex_request('save', 'string');
$template_id = rex_request('template_id', 'int');
$action_id = rex_request('action_id', 'int');
$iaction_id = rex_request('iaction_id', 'int'); // id der module-action relation
$mname = rex_request('mname', 'string');
$eingabe = rex_request('eingabe', 'string');
$ausgabe = rex_request('ausgabe', 'string');
$goon = rex_request('goon', 'string');
$add_action = rex_request('add_action', 'string');

$success = '';
$error = '';

$content = '';
$message = '';


// ---------------------------- FUNKTIONEN FUER MODULE

if ($function == 'delete') {
    $del = rex_sql::factory();
    $del->setQuery('SELECT ' . rex::getTablePrefix() . 'article_slice.article_id, ' . rex::getTablePrefix() . 'article_slice.clang_id, ' . rex::getTablePrefix() . 'article_slice.ctype_id, ' . rex::getTablePrefix() . 'slice_groups.name FROM ' . rex::getTablePrefix() . 'article_slice
            LEFT JOIN ' . rex::getTablePrefix() . 'slice_groups ON ' . rex::getTablePrefix() . 'article_slice.group_template=' . rex::getTablePrefix() . 'slice_groups.id
            WHERE ' . rex::getTablePrefix() . "article_slice.group_template='$template_id' GROUP BY " . rex::getTablePrefix() . 'article_slice.article_id');

    if ($del->getRows() > 0) {
        $module_in_use_message = '';
        $modulname = htmlspecialchars($del->getValue(rex::getTablePrefix() . 'slice_groups.name'));
        for ($i = 0; $i < $del->getRows(); ++$i) {
            $aid = $del->getValue(rex::getTablePrefix() . 'article_slice.article_id');
            $clang_id = $del->getValue(rex::getTablePrefix() . 'article_slice.clang_id');
            $ctype = $del->getValue(rex::getTablePrefix() . 'article_slice.ctype_id');
            $OOArt = rex_article::get($aid, $clang_id);

            $label = $OOArt->getName() . ' [' . $aid . ']';
            if (rex_clang::count() > 1) {
                $label = '(' . rex_i18n::translate(rex_clang::get($clang_id)->getName()) . ') ' . $label;
            }

            $module_in_use_message .= '<li><a href="' . rex_url::backendPage('content', ['article_id' => $aid, 'clang' => $clang_id, 'ctype' => $ctype]) . '">' . htmlspecialchars($label) . '</a></li>';
            $del->next();
        }

        $error = rex_i18n::msg('slice_group_template_cannot_be_deleted', $modulname);

        if ($module_in_use_message != '') {
            $error .= '<ul>' . $module_in_use_message . '</ul>';
        }
    } else {
        $del->setQuery('DELETE FROM ' . rex::getTablePrefix() . "slice_groups WHERE id='$template_id'");

        if ($del->getRows() > 0) {
            $success = rex_i18n::msg('slice_group_template_deleted');
        } else {
            $error = rex_i18n::msg('slice_group_template_not_found');
        }
    }
}

if ($function == 'add' or $function == 'edit') {
    if ($save == '1') {
        $module = rex_sql::factory();

        try {
            if ($function == 'add') {
                $IMOD = rex_sql::factory();
                $IMOD->setTable(rex::getTablePrefix() . 'slice_groups');
                $IMOD->setValue('name', $mname);
                $IMOD->setValue('output', $ausgabe);
                $IMOD->addGlobalCreateFields();

                $IMOD->insert();
                $success = rex_i18n::msg('module_added');
            } else {
                $module->setQuery('select * from ' . rex::getTablePrefix() . 'slice_groups where id=' . $template_id);
                if ($module->getRows() == 1) {
                    $old_ausgabe = $module->getValue('output');

                    // $module->setQuery("UPDATE ".rex::getTablePrefix()."module SET name='$mname', eingabe='$eingabe', ausgabe='$ausgabe' WHERE id='$template_id'");

                    $UMOD = rex_sql::factory();
                    $UMOD->setTable(rex::getTablePrefix() . 'slice_groups');
                    $UMOD->setWhere(['id' => $template_id]);
                    $UMOD->setValue('name', $mname);
                    $UMOD->setValue('output', $ausgabe);
                    $UMOD->addGlobalUpdateFields();

                    $UMOD->update();
                    $success = rex_i18n::msg('module_updated') . ' | ' . rex_i18n::msg('articel_updated');

                    $new_ausgabe = $ausgabe;

                    if ($old_ausgabe != $new_ausgabe) {
                        // article updaten - nur wenn ausgabe sich veraendert hat
                        $gc = rex_sql::factory();
                        $gc->setQuery('SELECT DISTINCT(' . rex::getTablePrefix() . 'article.id) FROM ' . rex::getTablePrefix() . 'article
                                LEFT JOIN ' . rex::getTablePrefix() . 'article_slice ON ' . rex::getTablePrefix() . 'article.id=' . rex::getTablePrefix() . 'article_slice.article_id
                                WHERE ' . rex::getTablePrefix() . "article_slice.group_template='$template_id'");
                        for ($i = 0; $i < $gc->getRows(); ++$i) {
                            rex_article_cache::delete($gc->getValue(rex::getTablePrefix() . 'article.id'));
                            $gc->next();
                        }
                    }
                }
            }
        } catch (rex_sql_exception $e) {
            $error = $e->getMessage();
        }

        if ($goon != '') {
            $save = '0';
        } else {
            $function = '';
        }
    }

    if ($save != '1') {
        if ($function == 'edit') {
            $legend = rex_i18n::msg('module_edit') . ' <small class="rex-primary-id">' . rex_i18n::msg('id') . '=' . $template_id . '</small>';

            $hole = rex_sql::factory();
            $hole->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'slice_groups WHERE id=' . $template_id);
            $mname = $hole->getValue('name');
            $ausgabe = $hole->getValue('output');
            $eingabe = $hole->getValue('input');
        } else {
            $legend = rex_i18n::msg('create_module');
        }

        $btn_update = '';
        if ($function != 'add') {
            $btn_update = '<button class="btn btn-apply" type="submit" name="goon" value="1"' . rex::getAccesskey(rex_i18n::msg('save_module_and_continue'), 'apply') . '>' . rex_i18n::msg('save_module_and_continue') . '</button>';
        }

        if ($success != '') {
            $message .= rex_view::success($success);
        }

        if ($error != '') {
            $message .= rex_view::error($error);
        }

        $echo = '';
        $content = '';
        $panel = '';
        $panel  .= '
                <fieldset>
                        <input type="hidden" name="function" value="' . $function . '" />
                        <input type="hidden" name="save" value="1" />
                        <input type="hidden" name="category_id" value="0" />
                        <input type="hidden" name="template_id" value="' . $template_id . '" />';

        $formElements = [];

        $n = [];
        $n['label'] = '<label for="mname">' . rex_i18n::msg('module_name') . '</label>';
        $n['field'] = '<input class="form-control" id="mname" type="text" name="mname" value="' . htmlspecialchars($mname) . '" />';
        $formElements[] = $n;

        $n = [];
        $n['label'] = '<label for="moutput">' . rex_i18n::msg('output') . '</label>';
        $n['field'] = '<textarea class="form-control rex-code codemirror" und codemirror-mode="php/htmlmixed" id="moutput" name="ausgabe">' . htmlspecialchars($ausgabe) . '</textarea>';
        $formElements[] = $n;

        $fragment = new rex_fragment();
        $fragment->setVar('flush', true);
        $fragment->setVar('elements', $formElements, false);
        $panel .= $fragment->parse('core/form/form.php');

        $panel .= '</fieldset>';

        $formElements = [];

        $n = [];
        $n['field'] = '<a class="btn btn-abort" href="' . rex_url::currentBackendPage() . '">' . rex_i18n::msg('form_abort') . '</a>';
        $formElements[] = $n;

        $n = [];
        $n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit"' . rex::getAccesskey(rex_i18n::msg('save_module_and_quit'), 'save') . '>' . rex_i18n::msg('save_module_and_quit') . '</button>';
        $formElements[] = $n;

        if ($btn_update != '') {
            $n = [];
            $n['field'] = $btn_update;
            $formElements[] = $n;
        }

        $fragment = new rex_fragment();
        $fragment->setVar('elements', $formElements, false);
        $buttons = $fragment->parse('core/form/submit.php');

        $fragment = new rex_fragment();
        $fragment->setVar('class', 'edit', false);
        $fragment->setVar('title', $legend, false);
        $fragment->setVar('body', $panel, false);
        $fragment->setVar('buttons', $buttons, false);
        $content .= $fragment->parse('core/page/section.php');

        if ($function == 'edit') {
            // Im Edit Mode Aktionen bearbeiten

            $gaa = rex_sql::factory();
            $gaa->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'action ORDER BY name');

            if ($gaa->getRows() > 0) {

                $formElements = [];

                $n = [];
                $n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" value="1" name="add_action">' . rex_i18n::msg('action_add') . '</button>';
                $formElements[] = $n;

                $fragment = new rex_fragment();
                $fragment->setVar('elements', $formElements, false);
                $buttons = $fragment->parse('core/form/submit.php');

                $fragment = new rex_fragment();
                $fragment->setVar('title', rex_i18n::msg('action_add'), false);
                $fragment->setVar('body', $panel, false);
                $fragment->setVar('buttons', $buttons, false);
                $content .= $fragment->parse('core/page/section.php');
            }
        }

        $content = '
            <form action="' . rex_url::currentBackendPage() . '" method="post">
            ' . $content . '
            </form>';

        echo $message;

        echo $content;

        $OUT = false;
    }
}

if ($OUT) {
    if ($success != '') {
        $message .= rex_view::success($success);
    }

    if ($error != '') {
        $message .= rex_view::error($error);
    }

    $list = rex_list::factory('SELECT id, name FROM ' . rex::getTablePrefix() . 'slice_groups ORDER BY name');
    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-module"></i>';
    $thIcon = '<a href="' . $list->getUrl(['function' => 'add']) . '"' . rex::getAccesskey(rex_i18n::msg('create_module'), 'add') . ' title="' . rex_i18n::msg('create_module') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['function' => 'edit', 'template_id' => '###id###']);

    $list->setColumnLabel('id', rex_i18n::msg('id'));
    $list->setColumnLayout('id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id" data-title="' . rex_i18n::msg('id') . '">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('slice_group_templates_description'));
    $list->setColumnParams('name', ['function' => 'edit', 'template_id' => '###id###']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['function' => 'edit', 'template_id' => '###id###']);

    $list->addColumn(rex_i18n::msg('slice_group_delete'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
    $list->setColumnLayout(rex_i18n::msg('slice_group_delete'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('slice_group_delete'), ['function' => 'delete', 'template_id' => '###id###']);
    $list->addLinkAttribute(rex_i18n::msg('slice_group_delete'), 'data-confirm', rex_i18n::msg('slice_group_confirm_delete'));

    $list->setNoRowsMessage(rex_i18n::msg('templates_not_found'));

    $content .= $list->get();

    echo $message;

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('slice_group_templates_caption'), false);
    $fragment->setVar('content', $content, false);
    echo $fragment->parse('core/page/section.php');
}
