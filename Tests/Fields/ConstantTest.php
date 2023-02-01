<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVTests\Fields;

use CSVObjects\Profile;
use CSVObjects\Fields\Constant;
use CSVObjects\Exception\CSVObjectsException;

class ConstantTest extends \PHPUnit\Framework\TestCase {

    const TEST_CONST = 'TestConstant';

    protected $profile = [
        Profile::KEY_FIELD_NAME => 'TestFieldName',
        Profile::OPT_VALUE => self::TEST_CONST,
    ];

    public function testExportField() {
        $this->assertEquals(self::TEST_CONST, Constant::exportField('something', $this->profile));
        $this->assertEquals(self::TEST_CONST, Constant::exportField(1234, $this->profile));
    }

    public function testImportField() {
        $this->assertEquals(self::TEST_CONST, Constant::importField('something', $this->profile));
    }

    public function testValidateProfileOptions() {
        $this->assertNull(Constant::validateProfileOptions($this->profile));
        $prf = $this->profile;
        unset($prf[Profile::OPT_VALUE]);
        $this->expectException(CSVObjectsException::class);
        $this->assertNull(Constant::validateProfileOptions($prf));
    }

    public function testValidateCSV() {
        $this->assertNull(Constant::validateCSV('something', $this->profile));
    }

    public function testValidatePHP() {
        $this->assertNull(Constant::validatePHP('something', $this->profile));
        $this->assertNull(Constant::validatePHP(12345, $this->profile));
        $this->assertNull(Constant::validatePHP([], $this->profile));
    }

}
