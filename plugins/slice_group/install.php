<?php

rex_sql_table::get(rex::getTablePrefix().'article_slice')
  ->ensureColumn(new rex_sql_column('group_template', 'int(10)'))
  ->ensureColumn(new rex_sql_column('group_closed', 'int(10)'))
  ->alter();

rex_sql_table::get(rex::getTablePrefix().'slice_groups')
  ->ensureColumn(new rex_sql_column('start_wrapper', 'text'))
  ->ensureColumn(new rex_sql_column('end_wrapper', 'text'))
  ->ensureColumn(new rex_sql_column('option_min', 'int(10)'))
  ->ensureColumn(new rex_sql_column('option_max', 'int(10)'))
  ->alter();