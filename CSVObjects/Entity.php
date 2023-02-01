<?php

/*
 * Profile-based multistring CSV entity handling library
 */

namespace CSVObjects;

use CSVObjects\Exception\CSVObjectsDataException;

/**
 * Peachtree entity class to handle any entity you may import or export
 *
 */
class Entity implements EntityInterface {

    protected $id = null;
    protected $fields = [];
    protected $rows = [];

    public function getField($fieldName) {
        return $this->fields[$fieldName] ?? null;
    }

    public function getFields(): array {
        return $this->fields;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getRows(): iterable {
        return $this->rows;
    }

    public function getSum(string $fieldName) {
        return array_sum(array_column($this->rows, $fieldName));
    }

    public function setField(string $fieldName, $value) {
        $this->checkFieldName($fieldName);
        $this->fields[$fieldName] = $value;
    }

    public function unsetField(string $fieldName) {
        unset($this->fields[$fieldName]);
    }

    public function setColumn(string $columnName, array $values) {
        $this->checkColumnName($columnName);
        $values = array_values($values);
        $index = 0;
        foreach ($this->rows as $key => $val) {
            $this->rows[$key][$columnName] = $values[$index++] ?? null;
        }
    }

    public function setColumnSameValue(string $columnName, $value = null) {
        $this->checkColumnName($columnName);
        foreach ($this->rows as $key => $val) {
            $this->rows[$key][$columnName] = $value;
        }
    }

    public function getColumn(string $columnName): array {
        return array_column($this->rows, $columnName);
    }

    public function unsetColumn(string $columnName) {
        foreach ($this->rows as $key => $val) {
            unset($this->rows[$key][$columnName]);
        }
    }

    public function setFromCSV(string $id, array $fields, array $rows) {
        $this->setData($id, $fields, $rows);
    }

    public function setFromPHP(string $id, array $fields, array $rows) {
        $this->setData($id, $fields, $rows);
    }

    public function setFromCustomData(array $data) {
        if (!(isset($data['id']) && isset($data['fields']) && isset($data['rows']))) {
            throw new CSVObjectsDataException("Custom data array must include: 'id','fields','rows'");
        }
        $this->setFromPHP($data['id'], $data['fields'], $data['rows']);
    }

    protected function setData($id, array $fields, array $rows) {
        $this->id = Profile::validateId($id);
        $this->fields = $fields;
        $this->rows = $rows;
    }

    protected function checkFieldName(string $fieldName) {
        array_walk_recursive($this->rows, function ($val, $key) use ($fieldName) {
            if (isset($key) && ($key == $fieldName)) {
                throw new CSVObjectsDataException("Can't set column name the same as it alredy exist as Entity field name");
            }
        });
    }

    protected function checkColumnName(string $columnName) {
        if (key_exists($columnName, $this->fields)) {
            throw new CSVObjectsDataException("Can't set field name the same as it alredy exist as Entity column name");
        }
    }

}
