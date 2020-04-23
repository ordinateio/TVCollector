<?php
/**
 * TVCollector.
 *
 * Saves additional fields in the resource properties column.
 * The output is available through the tvcollector placeholder.
 *
 * @package TVCollector
 * @author Callisto https://github.com/callisto2410
 * @source https://github.com/callisto2410/TVCollector
 */

switch ($modx->event->name) {
    case 'OnDocFormSave':
        $ID = $resource->get('id');
        $TVs = $modx->getCollection('modTemplateVarResource', array(
            'contentid' => $ID
        ));

        if (count($TVs) > 0) {
            $TVCollection = array();

            foreach ($TVs as $tv) {
                $TVCollection[$tv->TemplateVar->get('name')] = $tv->get('value');
            }

            $resource->setProperties($TVCollection, 'tvc', false);
            if (!$resource->save()) {
                $modx->log(modX::LOG_LEVEL_ERROR, "[TVCollector]: Failed to save resource with ID: {$ID}");
            }
        }

        break;


    case 'OnLoadWebDocument':
        $TVCollection = $modx->resource->get('properties');

        if (is_array($TVCollection) && array_key_exists('tvc', $TVCollection)) {
            $modx->toPlaceholders(array(
                'tvc' => $TVCollection['tvc']
            ));
        }

        break;
}
