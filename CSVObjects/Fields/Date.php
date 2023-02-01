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
 * Field class for date type field
 *
 */
class Date implements FieldInterface {

    public static function exportField($field, array $fieldProfile): string {
        static::validatePHP($field, $fieldProfile);
        $date = clone $field;
        if (isset($fieldProfile[Profile::OPT_TIMEZONE])) {
            $date->setTimezone(Field::getTimezone($fieldProfile));
        }
        return $date->format(Field::getExportFormat($fieldProfile));
    }

    public static function importField(string $field, array $fieldProfile) {
        return static::createDatetime($field, $fieldProfile);
    }

    public static function validateCSV(string $field, array $fieldProfile) {
        static::createDatetime($field, $fieldProfile);
    }

    public static function validatePHP($field, array $fieldProfile) {
        if (!($field instanceof \DateTime)) {
            throw new CSVObjectsDataException("Field {$fieldProfile[Profile::KEY_FIELD_NAME]} must be an instance of \DateTime");
        }
    }

    public static function validateProfileOptions(array $fieldProfile) {
        if (is_null(Field::getImportFormat($fieldProfile)) || is_null(Field::getExportFormat($fieldProfile))) {
            throw new CSVObjectsException("Date-time format option mandatory for field {$fieldProfile[Profile::KEY_FIELD_NAME]}");
        }
        Field::validateTimezone($fieldProfile);
    }

    protected static function createDatetime(string $field, array $fieldProfile) {
        $result = \DateTime::createFromFormat(Field::getImportFormat($fieldProfile), $field, Field::getTimezone($fieldProfile));
        if (false === $result) {
            throw new CSVObjectsDataException(
                            "Can't convert field '{$fieldProfile[Profile::KEY_FIELD_NAME]}' to Datetime object "
                            . "with format string '{$fieldProfile[Profile::OPT_FORMAT]}'");
        }
        return $result;
    }

}
