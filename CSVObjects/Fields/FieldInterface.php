<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects\Fields;

/**
 * Interface for custome field processing
 *
 */
interface FieldInterface {

    /**
     * Vslidate CSV data given, called on CSV data check before import
     * 
     * @param string $field - field data fromm CSV source
     * @param array $fieldProfile - field profile data array
     * 
     * @throws CSVOBjectsDataException on data is not valid
     */
    public static function validateCSV(string $field, array $fieldProfile);

    /**
     * Process field data on import from CSV
     * 
     * @param string $field - field value from CSV source
     * @param array $fieldProfile - field profile data array
     * 
     * @return mixed - the value to put into php structure as import result
     * 
     * @throws CSVOBjectsException on processing error
     */
    public static function importField(string $field, array $fieldProfile);

    /**
     * Vslidate PHP data given
     * 
     * @param string $field - field data fromm PHP data source
     * @param array $fieldProfile - field profile data array
     * 
     * @throws CSVOBjectsDataException on processing error
     */
    public static function validatePHP($field, array $fieldProfile);

    /**
     * Process field data on export to CSV
     * 
     * @param string $field - field value of PHP data field
     * @param array $fieldProfile - field profile data array
     * 
     * @return string - the value to put into CSV field
     * 
     * @throws CSVOBjectsException on processing error
     */
    public static function exportField($field, array $fieldProfile): string;

    /**
     * Check profile options are correct. Called once as profile loads
     * 
     * @param array $fieldProfile - field profile array
     * 
     * @throws CSVOBjectsException - in a case of any profile option is a bad 
     *                                 one or required option missed.
     */
    public static function validateProfileOptions(array $fieldProfile);
}
