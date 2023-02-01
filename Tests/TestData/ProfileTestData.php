<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 * 
 * Test profiles
 */

use CSVObjects\Profile;

$testProfileBasic = [
    Profile::KEY_FIELD_LIST => [
        'FieldId' => [
            Profile::OPT_TYPE => Profile::TYPE_ID,
        ],
        'FieldRowCount' => [
            Profile::OPT_TYPE => Profile::TYPE_ROWCOUNT,
        ],
        'FieldRowIndex' => [
            Profile::OPT_TYPE => Profile::TYPE_ROWINDEX,
        ],
        'FieldDefault' => [
        ],
        'FieldShared' => [
            Profile::OPT_SHARED => true,
        ],
        'FieldRequired' => [
            Profile::OPT_REQUIRED => true,
        ],
        'FieldText' => [
            Profile::OPT_TYPE => Profile::TYPE_TEXT,
        ],
        'FieldNumber' => [
            Profile::OPT_TYPE => Profile::TYPE_NUMBER,
        ],
        'FieldDate' => [
            Profile::OPT_TYPE => Profile::TYPE_DATE,
            Profile::OPT_FORMAT => 'y-m-d',
        ],
        'FieldConst' => [
            Profile::OPT_TYPE => Profile::TYPE_CONST,
            Profile::OPT_VALUE => 'DefValue',
        ],
        'FieldCustom' => [
            Profile::OPT_TYPE => Profile::TYPE_CUSTOM,
            Profile::OPT_CLASS => \CSVObjects\Fields\CustomInvertNumber::class,
        ]
    ]
];

$testProfileBasicAnswer = [
    "\0*\0strict" => false,
    "\0*\0profiles" =>
    [
        'FieldId' =>
        [
            'type' => 'id',
            'fieldName' => 'FieldId',
            'class' => 'CSVObjects\\Fields\\Generic',
        ],
        'FieldRowCount' =>
        [
            'type' => 'rowCount',
            'fieldName' => 'FieldRowCount',
            'class' => 'CSVObjects\\Fields\\Generic',
        ],
        'FieldRowIndex' =>
        [
            'type' => 'rowIndex',
            'fieldName' => 'FieldRowIndex',
            'class' => 'CSVObjects\\Fields\\Generic',
        ],
        'FieldDefault' =>
        [
            'fieldName' => 'FieldDefault',
            'class' => 'CSVObjects\\Fields\\Generic',
        ],
        'FieldShared' =>
        [
            'shared' => true,
            'fieldName' => 'FieldShared',
            'class' => 'CSVObjects\\Fields\\Generic',
        ],
        'FieldRequired' =>
        [
            'required' => true,
            'fieldName' => 'FieldRequired',
            'class' => 'CSVObjects\\Fields\\Generic',
        ],
        'FieldText' =>
        [
            'type' => 'text',
            'fieldName' => 'FieldText',
            'class' => 'CSVObjects\\Fields\\Generic',
        ],
        'FieldNumber' =>
        [
            'type' => 'number',
            'fieldName' => 'FieldNumber',
            'class' => 'CSVObjects\\Fields\\Number',
        ],
        'FieldDate' =>
        [
            'type' => 'date',
            'format' => 'y-m-d',
            'fieldName' => 'FieldDate',
            'class' => 'CSVObjects\\Fields\\Date',
        ],
        'FieldConst' =>
        [
            'type' => 'const',
            'value' => 'DefValue',
            'fieldName' => 'FieldConst',
            'class' => 'CSVObjects\\Fields\\Constant',
        ],
        'FieldCustom' =>
        [
            'type' => 'custom',
            'class' => 'CSVObjects\\Fields\\CustomInvertNumber',
            'fieldName' => 'FieldCustom',
        ],
    ],
    "\0*\0idFieldName" => 'FieldId',
    "\0*\0entityFieldList" => [
        0 => 'FieldRowCount',
        1 => 'FieldShared',
    ],
    "\0*\0rowFieldList" =>
    [0 => 'FieldRowIndex',
        1 => 'FieldDefault',
        2 => 'FieldRequired',
        3 => 'FieldText',
        4 => 'FieldNumber',
        5 => 'FieldDate',
        6 => 'FieldConst',
        7 => 'FieldCustom',
    ],
    "\0*\0rowCountFieldName" => 'FieldRowCount',
    "\0*\0rowIndexFieldName" => 'FieldRowIndex',
    "\0*\0targetClass" => 'CSVObjects\Entity',
];
