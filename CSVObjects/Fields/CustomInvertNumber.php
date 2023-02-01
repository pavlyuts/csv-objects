<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects\Fields;

/**
 * Custom class to invert numbeer between CSV and PHP data
 *
 */
class CustomInvertNumber extends Number {

    /**
     * @inheritDoc
     */
    public static function exportField($field, array $fieldProfile): string {
        return (string) (- $field);
    }

    /**
     * @inheritDoc
     */
    public static function importField(string $field, array $fieldProfile) {
        return -1 * $field;
    }

}
