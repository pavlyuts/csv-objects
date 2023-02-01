<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * Entity test datasets
 * (c) Alexey Pavlyuts
 */

$testEntityId = 'TestEntityId';

$testEntityFields = [
    'EntityFieldText' => 'EntityFieldTextContent',
    'EntityFieldDate' => new DateTime('now'),
    'EntityFieldInt' => 1972,
    'EntityFieldFloat' => 20.23,
];

$testEntityRows = [
    [
        'RowFieldText' => 'Row0FieldTextContent',
        'RowFieldDate' => new DateTime('1972-05-12 05:00'),
        'RowFieldInt' => 103,
        'RowFieldFloat' => 10.3,
        'RowFieldMixed' => 'Row0FieldMixedContent',
        
    ],
    [
        'RowFieldText' => 'Row1FieldTextContent',
        'RowFieldDate' => (new \DateTime('1972-05-12 05:00'))->add(new \DateInterval('P1D')),
        'RowFieldInt' => 203,
        'RowFieldFloat' => 20.3,
        'RowFieldMixed' => 203,
    ],
    [
        'RowFieldText' => 'Row2FieldTextContent',
        'RowFieldDate' => (new \DateTime('1972-05-12 05:00'))->add(new \DateInterval('P2D')),
        'RowFieldInt' => 303,
        'RowFieldFloat' => 30.3,
        'RowFieldMixed' => 30.3,
    ],
];