<?php
/**
 * TVCollector
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 *
 */

// -----------------------------------------------------------------------------
// Start
$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Necessary variables
define('PKG_NAME', 'TVCollector');
define('PKG_NAME_LOWER', 'tvcollector');
define('PKG_VERSION', '1.0.2');
define('PKG_RELEASE', 'pl');
define('PKG_AUTO_INSTALL', false);

$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'lexicon' => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'elements' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/',
    'processors' => $root . 'core/model/modx/processors/' . PKG_NAME_LOWER,
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
);

unset($root);
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// MODx
require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Packing categories
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);

// Packaging plugins
$modx->log(modX::LOG_LEVEL_INFO, 'Packaging plugins...');
$plugins = include $sources['data'] . 'transport.plugins.php';

if (empty($plugins)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to pack plugins');
}
$category->addMany($plugins);

// Create category
$attr = array(
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
$vehicle = $builder->createVehicle($category, $attr);

// Adding file resolvers
$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers to categories...');
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers to processors...');
$vehicle->resolve('file', array(
    'source' => $sources['processors'],
    'target' => "return MODX_CORE_PATH . 'model/modx/processors/';",
));

$builder->putVehicle($vehicle);
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Packing menu items
$modx->log(modX::LOG_LEVEL_INFO, 'Packing menu items...');
$menu = include $sources['data'] . 'transport.menu.php';

if (empty($menu)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to pack menu items.');
} else {
    for ($i = 0; $i < count($menu); $i++) {
        $vehicle = $builder->createVehicle($menu[$i], array(
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
}
unset($vehicle, $menu);
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Adding package attributes
$modx->log(modX::LOG_LEVEL_INFO, 'Adding package attributes and setup options...');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
));
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Packing up transport package
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package...');
$builder->pack();
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Auto install
if (PKG_AUTO_INSTALL) {
    $signature = $builder->getSignature();
    $sig = explode('-', $signature);
    $versionSignature = explode('.', $sig[1]);
    $package = $modx->getObject('transport.modTransportPackage', array(
        'signature' => $signature
    ));
    if (!$package) {
        $package = $modx->newObject('transport.modTransportPackage');
        $package->set('signature', $signature);
        $package->fromArray(array(
            'created' => date('Y-m-d h:i:s'),
            'updated' => null,
            'state' => 1,
            'workspace' => 1,
            'provider' => 0,
            'source' => $signature . '.transport.zip',
            'package_name' => $sig[0],
            'version_major' => $versionSignature[0],
            'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
            'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
        ));
        if (!empty($sig[2])) {
            $r = preg_split('/([0-9]+)/', $sig[2], -1, PREG_SPLIT_DELIM_CAPTURE);
            if (is_array($r) && !empty($r)) {
                $package->set('release', $r[0]);
                $package->set('release_index', (isset($r[1]) ? $r[1] : '0'));
            } else {
                $package->set('release', $sig[2]);
            }
        }
        $package->save();

        if ($package->install()) {
            $modx->runProcessor('system/clearcache');
        }
    }
}
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// Finish
$tend = explode(' ', microtime());
$tend = $tend[1] + $tend[0];
$totalTime = sprintf("%2.4f s", ($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO, "Package Built. <br>Execution time: {$totalTime}");
exit();
// -----------------------------------------------------------------------------
