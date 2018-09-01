<?php
$menu[0] = $modx->newObject('modMenu');
$menu[0]->fromArray(array(
  'text'        => PKG_NAME,
  'parent'      => 'components',
  'namespace'   => PKG_NAME_LOWER,
  'description' => PKG_NAME_LOWER . '.menu_update_clear_desc',
  'menuindex'   => 0,
), '', true, true);


$menu[1] = $modx->newObject('modMenu');
$menu[1]->fromArray(array(
  'text'        => PKG_NAME_LOWER . '.menu_update',
  'parent'      => PKG_NAME,
  'namespace'   => PKG_NAME_LOWER,
  'description' => PKG_NAME_LOWER . '.menu_update_desc',
  'menuindex'   => 0,
  'handler'     => file_get_contents($sources['data'] . 'transport.menu.update.handler.js'),
), '', true, true);


$menu[2] = $modx->newObject('modMenu');
$menu[2]->fromArray(array(
  'text'        => PKG_NAME_LOWER . '.menu_clear',
  'parent'      => PKG_NAME,
  'namespace'   => PKG_NAME_LOWER,
  'description' => PKG_NAME_LOWER . '.menu_clear_desc',
  'menuindex'   => 1,
  'handler'     => file_get_contents($sources['data'] . 'transport.menu.clear.handler.js'),
), '', true, true);

return $menu;
