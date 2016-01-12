<?php

slice_ui::emptyClipboard(1);

rex_sql_table::get(rex::getTablePrefix().'article_slice')
  ->ensureColumn(new rex_sql_column('active', 'char(1)'))
  ->ensureColumn(new rex_sql_column('online_from', 'int(10)'))
  ->ensureColumn(new rex_sql_column('online_to', 'int(10)'))
  ->alter();
