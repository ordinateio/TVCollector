<?php

/**
 * Class TVCollectorProcessor - Update data for all resources.
 *
 * @package TVCollector
 * @author Callisto https://github.com/callisto2410
 * @source https://github.com/callisto2410/TVCollector
 */
class TVCollectorProcessor extends modProcessor
{
    /** @var int The wait interval in microseconds after processing each resource. */
    private $sleep = 50000;

    /** @var int The number of resources per slice. */
    private $limit = 100;

    /** @var int Resource sampling offset. */
    private $offset = 0;

    /** @var int Current slice number. */
    private $slice = 1;

    /** @var int The number of processed resources. */
    private $updated = 0;

    /** @var string Absolute path to the lock file. */
    private $lock_file = MODX_CORE_PATH . 'cache/tvcollector.lock';

    /**
     * The main method of the processor.
     *
     * @return array|string
     */
    public function process()
    {
        $this->modx->lexicon->load('tvcollector:default');

        if ($this->isLock()) {
            $this->printAlreadyRunning();

            return $this->failure('failure');
        }

        set_time_limit(0);
        $this->lock();

        if ($this->isUpdate()) {
            $this->update();
        }

        if ($this->isClear()) {
            $this->clear();
        }

        $this->printReport();
        $this->unlock();

        return $this->success('success');
    }

    /**
     * Returns true if it is necessary to start the data updating process, otherwise returns false.
     *
     * @return bool
     */
    private function isUpdate()
    {
        return $_POST['process'] === 'update';
    }

    /**
     * Collecting and saving TVs.
     */
    private function update()
    {
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('tvcollector.updating_data'));

        while (true) {
            $resources = $this->getSliceResources();
            if (count($resources) === 0 || is_null($resources)) {
                break;
            }

            $this->printCurrentSlice();

            /** @var modResource $resource */
            foreach ($resources as $resource) {
                $ID = $resource->get('id');
                $TVs = $this->modx->getCollection('modTemplateVarResource', array(
                    'contentid' => $ID
                ));

                if (count($TVs) > 0) {
                    $TVCollection = $this->getTVCollection($TVs);

                    $resource->setProperties($TVCollection, 'tvc', false);
                    if (!$resource->save()) {
                        $this->printCouldNotBeSaved($resource);
                        continue;
                    }

                    $this->updated++;
                }

                usleep($this->sleep);
            }

            $this->offset += $this->limit;
            $this->slice++;
        }
    }

    /**
     * Returns an associative array of TVs in the form name => value.
     *
     * @param array $TVs Array of TVs for a resource.
     * @return array
     */
    private function getTVCollection(array $TVs)
    {
        $TVCollection = array();

        foreach ($TVs as $tv) {
            $TVCollection[$tv->TemplateVar->get('name')] = $tv->get('value');
        }

        return $TVCollection;
    }

    /**
     * Returns a slice of resources for processing.
     *
     * @return array|null
     */
    private function getSliceResources()
    {
        $query = $this->modx->newQuery('modResource');
        $query->limit($this->limit, $this->offset);

        return $this->modx->getCollection('modResource', $query);
    }

    /**
     * Returns true if it is necessary to start the data cleaning process, otherwise returns false.
     *
     * @return bool
     */
    private function isClear()
    {
        return !$this->isUpdate();
    }

    /**
     * Cleans previously saved data.
     */
    private function clear()
    {
        $this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('tvcollector.data_cleaning'));

        while (true) {
            $resources = $this->getSliceResources();
            if (count($resources) === 0 || is_null($resources)) {
                break;
            }

            $this->printCurrentSlice();

            /** @var modResource $resource */
            foreach ($resources as $resource) {
                $resource->setProperties(array(), 'tvc', false);
                if (!$resource->save()) {
                    $this->printCouldNotBeSaved($resource);
                    continue;
                }

                $this->updated++;

                usleep($this->sleep);
            }

            $this->offset += $this->limit;
            $this->slice++;
        }
    }

    /**
     * Returns true if the lock file exists, otherwise returns false.
     *
     * @return bool
     */
    private function isLock()
    {
        return file_exists($this->lock_file);
    }

    /** Locking. */
    private function lock()
    {
        fclose(fopen($this->lock_file, 'w'));
    }

    /**
     * Unlocking.
     */
    private function unlock()
    {
        unlink($this->lock_file);
    }

    /**
     * Displays a message about an already running processor.
     */
    private function printAlreadyRunning()
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR,
            $this->modx->lexicon('tvcollector.already_running')
        );
        $this->printCompleted();
    }

    /**
     * Displays information about the current slice.
     */
    private function printCurrentSlice()
    {
        $this->modx->log(modX::LOG_LEVEL_INFO,
            $this->modx->lexicon('tvcollector.processing_slice', array(
                'slice' => $this->slice,
                'from' => $this->offset,
                'to' => $this->offset + $this->limit,
            ))
        );
    }

    /**
     * It displays a message about the inability to save the resource.
     *
     * @param modResource $resource
     */
    private function printCouldNotBeSaved(modResource $resource)
    {
        $this->modx->log(modX::LOG_LEVEL_WARN,
            $this->modx->lexicon('tvcollector.resource_could_not_be_saved', array(
                'id' => $resource->id
            ))
        );
    }

    /**
     * Prints a report.
     */
    private function printReport()
    {
        $this->modx->log(modX::LOG_LEVEL_INFO, str_repeat('-', 78));
        $this->modx->log(modX::LOG_LEVEL_INFO,
            $this->modx->lexicon('tvcollector.total_processed', array(
                "updated" => $this->updated,
            ))
        );
        $this->printCompleted();
    }

    /**
     * Completes console output.
     */
    private function printCompleted()
    {
        $this->modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
    }
}


return 'TVCollectorProcessor';