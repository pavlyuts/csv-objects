<?php

/*
 * Profile-based multistring CSV entity handling library
 */

namespace CSVObjects;

use SKArray\SKArray;
use League\Csv\Reader;
use League\Csv\Writer;
use CSVObjects\Exception\CSVObjectsException;

/**
 * Collection of Peachtree Entities
 *
 */
class Collection extends SKArray {

    protected $profile;
    protected $processor;

    public function __construct(Profile $profile) {
        parent::__construct();
        $this->profile = $profile;
        $this->processor = new Processor($this->profile);
        return $this;
    }

    public function createFromCSVFile(string $fileName) {
        $reader = Reader::createFromPath($fileName);
        $this->renderImportFromReader($reader);
    }

    public function createFromCSVString(string $csvData) {
        $reader = Reader::createFromString($csvData);
        $this->renderImportFromReader($reader);
    }

    public function createFromPHPCustomArray(array $data, string $targetClass = Entity::class, ...$args) {
        try {
            if (!((new $targetClass(...$args)) instanceof EntityInterface)) {
                throw new CSVObjectsException("The given class must implement EntityInterface");
            }
        } catch (\Error $e) {
            throw new CSVObjectsException("Can't create new class '$targetClass' with error '{$e->getMessage()}'");
        }
        $entities = [];
        foreach ($data as $dataset) {
            $c = new $targetClass(...$args);
            $c->setFromCustomData($dataset);
            $entities[] = $c;
        }
        $this->vaidateAndAddMultple($entities);
    }

    public function exportCSVString(): string {
        $writer = Writer::createFromString("");
        $this->doExportCSV($writer);
        return $writer->getContent();
    }

    public function exportCSVFile(string $fileName) {
        $writer = Writer::createFromPath($fileName, 'w+');
        $this->doExportCSV($writer);
    }

    public function vaidateAndAddMultple(array $entities) {
        foreach ($entities as $entity) {
            $this->processor->validatePHPData($entity);
        }
        foreach ($entities as $entity) {
            $this[$entity->getId()] = $entity;
        }
    }

    public function getRowsWithoutIdentity() {
        return $this->processor->getRowsWithoutIdentity();
    }

    public function getBrokenEntities() {
        return $this->processor->getBrokenEntities();
    }

    protected function renderImportFromReader(Reader $reader) {
        $entityData = $this->processor->doImportCSV($reader);
        foreach ($entityData as $fieldId => $dataset) {
            $targetClass = $this->profile->getTargetClass($dataset);
            $entity = new $targetClass();
            $entity->setFromCSV($fieldId, $dataset['fields'], $dataset['rows']);
            $this[$fieldId] = $entity;
        }
    }

    protected function doExportCSV(Writer $writer) {
        $writer->insertOne($this->profile->getProfileFieldList());
        foreach ($this->list as $element) {
            $writer->insertAll($this->processor->doEntityExport($element));
        }
    }

    public function offsetSet($offset, $value): void {
        if (!($value instanceof EntityInterface)) {
            throw new CSVObjectsException("Collection element must impement EntityInterface");
        }
        $this->processor->validatePHPData($value);
        if (is_null($offset)) {
            $offset = $value->getId();
        }
        if ($offset != $value->getId()) {
            throw new CSVObjectsException("Trying to put key different from Entity Id");
        }
        parent::offsetSet($offset, $value);
    }

}
