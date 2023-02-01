<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects\Fields;

use CSVObjects\Profile;
use CSVObjects\Exception\CSVObjectsDataException;

/**
 * Custom class for handle numeric fields
 *
 */
class Number extends Generic {

    /**
     * @inheritDoc
     */
    public static function exportField($field, array $fieldProfile): string {
        return (string)$field;
    }

    /**
     * @inheritDoc
     */
    public static function importField(string $field, array $fieldProfile) {
        return 0 + $field;
    }

    /**
     * @inheritDoc
     */
    public static function validateCSV(string $field, array $fieldProfile) {
        static::validateNumber($field, $fieldProfile);
    }

    /**
     * @inheritDoc
     */
    public static function validatePHP($field, array $fieldProfile) {
        static::validateNumber($field, $fieldProfile);
    }

    protected static function validateNumber($field, array $fieldProfile) {
        if (!is_numeric($field)) {
            throw new CSVObjectsDataException("Data value of field '{$fieldProfile[Profile::KEY_FIELD_NAME]}' is not a number");
        }
    }

}
