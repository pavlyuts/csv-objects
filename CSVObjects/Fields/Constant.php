<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects\Fields;

use CSVObjects\Profile;
use CSVObjects\Exception\CSVObjectsException;

/**
 * Class for constant field
 *
 */
class Constant extends Generic {

    public static function exportField($field, array $fieldProfile): string {
        return $fieldProfile[Profile::OPT_VALUE];
    }

    public static function importField(string $field, array $fieldProfile) {
        return $fieldProfile[Profile::OPT_VALUE];
    }

    public static function validateProfileOptions(array $fieldProfile) {
        if (!isset($fieldProfile[Profile::OPT_VALUE])) {
            throw new CSVObjectsException("Option 'value' mandatory for the constant-type field {$fieldProfile[Profile::KEY_FIELD_NAME]}");
        }
    }

    public static function validatePHP($field, array $fieldProfile) {
        
    }

}
