<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 */

use CSVObjects\Profile;

$testInvoiceProfile = [
    Profile::KEY_FIELD_LIST => [
        'Invoice Nr' => [
            Profile::OPT_TYPE => Profile::TYPE_ID,
        ],
        'Date' => [
            Profile::OPT_SHARED => true,
            Profile::OPT_TYPE => Profile::TYPE_DATE,
            Profile::OPT_FORMAT_IMPORT => '!Y-m-d',
            Profile::OPT_FORMAT => 'Y-m-d',
        ],
        'Date due' => [
            Profile::OPT_SHARED => true,
            Profile::OPT_TYPE => Profile::TYPE_DATE,
            Profile::OPT_FORMAT_IMPORT => '!Y-m-d',
            Profile::OPT_FORMAT => 'Y-m-d',
        ],
        'Customer Id' => [
            Profile::OPT_SHARED => true,
            Profile::OPT_REQUIRED => true,
        ],
        'Customer name' => [
            Profile::OPT_SHARED => true,
        ],
        'Rows' => [
            Profile::OPT_TYPE => Profile::TYPE_ROWCOUNT,
        ],
        'Row' => [
            Profile::OPT_TYPE => Profile::TYPE_ROWINDEX,
        ],
        'Item Code' => [
        ],
        'Item' => [
        ],
        'Price' => [
            Profile::OPT_TYPE => Profile::TYPE_NUMBER
        ],
        'Quantity' => [
            Profile::OPT_TYPE => Profile::TYPE_NUMBER
        ],
        'Total' => [
            Profile::OPT_TYPE => Profile::TYPE_NUMBER
        ],
    ],
];

$testInvoiceCSV = <<< EOT
"Invoice Nr",Date,"Date due","Customer Id","Customer name",Rows,Row,"Item Code",Item,Price,Quantity,Total
586,2022-10-11,2022-11-11,543,"Acme Inc.",3,1,386,"Incredible Machine",125,3,375
586,2022-10-11,2022-11-11,543,"Acme Inc.",3,2,328,"Green baloon",5,12,60
586,2022-10-11,2022-11-11,543,"Acme Inc.",3,3,542,"Dream recorder",1250,1,1250
498,2022-05-12,2022-06-12,324,"Gandalf the Grey",2,1,124,"Majic ball",7876,1,7876
498,2022-05-12,2022-06-12,324,"Gandalf the Grey",2,2,496,"Gray hat",150,1,150
G-345,2022-03-07,2022-04-07,Fro-1,"Froddo Baggins",3,1,666,"Ring of power",0,1,0
G-345,2022-03-07,2022-04-07,Fro-1,"Froddo Baggins",3,2,542,"Dream recorder",1250,1,1250
G-345,2022-03-07,2022-04-07,Fro-1,"Froddo Baggins",3,3,387,"Travel bag",40,1,40

EOT;

$testInvoiceProcessed = <<< EOT
array (
  586 => 
  array (
    'id' => '586',
    'fields' => 
    array (
      'Date' => 
      DateTime::__set_state(array(
         'date' => '2022-10-11 00:00:00.000000',
         'timezone_type' => 3,
         'timezone' => 'UTC',
      )),
      'Date due' => 
      DateTime::__set_state(array(
         'date' => '2022-11-11 00:00:00.000000',
         'timezone_type' => 3,
         'timezone' => 'UTC',
      )),
      'Customer Id' => '543',
      'Customer name' => 'Acme Inc.',
      'Rows' => '3',
    ),
    'rows' => 
    array (
      1 => 
      array (
        'Row' => '1',
        'Item Code' => '386',
        'Item' => 'Incredible Machine',
        'Price' => 125,
        'Quantity' => 3,
        'Total' => 375,
      ),
      2 => 
      array (
        'Row' => '2',
        'Item Code' => '328',
        'Item' => 'Green baloon',
        'Price' => 5,
        'Quantity' => 12,
        'Total' => 60,
      ),
      3 => 
      array (
        'Row' => '3',
        'Item Code' => '542',
        'Item' => 'Dream recorder',
        'Price' => 1250,
        'Quantity' => 1,
        'Total' => 1250,
      ),
    ),
  ),
  498 => 
  array (
    'id' => '498',
    'fields' => 
    array (
      'Date' => 
      DateTime::__set_state(array(
         'date' => '2022-05-12 00:00:00.000000',
         'timezone_type' => 3,
         'timezone' => 'UTC',
      )),
      'Date due' => 
      DateTime::__set_state(array(
         'date' => '2022-06-12 00:00:00.000000',
         'timezone_type' => 3,
         'timezone' => 'UTC',
      )),
      'Customer Id' => '324',
      'Customer name' => 'Gandalf the Grey',
      'Rows' => '2',
    ),
    'rows' => 
    array (
      4 => 
      array (
        'Row' => '1',
        'Item Code' => '124',
        'Item' => 'Majic ball',
        'Price' => 7876,
        'Quantity' => 1,
        'Total' => 7876,
      ),
      5 => 
      array (
        'Row' => '2',
        'Item Code' => '496',
        'Item' => 'Gray hat',
        'Price' => 150,
        'Quantity' => 1,
        'Total' => 150,
      ),
    ),
  ),
  'G-345' => 
  array (
    'id' => 'G-345',
    'fields' => 
    array (
      'Date' => 
      DateTime::__set_state(array(
         'date' => '2022-03-07 00:00:00.000000',
         'timezone_type' => 3,
         'timezone' => 'UTC',
      )),
      'Date due' => 
      DateTime::__set_state(array(
         'date' => '2022-04-07 00:00:00.000000',
         'timezone_type' => 3,
         'timezone' => 'UTC',
      )),
      'Customer Id' => 'Fro-1',
      'Customer name' => 'Froddo Baggins',
      'Rows' => '3',
    ),
    'rows' => 
    array (
      6 => 
      array (
        'Row' => '1',
        'Item Code' => '666',
        'Item' => 'Ring of power',
        'Price' => 0,
        'Quantity' => 1,
        'Total' => 0,
      ),
      7 => 
      array (
        'Row' => '2',
        'Item Code' => '542',
        'Item' => 'Dream recorder',
        'Price' => 1250,
        'Quantity' => 1,
        'Total' => 1250,
      ),
      8 => 
      array (
        'Row' => '3',
        'Item Code' => '387',
        'Item' => 'Travel bag',
        'Price' => 40,
        'Quantity' => 1,
        'Total' => 40,
      ),
    ),
  ),
)
EOT;

