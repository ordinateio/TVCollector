<?php
/**
 * Build script for TVCollector.
 *
 * @package TVCollector
 * @author Callisto https://github.com/callisto2410
 * @source https://github.com/callisto2410/TVCollector
 */

$menu = array();

$menu[0] = $modx->newObject('modMenu');
$menu[0]->fromArray(array(
    'text' => PKG_NAME,
    'parent' => 'components',
    'namespace' => PKG_NAME_LOWER,
    'description' => PKG_NAME_LOWER . '.menu_description',
    'menuindex' => 0,
), '', true, true);


$handler = file_get_contents(RESOURCES . 'transport.menu.update.handler.js');
if ($handler === false) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get the source code of the menu item handler.');
    exit();
}

$menu[1] = $modx->newObject('modMenu');
$menu[1]->fromArray(array(
    'text' => PKG_NAME_LOWER . '.menu_update',
    'parent' => PKG_NAME,
    'namespace' => PKG_NAME_LOWER,
    'description' => PKG_NAME_LOWER . '.menu_update_description',
    'menuindex' => 0,
    'handler' => $handler,
), '', true, true);


$handler = file_get_contents(RESOURCES . 'transport.menu.clear.handler.js');
if ($handler === false) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get the source code of the menu item handler.');
    exit();
}

$menu[2] = $modx->newObject('modMenu');
$menu[2]->fromArray(array(
    'text' => PKG_NAME_LOWER . '.menu_clear',
    'parent' => PKG_NAME,
    'namespace' => PKG_NAME_LOWER,
    'description' => PKG_NAME_LOWER . '.menu_clear_description',
    'menuindex' => 1,
    'handler' => $handler,
), '', true, true);


return $menu;
