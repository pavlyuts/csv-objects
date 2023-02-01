<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVTests\Fields;

use CSVObjects\Profile;
use CSVObjects\Fields\Number;
use CSVObjects\Exception\CSVObjectsDataException;

class NumberTest extends \PHPUnit\Framework\TestCase {

    protected $profile = [
        Profile::KEY_FIELD_NAME => 'TestFieldName'
    ];

    public function testExportField() {
        $this->assertEquals('789', Number::exportField(789, $this->profile));
        $this->assertEquals('-789', Number::exportField(-789, $this->profile));
        $this->assertEquals('123.65', Number::exportField(123.65, $this->profile));
        $this->assertEquals('-123.65', Number::exportField(-123.65, $this->profile));
    }

    public function testImportField() {
        $this->assertEquals(789, Number::importField('789', $this->profile));
        $this->assertEquals(-789, Number::importField('-789', $this->profile));
        $this->assertEquals(123.65, Number::importField('123.65', $this->profile));
        $this->assertEquals(-123.65, Number::importField('-123.65', $this->profile));
    }

    public function testValidateCSV() {
        $this->assertNull(Number::validateCSV('789', $this->profile));
        $this->assertNull(Number::validateCSV('-789', $this->profile));
        $this->assertNull(Number::validateCSV('123.65', $this->profile));
        $this->assertNull(Number::validateCSV('-123.65', $this->profile));
        $this->expectException(CSVObjectsDataException::class);
        $this->assertNull(Number::validateCSV('-12 3.65', $this->profile));
    }

    public function testValidatePHP() {
        $this->assertNull(Number::validatePHP(789, $this->profile));
        $this->assertNull(Number::validatePHP(-789, $this->profile));
        $this->assertNull(Number::validatePHP(123.65, $this->profile));
        $this->assertNull(Number::validatePHP(-123.65, $this->profile));
        $this->assertNull(Number::validatePHP('-123.65', $this->profile));
        $this->expectException(CSVObjectsDataException::class);
        $this->assertNull(Number::validatePHP([], $this->profile));
        $this->fail("Testing fail");
    }

}
