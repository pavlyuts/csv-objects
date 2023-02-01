<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects\Fields;

use CSVObjects\Profile;
use CSVObjects\Exception\CSVObjectsException;
use CSVObjects\Exception\CSVObjectsDataException;

/**
 * Generic field class
 *
 */
class Generic implements FieldInterface {

    /**
     * @inheritDoc
     */
    public static function exportField($field, array $fieldProfile): string {
        return $field ?? ($fieldProfile[Profile::OPT_VALUE] ?? '');
    }

    /**
     * @inheritDoc
     */
    public static function importField(string $field, array $fieldProfile) {
        return $field;
    }

    /**
     * always valid, do nothing
     */
    public static function validateCSV(string $field, array $fieldProfile) {
        
    }

    /**
     * Always valid, do nothing
     */
    public static function validatePHP($field, array $fieldProfile) {
        if (!is_scalar($field)) {
            throw new CSVObjectsDataException("Field {$fieldProfile[Profile::KEY_FIELD_NAME]} must be scalar type");
        }
    }

    /**
     * Do nothing for generic field
     */
    public static function validateProfileOptions(array $fieldProfile) {
        if (isset($fieldProfile[Profile::OPT_VALUE]) && !is_scalar($fieldProfile[Profile::OPT_VALUE])) {
            throw new CSVObjectsException("Default value must be scalar type");
        }
    }

}
