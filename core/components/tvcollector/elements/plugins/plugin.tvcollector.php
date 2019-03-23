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
      $properties = array();

      foreach ($tvs as $tv) {
        $name = $tv->TemplateVar->get('name');
        $value = $tv->get('value');
        /*
          need tests
          replace it to just:
          $properties[$name] = $value;
        */
        if (!empty($value)) {
          $properties[$name] = $value;
        }
      }

      $resource->setProperties($properties, 'tvc', false);
      $resource->save();
    }
  break;



  case 'OnLoadWebDocument':
    $properties = $modx->resource->get('properties');

    if (is_array($properties) && array_key_exists('tvc', $properties)) {
      $modx->toPlaceholders(array(
        'tvc' => $properties['tvc']
      ));
    }
  break;
}