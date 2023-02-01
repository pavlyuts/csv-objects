<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 */

namespace CSVObjects\Fields;

use CSVObjects\Profile;
use CSVObjects\Exception\CSVObjectsException;

/**
 * Abstract class to implement shared functions
 *
 */
abstract class Field {

    public static function getImportFormat(array $fieldProfile) {
        return $fieldProfile[Profile::OPT_FORMAT_IMPORT] ?? $fieldProfile[Profile::OPT_FORMAT] ?? null;
    }

    public static function getExportFormat(array $fieldProfile) {
        return $fieldProfile[Profile::OPT_FORMAT_EXPORT] ?? $fieldProfile[Profile::OPT_FORMAT] ?? null;
    }

    public static function validateTimezone($fieldProfile) {
        if (isset($fieldProfile[Profile::OPT_TIMEZONE])) {
            try {
                return timezone_open($fieldProfile[Profile::OPT_TIMEZONE]);
            } catch (\Exception $e) {
                throw new CSVObjectsException("Can't create timezone from '{$fieldProfile[Profile::OPT_TIMEZONE]}'"
                                . " given for field {$fieldProfile[Profile::KEY_FIELD_NAME]}");
            }
        }
    }

    public static function getTimezone($fieldProfile) {
        return (isset($fieldProfile[Profile::OPT_TIMEZONE])) ? timezone_open($fieldProfile[Profile::OPT_TIMEZONE]) : null;
    }

}
