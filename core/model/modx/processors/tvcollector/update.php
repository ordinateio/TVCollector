<?php
/**
 * TVCollector
 * Update data for all resources.
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 */
set_time_limit(0);

$resources = $modx->getCollection('modResource');
$modx->lexicon->load('tvcollector:default');
$counter = 0;
$usleep = 200000;

$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('tvcollector.updating_data'));

foreach ($resources as $resource) {
  $id = $resource->get('id');
  $tvs = $modx->getCollection('modTemplateVarResource', array(
    'contentid' => $id
  ));

  if (count($tvs) > 0) {
    $tvcollector = array();

    foreach ($tvs as $tv) {
      $name = $tv->TemplateVar->get('name');
      $value = $tv->get('value');

      if (!empty($value)) {
        $tvcollector[$name] = $value;
      }
    }

    $resource->setProperties($tvcollector, 'tvc', false);

    $ok = $resource->save();
    if (!$ok) {
      $modx->log(modX::LOG_LEVEL_WARN,
        $modx->lexicon('tvcollector.resource_could_not_be_saved', array(
          'id' => $resource->id
        ))
      );
      continue;
    }

    $modx->log(modX::LOG_LEVEL_INFO,
      $modx->lexicon('tvcollector.resource_successfully_updated', array(
        'id' => $resource->id
      ))
    );

    $counter++;
  }

  usleep($usleep);
}



$modx->log(modX::LOG_LEVEL_INFO,
  $modx->lexicon('tvcollector.processed_resources_from', array(
    'counter'   => $counter,
    'resources' => count($resources),
  ))
);

$modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');