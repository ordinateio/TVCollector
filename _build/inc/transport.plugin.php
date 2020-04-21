<?php
/**
 * Build script for TVCollector.
 *
 * @package TVCollector
 * @author Callisto https://github.com/callisto2410
 * @source https://github.com/callisto2410/TVCollector
 */

/**
 * Returns the source code of the plugin, without opening and closing tags.
 *
 * @param string $file_name The name of the file containing the plugin code.
 * @return string
 */
function getPluginContent($file_name)
{
    $output = file_get_contents($file_name);
    if ($output !== false) {
        $output = trim(str_replace(array('<?php', '<?', '?>'), '', $output));
    }

    return $output;
}


$plugin_code = getPluginContent(ELEMENTS . 'plugins/plugin.tvcollector.php');
if ($plugin_code === false) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get plugin source code.');
    exit();
}


$plugin = $modx->newObject('modPlugin');
$plugin->fromArray(array(
    'id' => 1,
    'name' => PKG_NAME,
    'description' => PKG_DESCRIPTION,
    'plugincode' => $plugin_code,
), '', true, true);


$events = array();

$events['OnDocFormSave'] = $modx->newObject('modPluginEvent');
$events['OnDocFormSave']->fromArray(array(
    'event' => 'OnDocFormSave',
    'priority' => -100,
    'propertyset' => 0
), '', true, true);


$events['OnLoadWebDocument'] = $modx->newObject('modPluginEvent');
$events['OnLoadWebDocument']->fromArray(array(
    'event' => 'OnLoadWebDocument',
    'priority' => 0,
    'propertyset' => 0
), '', true, true);

$plugin->addMany($events);


return $plugin;
