<?php
/**
 * Build script for TVCollector.
 *
 * @package TVCollector
 * @author Callisto https://github.com/callisto2410
 * @source https://github.com/callisto2410/TVCollector
 */

header('Content-type: text/html; charset=utf-8');
set_time_limit(0);


// region Defining the necessary variables.
define('PKG_NAME', 'TVCollector');
define('PKG_NAME_LOWER', 'tvcollector');
define('PKG_VERSION', '2.0.0');
define('PKG_RELEASE', 'pl');
define('PKG_DESCRIPTION', 'Saves additional fields in the resource properties column. The output is available through the tvcollector placeholder.');
define('ROOT', dirname(dirname(__FILE__)) . '/');
define('INC', ROOT . '_build/inc/');
define('RESOURCES', ROOT . '_build/resources/');
define('ELEMENTS', ROOT . 'core/components/' . PKG_NAME_LOWER . '/elements/');
define('LEXICON', ROOT . 'core/components/' . PKG_NAME_LOWER . '/lexicon/');
define('DOCUMENTATION', ROOT . 'core/components/' . PKG_NAME_LOWER . '/docs/');
define('PROCESSORS', ROOT . 'core/model/modx/processors/' . PKG_NAME_LOWER . '/');
define('PKG_ROOT', ROOT . 'core/components/' . PKG_NAME_LOWER . '/');

$time_start = explode(' ', microtime());
$time_start = $time_start[1] + $time_start[0];
// endregion


// region Initializing a MODX instance.
require_once ROOT . '_build/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->loadClass('transport.modPackageBuilder', '', false, true);

$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');
// endregion


// region Plugins.
$plugin = require_once INC . 'transport.plugin.php';
if (!is_object($plugin)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to build plugin.');
    exit();
}

$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);
$category->addMany($plugin);

$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Plugins' => array(
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
                'PluginEvents' => array(
                    xPDOTransport::UNIQUE_KEY => array('pluginid', 'event'),
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => false,
                ),
            ),
        ),
    ),
);

$vehicle = $builder->createVehicle($category, $attributes);

$vehicle->resolve('file', array(
    'source' => PKG_ROOT,
    'target' => "return MODX_CORE_PATH . 'components/';",
));

$vehicle->resolve('file', array(
    'source' => PROCESSORS,
    'target' => "return MODX_CORE_PATH . 'model/modx/processors/';",
));

$builder->putVehicle($vehicle);
// endregion


// region Menu.
$menu = require_once INC . 'transport.menu.php';
if (!is_array($menu)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to build menu.');
    exit();
}

foreach ($menu as $key => $item) {
    $vehicle = $builder->createVehicle($menu[$key], array(
        xPDOTransport::UNIQUE_KEY => 'text',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
            'Action' => array(
                xPDOTransport::UNIQUE_KEY => array('namespace', 'controller'),
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => true,
            ),
        ),
    ));

    $builder->putVehicle($vehicle);
}
// endregion


// region Packing.
$builder->setPackageAttributes(array(
    'license' => file_get_contents(DOCUMENTATION . 'license.txt'),
    'readme' => file_get_contents(DOCUMENTATION . 'readme.txt'),
    'changelog' => file_get_contents(DOCUMENTATION . 'changelog.txt'),
));

$builder->pack();
// endregion


// region Print report.
$time_end = explode(' ', microtime());
$time_end = $time_end[1] + $time_end[0];
$total_time = sprintf("%2.4f s", ($time_end - $time_start));

$modx->log(modX::LOG_LEVEL_INFO, 'Package created, time: ' . $total_time);

exit();
// endregion
