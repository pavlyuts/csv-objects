<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects;

use CSVObjects\Profile;
use CSVObjects\Exception\CSVObjectsDataException;
use CSVObjects\Exception\CSVObjectsStrictException;
use SKArray\SKArray;
use \League\Csv\Reader;

/**
 * Class to validate CSV against profile
 *
 */
class Processor {

    protected $profile;
    protected $rowsWithoutIdentity = [];
    protected $brokenEntities = [];

    public function __construct(Profile $profile) {
        $this->profile = $profile;
    }

    public function doImportCSV(Reader $csv) {
        $csv->setHeaderOffset(0);
        $this->validateHeaders($csv->getHeader());
        return $this->extractValidateEntities($this->splitCsvByEntity($csv));
    }

    public function getRowsWithoutIdentity() {
        return $this->rowsWithoutIdentity;
    }

    public function getBrokenEntities() {
        return $this->brokenEntities;
    }

    public function validatePHPData(EntityInterface $entity) {
        $requiredFields = $this->buildExportRequiredFieldList();
        $idFieldName = $this->profile->getIdField();
        $fields = $entity->getFields();
        $rows = $entity->getRows();
        foreach ($requiredFields as $fieldName) {
            if ($fieldName == $idFieldName) {
                if (!is_null($entity->getId())) {
                    continue;
                }
            } elseif (key_exists($fieldName, $fields)) {
                $this->profile->validatePHPField($fields[$fieldName], $fieldName);
                continue;
            } elseif ($this->validatePHPRowsField($fieldName, $rows)) {
                continue;
            }
            throw new CSVObjectsDataException("Required field '$fieldName' not found in the Entity dataset");
        }
    }

    public function doEntityExport(EntityInterface $element) {
        $result = [];
        $fields = array_merge($element->getFields(), [$this->profile->getIdField() => $element->getId()]);
        $rows = $element->getRows();
        $rows = ($rows == []) ? [[]] : $rows;
        $rowIndex = 1;
        $rowCount = count($rows);
        foreach ($rows as $row) {
            $result[] = $this->doEntityRowExport(array_merge($fields, $row), $rowIndex, $rowCount);
        }
        return $result;
    }

    protected function doEntityRowExport(array $row, &$rowIndex, $rowCount): array {
        $result = [];
        foreach ($this->profile->getFieldProfiles() as $fieldName => $fieldProfile) {
            switch ($fieldProfile[Profile::OPT_TYPE] ?? Profile::TYPE_TEXT) {
                case Profile::TYPE_ROWCOUNT:
                    $result[$fieldName] = $rowCount;
                    continue;
                case Profile::TYPE_ROWINDEX:
                    $result[$fieldName] = $row[$fieldName] ?? $rowIndex++;
                    continue;
                default:
                    $fieldClass = $this->profile->getFieldClass($fieldName);
                    $result[$fieldName] = $fieldClass::exportField($row[$fieldName] ?? null, $fieldProfile);
            }
        }
        return $result;
    }

    protected function splitCsvByEntity(Reader $csv): iterable {
        $this->rowsWithoutIdentity = [];
        $idFieldProfile = $this->profile->getFieldProfile($this->profile->getIdField());
        $result = new SKArray();
        foreach ($csv->getRecords() as $rowNr => $record) {
            try {
                $id = $this->extractValidateIdField($record, $idFieldProfile);
                //$result[$id] = array_merge($result[$id] ?? [], [$record]);
                $result->setSubarrayItem($id, $record, $rowNr);
            } catch (CSVObjectsDataException $ex) {
                $this->rowsWithoutIdentity[] = $rowNr;
            }
        }
        return $result;
    }

    protected function extractValidateEntities(SKArray $recordSets): iterable {
        $this->brokenEntities = [];
        $result = new SKArray();
        foreach ($recordSets as $id => $rowset) {
            try {
                $result[$id] = $this->extractValidateEntityData($rowset);
            } catch (CSVObjectsDataException $ex) {
                $this->brokenEntities[] = [
                    'id' => $id,
                    'rows' => array_keys($rowset),
                    'error' => $ex->getMessage(),
                ];
            }
        }
        return $result;
    }

    /**
     * Check if an array filled with same value, simple == check
     * 
     * @param array $data - array to check
     * @return boolean true if each element the same with == check
     */
    public static function arrayValuesSame(array $data): bool {
        $item = null;
        foreach ($data as $element) {
            if (is_null($item)) {
                $item = $element;
                continue;
            }
            if ($item != $element) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if an array filled with same value, strict === check
     * 
     * @param array $data - array to check
     * @return boolean true if each element the same with strict check
     */
    public static function arrayValuesSameStrict(array $data): bool {
        $item = null;
        foreach ($data as $element) {
            if (is_null($item)) {
                $item = $element;
                continue;
            }
            if ($item !== $element) {
                return false;
            }
        }
        return true;
    }

    /**
     * take array of arrays and return attay of it's keys
     * 
     * @param array $data - array of array
     * @return array      - array of key arrays
     */
    public static function subarrayKeys(array $data): array {
        $result = [];
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val) ? array_keys($val) : null;
        }
        return $result;
    }