$testInvoiceCSVStringBroken = <<< EOT
Invoice Nr,Date,Date due,Customer Id,Customer name,Rows,Row,Item Code,Item,Price,Quantity,Total
586,2022-10-11,2022-11-11,543,Acme Inc.,3,1,386,Incredible Machine,125,3,375
586,2022-10-11,2022-11-11,543,Acme Inc.,3,2,328,Green baloon,5,12,60
586,2022-10-11,2022-11-11,543,Acme Inc.,3,3,542,Dream recorder,1,1250
,2022-05-12,2022-06-12,324,Gandalf the Grey,2,1,124,Majic ball,7876,1,7876
498,2022-05-12,2022-06-12,324,Gandalf the Grey,2,2,496,Gray hat,150,1,150
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,1,NotNumber,Ring of power,0,1,0
G-345,2022-03-07,2022-04-07,Fro-1,Froddo,3,2,542,Dream recorder,1250,1,1250
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,3,387,Travel bag,40,1,40
EOT;

$testInvoiceCSVStringBrokenNoIdent = [4];

$testInvoiceCSVStringBrokenEntities = array(
    0 =>
    array(
        'id' => '586',
        'rows' =>
        array(
            0 => 1,
            1 => 2,
            2 => 3,
        ),
        'error' => 'Field count in row 3 less then header column count',
    ),
    1 =>
    array(
        'id' => '498',
        'rows' =>
        array(
            0 => 5,
        ),
        'error' => 'Row count field is 2, but 1 found',
    ),
    2 =>
    array(
        'id' => 'G-345',
        'rows' =>
        array(
            0 => 6,
            1 => 7,
            2 => 8,
        ),
        'error' => 'Shared field must have same value, failed for fields \'Customer name\'',
    ),
);

$testInvoiceCSVWrongOrder = <<< EOT
Invoice Nr,Date due,Date,Customer Id,Customer name,Rows,Row,Item Code,Item,Price,Quantity,Total
586,2022-10-11,2022-11-11,543,Acme Inc.,3,1,386,Incredible Machine,125,3,375
586,2022-10-11,2022-11-11,543,Acme Inc.,3,2,328,Green baloon,5,12,60
586,2022-10-11,2022-11-11,543,Acme Inc.,3,3,542,Dream recorder,1250,1,1250
498,2022-05-12,2022-06-12,324,Gandalf the Grey,2,1,124,Majic ball,7876,1,7876
498,2022-05-12,2022-06-12,324,Gandalf the Grey,2,2,496,Gray hat,150,1,150
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,1,666,Ring of power,0,1,0
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,2,542,Dream recorder,1250,1,1250
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,3,387,Travel bag,40,1,40
EOT;

$testInvoiceCSVMissReqired = <<< EOT
Invoice Nr,Date,Date due,Customer Nr,Customer name,Rows,Row,Item Code,Item,Price,Quantity,Total
586,2022-10-11,2022-11-11,543,Acme Inc.,3,1,386,Incredible Machine,125,3,375
586,2022-10-11,2022-11-11,543,Acme Inc.,3,2,328,Green baloon,5,12,60
586,2022-10-11,2022-11-11,543,Acme Inc.,3,3,542,Dream recorder,1250,1,1250
EOT;

