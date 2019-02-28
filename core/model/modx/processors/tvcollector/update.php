<?php
/**
 * TVCollector
 * Update data for all resources.
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 */

$resources = $modx->getCollection('modResource');
$modx->lexicon->load('tvcollector:default');
$counter = 0;
$sleep = 0.2;

$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('tvcollector.updating_data'));
set_time_limit(0);

foreach ( $resources as $resource ) {
  $template = $modx->getObject('modTemplate', $resource->get('template'));
  if ( is_null($template) ) continue;

  $tvs = $template->getTemplateVarList();
  if ( is_null($tvs) ) continue;

  $tvcollector = array();
  foreach ( $tvs['collection'] as $tv ) {
    $tvId = $tv->get('id');
    $tvName = $tv->get('name');
    $tvcollector[$tvName] = $resource->getTVValue($tvId);
  }
  $resource->setProperties($tvcollector, 'tvc', false);

  if ( $resource->save() !== false ) {
    $modx->log(modX::LOG_LEVEL_INFO,
      $modx->lexicon('tvcollector.resource_successfully_updated', array(
        'id' => $resource->id
      ))
    );
    $counter++;
  } else {
    $modx->log(modX::LOG_LEVEL_WARN,
      $modx->lexicon('tvcollector.resource_could_not_be_saved', array(
        'id' => $resource->id
      ))
    );
  }

  sleep($sleep);
}

sleep($sleep);
$modx->log(modX::LOG_LEVEL_INFO,
  $modx->lexicon('tvcollector.processed_resources_from', array(
    'counter'   => $counter,
    'resources' => count($resources),
  ))
);
sleep($sleep);
$modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');