<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects;

use CSVObjects\Exception\CSVObjectsException;
use CSVObjects\Exception\CSVObjectsDataException;
use CSVObjects\Exception\CSVObjectsStrictException;
use CSVObjects\Fields;

/**
 * Handles CSV profile
 */
class Profile {

    //Top-level profile data structure keys 
    const KEY_FIELD_LIST = 'fields';
    const OPT_STRICT = 'strict';
    const KEY_FIELD_NAME = 'fieldName';
    //
    //Field option keys
    const OPT_SHARED = 'shared';
    const OPT_REQUIRED = 'required';
    const OPT_TYPE = 'type';
    const OPT_IMPORT = 'import';
    const OPT_EXPORT = 'export';
    //Field type-dependant option keys
    const OPT_VALUE = 'value';
    const OPT_FORMAT = 'format';
    const OPT_FORMAT_IMPORT = 'formatImport';
    const OPT_FORMAT_EXPORT = 'formatExport';
    const OPT_TIMEZONE = 'timezone';
    const OPT_CLASS = 'class';
    //
    //Special field types
    const TYPE_ID = 'id';
    const TYPE_ROWCOUNT = 'rowCount';
    const TYPE_ROWINDEX = 'rowIndex';
    //
    //Data field types
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';
    const TYPE_CONST = 'const';
    const TYPE_CUSTOM = 'custom';
    //
    const DEFAULT_DATE_FORMAT = 'm/d/y';

    /* static $classmap = [
      static::TYPE_NUMBER => Fields\Number::class,
      static::TYPE_DATE => Fields\Date::class,
      static::TYPE_CONST => Fields\Constant::class,
      ]; */

    protected $strict;
    protected $profiles;
    protected $idFieldName;
    protected $targetClass;
    protected $entityFieldList = [];
    protected $rowFieldList = [];
    protected $rowCountFieldName = null;
    protected $rowIndexFieldName = null;

    public function __construct(array $profile) {
        $this->strict = $profile[self::OPT_STRICT] ?? false;
        if (!isset($profile[self::KEY_FIELD_LIST])) {
            throw new CSVObjectsException("CSV profile: fields definition not found");
        }
        $this->profiles = $this->loadProfiles($profile[self::KEY_FIELD_LIST]);
        $this->loadFields();
        if (is_null($this->idFieldName)) {
            throw new CSVObjectsException('CSV profile: Entity id field defenition required');
        }
        $this->targetClass = $profile[Profile::OPT_CLASS] ?? Entity::class;
    }

    public function isStrict() {
        return $this->strict;
    }

    public function getFieldProfiles() {
        return $this->profiles;
    }

    public function getFieldProfile(string $fieldName): array {
        if (isset($this->profiles[$fieldName])) {
            return $this->profiles[$fieldName];
        }
        throw new CSVObjectsException("CSV profile: unknown field profile '$fieldName' requested");
    }

    public function getFieldClass(string $fieldName): string {
        if (isset($this->profiles[$fieldName])) {
            return $this->profiles[$fieldName][Profile::OPT_CLASS];
        }
        throw new CSVObjectsException("CSV profile: unknown field profile '$fieldName' requested");
    }

    public function getIdField(): string {
        return $this->idFieldName;
    }

    public function getRowCountFieldName() {
        return $this->rowCountFieldName;
    }

    public function getRowIndexFieldName() {
        return $this->rowIndexFieldName;
    }

    public function getProfileFieldList(): array {
        return array_keys($this->profiles);
    }

    public function getEntityFieldList(): array {
        return $this->entityFieldList;
    }

    public function getRowFieldList(): array {
        return $this->rowFieldList;
    }

    public function getRequiredFieldList(): array {
        $result = [];
        foreach ($this->profiles as $fieldName => $profile) {
            if (($profile[Profile::OPT_REQUIRED] ?? false)) {
                $result[] = $fieldName;
            }
        }
        $result[] = $this->idFieldName;
        return $result;
    }

    public function getFieldListByType($fieldTypes): array {
        if (is_string($fieldTypes)) {
            $fieldTypes = [$fieldTypes];
        }
        $result = [];
        foreach ($this->profiles as $key => $val) {
            if (in_array($val[Profile::OPT_TYPE] ?? Profile::TYPE_TEXT, $fieldTypes)) {
                $result[] = $key;
            }
        }
        return $result;
    }

