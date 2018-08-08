<?php
$menu = $modx->newObject('modMenu');
$menu->fromArray(array(
  'text'        => PKG_NAME,
  'parent'      => 'refresh_site',
  'namespace'   => PKG_NAME_LOWER,
  'description' => PKG_NAME_LOWER . '.menu_desc',
  'menuindex'   => 0,
  'handler'     => file_get_contents($sources['data'] . 'transport.menu.handler.js'),
), '', true, true);

return $menu;
