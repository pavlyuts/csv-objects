<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVTests\Fields;

use CSVObjects\Profile;
use CSVObjects\Fields\Generic;
use CSVObjects\Exception\CSVObjectsException;
use CSVObjects\Exception\CSVObjectsDataException;

class GenericTest extends \PHPUnit\Framework\TestCase {

    protected $profileName = [
        Profile::KEY_FIELD_NAME => 'TestFieldName'
    ];
    protected $profileWithDef = [
        Profile::KEY_FIELD_NAME => 'TestFieldName',
        Profile::OPT_VALUE => 'TestDefaultValue',
    ];

    public function testValidatePHP() {

        $this->assertNull(Generic::validatePHP('TestValue', $this->profileName));
        $this->assertNull(Generic::validatePHP(12, $this->profileName));
        $this->assertNull(Generic::validatePHP(12.12, $this->profileName));

        $this->expectException(CSVObjectsDataException::class);
        $this->assertNull(Generic::validatePHP([], $this->profileName));
    }

    public function testExportField() {

        $this->assertEquals('TestValue', Generic::exportField('TestValue', $this->profileName));
        $this->assertEquals('', Generic::exportField(null, $this->profileName));
        $this->assertEquals('TestDefaultValue', Generic::exportField(null, $this->profileWithDef));
    }

    public function testImportField() {
        $this->assertEquals('TestValue', Generic::importField('TestValue', $this->profileName));
    }

    public function testValidateCSV() {
        $this->assertNull(Generic::validateCSV('TestValue', $this->profileName));
    }

    public function testValidateProfileOptions() {
        $this->assertNull(Generic::validateProfileOptions($this->profileName));
        $this->assertNull(Generic::validateProfileOptions($prf = $this->profileName));
        $prf[Profile::OPT_VALUE] = 12345;
        $this->assertNull(Generic::validateProfileOptions($prf));
        $prf[Profile::OPT_VALUE] = 123.45;
        $this->assertNull(Generic::validateProfileOptions($prf));
        $prf[Profile::OPT_VALUE] = [1];
        $this->expectException(CSVObjectsException::class);
        $this->assertNull(Generic::validateProfileOptions($prf));
    }

}
