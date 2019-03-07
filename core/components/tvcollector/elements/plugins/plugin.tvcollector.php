<?php
/**
 * TVCollector
 * Saves additional fields in the resource properties column.
 * The output is available through the tvcollector placeholder.
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 */

switch ($modx->event->name) {
  case 'OnDocFormSave':
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
      $resource->save();
    }
  break;



  case 'OnLoadWebDocument':
    $tvcollector = $modx->resource->get('properties');

    if (is_array($tvcollector) && array_key_exists('tvc', $tvcollector)) {
      $modx->toPlaceholders(array(
        'tvc' => $tvcollector['tvc']
      ));
    }
  break;
}
