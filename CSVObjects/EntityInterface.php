<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 */

namespace CSVObjects;

/**
 * Interface to handle PHP entities, corresponding multistring CSV objects
 */
interface EntityInterface {

    /**
     * Got pre-processed data, originated  from CSV and complete the entity with 
     * extra logics if necessary
     * 
     * @param array $fields - Field-name keyed array of enity fields
     * @param array $rows - array of field-name keyed rows.
     * 
     * @throws CSVObjectsDataException on ay problem with data processing 
     */
    public function setFromCSV(string $id, array $fields, array $rows);

    /**
     * Fill entity with data and complete the entity with extra logics 
     * if necessary
     * 
     * @param array $fields - Field-name keyed array of enity fields
     * @param array $rows - array of field-name keyed rows.
     * 
     * @throws CSVObjectsDataException on ay problem with data processing 
     */
    public function setFromPHP(string $id, array $fields, array $rows);

    /**
     * Methods to use in subclasses when the entity knows how to setup itlesf 
     * from aray given. This methd should set id, fields and rows.
     * 
     * @param array $data - array of some data
     * 
     * @throws CSVObjectsDataException on ay problem with data processing 
     */
    public function setFromCustomData(array $data);
    
    /**
     * @return mixed Entity main Id
     */
    public function getId();

    /**
     * @return array of entity fiields, there no Id
     */
    public function getFields(): array;

    /**
     * @return array of entity rows, each is array keyed with field name
     */
    public function getRows(): iterable;

    /**
     * Set entity field to specific value. If field not exist - creates new one
     * 
     * @param string $fieldName
     * @param type $value
     */
    public function setField(string $fieldName, $value);

    /**
     * Return entity field value
     * 
     * @param string $fieldName, **must** be from entity fields, do not return rows
     */
    public function getField(string $fieldName);

    /**
     * Remove field from Entity
     * 
     * @param string $fieldName
     */
    public function unsetField(string $fieldName);

    /**
     * Change or add a column in the Entity rowset
     * 
     * @param string $columnName
     * @param array $values - array of value to put in the column, put null 
     *                        if array count is less then rows count
     */
    public function setColumn(string $columnName, array $values);

    /**
     * Change or add a column in the Entity rowset with same value
     * 
     * @param string $columnName
     * @param mixed  $value - value to set for all rows
     */
    public function setColumnSameValue(string $columnName, $value = null);

    /**
     * 
     * @param string $columnName - name of column to return
     * 
     * @return array the column values
     */
    public function getColumn(string $columnName): array;
    
    /**
     * Remove the column from rowset, if exist
     * 
     * @param string $columnName
     */
    public function unsetColumn(string $columnName);

    /**
     * Get total sum of rows field
     * 
     * @param string $fieldName
     */
    public function getSum(string $fieldName);
}
