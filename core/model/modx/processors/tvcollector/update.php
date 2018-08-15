<?php
/**
 * TVCollector
 * Update data for all resources.
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 *
 */
$resources = $modx->getCollection('modResource');
$modx->lexicon->load('tvcollector:default');
$counter = 0;

$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('tvcollector.updating_data'));
set_time_limit(0);

foreach ( $resources as $resource ) {

  $template = $modx->getObject('modTemplate', $resource->get('template'));
  if ( is_null($template) ) continue;

  $tvs = $template->getTemplateVarList();
  if ( is_null($tvs) ) continue;

  $tvcollector = array();
  foreach ( $tvs['collection'] as $tv ) {
    $tvcollector[$tv->get('name')] = $resource->getTVValue($tv->get('id'));
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
  sleep(1);

}

sleep(1);
$modx->log(modX::LOG_LEVEL_INFO,
  $modx->lexicon('tvcollector.processed_resources_from', array(
    'counter'   => $counter,
    'resources' => count($resources),
  ))
);
sleep(1);
$modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