$testInvoiceMissedShared = <<< EOT
Invoice Nr,Date,Another,Customer Id,Customer name,Rows,Row,Item Code,Item,Price,Quantity,Total
586,2022-10-11,2022-11-11,543,Acme Inc.,3,1,386,Incredible Machine,125,3,375
586,2022-10-11,2022-11-11,543,Acme Inc.,3,2,328,Green baloon,5,12,60
586,2022-10-11,2022-11-11,543,Acme Inc.,3,3,542,Dream recorder,1250,1,1250
498,2022-05-12,2022-06-12,324,Gandalf the Grey,2,1,124,Majic ball,7876,1,7876
498,2022-05-12,2022-06-12,324,Gandalf the Grey,2,2,496,Gray hat,150,1,150
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,1,666,Ring of power,0,1,0
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,2,542,Dream recorder,1250,1,1250
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,3,387,Travel bag,40,1,40
EOT;

$testInvoiceCSVDataBugs = <<< EOT
Invoice Nr,Date,Date due,Customer Id,Customer name,Rows,Row,Item Code,Item,Price,Quantity,Total
586,2022-10-11,NoData,543,Acme Inc.,3,1,386,Incredible Machine,125,,375
586,2022-10-11,NoData,543,Acme Inc.,3,2,328,Green baloon,5,12,60
586,2022-10-11,NoData,543,Acme Inc.,3,3,542,Dream recorder,1250,1,1250
498,2022-05-12,2022-06-12,324,Gandalf the Grey,2,1,124,Majic ball,NotANum,1,7876
498,2022-05-12,2022-06-12,324,Gandalf the Grey,2,2,496,Gray hat,150,1,150
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,1,666,Ring of power,0,1,0
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,2,542,Dream recorder,1250,1,1250
G-345,2022-03-07,2022-04-07,Fro-1,Froddo Baggins,3,3,387,Travel bag,40,1,40
EOT;

$testInvoiceCSVDataBugsEtities = array(
    0 =>
    array(
        'id' => '586',
        'rows' =>
        array(
            0 => 1,
            1 => 2,
            2 => 3,
        ),
        'error' => 'Can\'t convert field \'Date due\' to Datetime object with format string \'Y-m-d\'',
    ),
    1 =>
    array(
        'id' => '498',
        'rows' =>
        array(
            0 => 4,
            1 => 5,
        ),
        'error' => 'Data value of field \'Price\' is not a number',
    ),
);

$entityData = array(
    586 =>
    array(
        'id' => '586',
        'fields' =>
        array(
            'Date' => new \DateTime('2022-10-11 00:00:00.000000'),
            'Date due' => new \DateTime('2022-11-11 00:00:00.000000'),
            'Customer Id' => '543',
            'Customer name' => 'Acme Inc.',
            'Rows' => '3',
        ),
        'rows' =>
        array(
            1 =>
            array(
                'Row' => '1',
                'Item Code' => '386',
                'Item' => 'Incredible Machine',
                'Price' => 125,
                'Quantity' => 3,
                'Total' => 375,
            ),
            2 =>
            array(
                'Row' => '2',
                'Item Code' => '328',
                'Item' => 'Green baloon',
                'Price' => 5,
                'Quantity' => 12,
                'Total' => 60,
            ),
            3 =>
            array(
                'Row' => '3',
                'Item Code' => '542',
                'Item' => 'Dream recorder',
                'Price' => 1250,
                'Quantity' => 1,
                'Total' => 1250,
            ),
        ),
    ),
    498 =>
    array(
        'id' => '498',
        'fields' =>
        array(
            'Date' => new \DateTime('2022-05-12 00:00:00.000000'),
            'Date due' => new \DateTime('2022-06-12 00:00:00.000000'),
            'Customer Id' => '324',
            'Customer name' => 'Gandalf the Grey',
            'Rows' => '2',
        ),
        'rows' =>
        array(
            4 =>
            array(
                'Row' => '1',
                'Item Code' => '124',
                'Item' => 'Majic ball',
                'Price' => 7876,
                'Quantity' => 1,
                'Total' => 7876,
            ),
            5 =>
            array(
                'Row' => '2',
                'Item Code' => '496',
                'Item' => 'Gray hat',
                'Price' => 150,
                'Quantity' => 1,
                'Total' => 150,
            ),
        ),
    ),
    'G-345' =>
    array(
        'id' => 'G-345',
        'fields' =>
        array(
            'Date' => new \DateTime('2022-03-07 00:00:00.000000'),
            'Date due' => new \DateTime('2022-04-07 00:00:00.000000'),
            'Customer Id' => 'Fro-1',
            'Customer name' => 'Froddo Baggins',
            'Rows' => '3',
        ),
        'rows' =>
        array(
            6 =>
            array(
                'Row' => '1',
                'Item Code' => '666',
                'Item' => 'Ring of power',
                'Price' => 0,
                'Quantity' => 1,
                'Total' => 0,
            ),
            7 =>
            array(
                'Row' => '2',
                'Item Code' => '542',
                'Item' => 'Dream recorder',
                'Price' => 1250,
                'Quantity' => 1,
                'Total' => 1250,
            ),
            8 =>
            array(
                'Row' => '3',
                'Item Code' => '387',
                'Item' => 'Travel bag',
                'Price' => 40,
                'Quantity' => 1,
                'Total' => 40,
            ),
        ),
    ),
);