    /**
     * Check if the columns with listed names contain the same value each
     * 
     * @param array $rowset - multi-dimensional array to test
     * @param array $columns
     * @return boolean
     */
    protected function validateHeaders(array $headers) {
        if ($this->profile->isStrict()) {
            if ($headers === $this->profile->getProfileFieldList()) {
                return;
            } else {
                throw new CSVObjectsStrictException("CSV headers strict check filed: names, count or order does not match");
            }
        }
        foreach ($this->profile->getRequiredFieldList() as $fieldName) {
            if (!in_array($fieldName, $headers)) {
                throw new CSVObjectsDataException("CSV headers check filed: required field '$fieldName' not exists");
            }
        }
        return;
    }

    protected function extractValidateIdField(array $record, array $profile) {
        if (($id = $record[$profile[Profile::KEY_FIELD_NAME]] ?? '') == '') {
            throw new CSVObjectsDataException("Id field not found or empty");
        }
        $fieldClass = $profile[Profile::OPT_CLASS] ?? \CSVObjects\Fields\Generic::class;
        $fieldClass::validatePHP($id, $profile);
        return $fieldClass::importField($id, $profile);
    }

    protected function extractValidateEntityData(array $rowset): array {
        $this->validateAllRowsFull($rowset);
        $shared = $this->extractValidateShared($rowset);
        $rowCountFieldName = $this->profile->getRowCountFieldName();
        if (!is_null($rowCountFieldName) &&
                isset($shared[$rowCountFieldName]) &&
                ($shared[$rowCountFieldName] != count($rowset))) {
            throw new CSVObjectsDataException("Row count field is {$shared[$rowCountFieldName]}, but " . count($rowset) . " found");
        }
        return [
            'fields' => $shared,
            'rows' => $this->extractAndValidateRows($rowset)
        ];
    }

    protected function validateAllRowsFull(array $rowset) {
        foreach ($rowset as $key => $row) {
            if (in_array(null, $row, true)) {
                throw new CSVObjectsDataException("Field count in row $key less then header column count");
            }
        }
    }

    protected function sharedColumnsHasSameValue(array $rowset) {
        $failures = [];
        $n = count($rowset);
        foreach ($this->profile->getEntityFieldList() as $fieldName) {
            $column = array_column($rowset, $fieldName);
            if ((count($column) != 0) && !(self::arrayValuesSame($column) && (count($column) == $n))) {
                $failures[] = $fieldName;
            }
        }
        if ($failures != []) {
            throw new CSVObjectsDataException("Shared field must have same value, failed for fields '" .
                            implode("', '", $failures) . "'");
        }
    }

    protected function extractValidateShared(array $rowset): array {
        $this->sharedColumnsHasSameValue($rowset);
        $result = [];
        $row = $rowset[array_keys($rowset)[0]];
        foreach ($this->profile->getEntityFieldList() as $fieldName) {
            if (!isset($row[$fieldName])) {
                continue;
            }
            $result[$fieldName] = $this->profile->validateAndImportField($row[$fieldName], $fieldName);
        }
        return $result;
    }

    protected function extractAndValidateRows(array $rowset): array {
        $result = $this->unsetColumns($rowset, array_merge($this->profile->getEntityFieldList(), [$this->profile->getIdField()]));
        foreach ($result as $rowKey => $row) {
            foreach ($row as $fieldName => $value) {
                $result[$rowKey][$fieldName] = $this->profile->validateAndImportField($value, $fieldName);
            }
        }
        return $result;
    }

    protected function unsetColumns(array $data, array $columns) {
        foreach ($data as $key => $val) {
            foreach ($columns as $fieldName) {
                unset($data[$key][$fieldName]);
            }
        }
        return $data;
    }

    protected function buildExportRequiredFieldList() {
        if ($this->profile->isStrict()) {
            return array_diff($this->profile->getProfileFieldList(),
                    $this->profile->getFieldListByType([Profile::TYPE_CONST, Profile::TYPE_ROWCOUNT, Profile::TYPE_ROWINDEX]));
        } else {
            return $this->profile->getRequiredFieldList();
        }
    }

    protected function validatePHPRowsField($fieldName, $rows) {
        $data = array_column($rows, $fieldName);
        if (count($data) == 0) {
            return false;
        }
        if (count($data) != count($rows)) {
            throw new CSVObjectsDataException("Required rows field '$fieldName' has only "
                            . count($data) . " values in " . count($rows) . " rows");
        }
        foreach ($data as $value) {
            $this->profile->validatePHPField($value, $fieldName);
        }
        return true;
    }

}
