<?php
function getPluginContent($filename) {
  $o = file_get_contents($filename);
  $o = trim( str_replace(array('<?php', '?>'), '', $o) );
  return $o;
}



// =============================================================================
// plugins
$plugins = array();

$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->fromArray(array(
  'id'          => 1,
  'name'        => 'TVCollector',
  'description' => '',
  'plugincode'  => getPluginContent($sources['elements'] . 'plugins/plugin.tvcollector.php'),
), '', true, true);
// =============================================================================



// =============================================================================
// events
$events = array();

$events['OnDocFormSave'] = $modx->newObject('modPluginEvent');
$events['OnDocFormSave']->fromArray(array(
  'event'       => 'OnDocFormSave',
  'priority'    => 0,
  'propertyset' => 0
), '', true, true);


$events['OnLoadWebDocument'] = $modx->newObject('modPluginEvent');
$events['OnLoadWebDocument']->fromArray(array(
  'event'       => 'OnLoadWebDocument',
  'priority'    => 0,
  'propertyset' => 0
), '', true, true);


$plugins[1]->addMany($events);
$modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in ' . count($events) . ' Plugin Events.'); flush();
unset($events);
// =============================================================================



return $plugins;