    public function getTargetClass(array $rowset) {
        return $this->targetClass;
    }

    protected function loadProfiles(array $profiles): array {
        foreach ($profiles as $fieldName => $profile) {
            $profiles[$fieldName][Profile::KEY_FIELD_NAME] = $fieldName;
        }
        return $profiles;
    }

    protected function loadFields() {
        foreach ($this->profiles as $fieldId => $fieldProfile) {
            $this->loadField($fieldId, $fieldProfile);
            $this->attachClass($fieldId, $fieldProfile);
        }
    }

    public function validateAndImportField($value, string $fieldName) {
        $class = $this->profiles[$fieldName][self::OPT_CLASS] ?? Fields\Generic::class;
        $class::validateCSV($value, $this->profiles[$fieldName] ?? []);
        return $class::importField($value, $this->profiles[$fieldName] ?? []);
    }

    public function validatePHPField($value, string $fieldName) {
        $class = $this->profiles[$fieldName][self::OPT_CLASS] ?? Fields\Generic::class;
        $class::validatePHP($value, $this->profiles[$fieldName] ?? []);
    }

    public static function validateId($id) {
        if (is_null($id) || !(is_string($id) || is_numeric($id))) {
            throw new CSVObjectsDataException("Entity Id field must be not null string, int or float");
        }
        return $id;
    }

    protected function loadField($fieldName, $fieldProfile) {
        switch ($fieldProfile[self::OPT_TYPE] ?? Profile::TYPE_TEXT) {
            case self::TYPE_ID:
                return $this->setId($fieldName);
            case self::TYPE_ROWCOUNT:
                return $this->setRowcount($fieldName);
            case self::TYPE_ROWINDEX:
                return $this->setRowindex($fieldName);
            default:
                if ($fieldProfile[self::OPT_SHARED] ?? false) {
                    $this->entityFieldList[] = $fieldName;
                } else {
                    $this->rowFieldList[] = $fieldName;
                }
        }
    }

    protected function attachClass($fieldName, $fieldProfile) {
        $type = $fieldProfile[self::OPT_TYPE] ?? Profile::TYPE_TEXT;
        if (Profile::TYPE_CUSTOM == $type) {
            if (!isset($fieldProfile[Profile::OPT_CLASS])) {
                throw new CSVObjectsException("Class option is not given for custom class field '$fieldName' profile");
            }
            if (!is_subclass_of($fieldProfile[Profile::OPT_CLASS], Fields\FieldInterface::class)) {
                throw new CSVObjectsException("Custom class do not implement interface 'FieldInterface' for field '$fieldName' profile");
            }
            return;
        }
        switch ($type) {
            case static::TYPE_NUMBER:
                return $this->profiles[$fieldName][static::OPT_CLASS] = Fields\Number::class;
            case static::TYPE_DATE:
                return $this->profiles[$fieldName][static::OPT_CLASS] = Fields\Date::class;
            case static::TYPE_CONST:
                return $this->profiles[$fieldName][static::OPT_CLASS] = Fields\Constant::class;
            default:
                $this->profiles[$fieldName][static::OPT_CLASS] = Fields\Generic::class;
        }
    }

    protected function setRowcount($fieldName) {
        if (is_null($this->rowCountFieldName)) {
            $this->entityFieldList[] = $this->rowCountFieldName = $fieldName;
            $this->profiles[$fieldName][Profile::OPT_CLASS] = Fields\Generic::class;
        } else {
            throw new CSVObjectsException("CSV profile: only one 'rowCount' type field allowed");
        }
    }

    protected function setId($fieldName) {
        if (is_null($this->idFieldName)) {
            $this->idFieldName = $fieldName;
            $this->profiles[$fieldName][Profile::OPT_CLASS] = Fields\Generic::class;
        } else {
            throw new CSVObjectsException("CSV profile: only one 'id' type field allowed");
        }
    }

    protected function setRowindex($fieldName) {
        if (is_null($this->rowIndexFieldName)) {
            $this->rowFieldList[] = $this->rowIndexFieldName = $fieldName;
            $this->profiles[$fieldName][Profile::OPT_CLASS] = Fields\Generic::class;
        } else {
            throw new CSVObjectsException("CSV profile: only one 'rowIndex' type field allowed");
        }
    }

}
