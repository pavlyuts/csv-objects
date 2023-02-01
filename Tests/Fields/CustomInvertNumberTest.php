<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVTests\Fields;

use CSVObjects\Profile;
use CSVObjects\Fields\CustomInvertNumber;

class CustomInvertNumberTest extends \PHPUnit\Framework\TestCase {

    protected $profile = [
        Profile::KEY_FIELD_NAME => 'TestFieldName'
    ];

    public function testExportField() {
        $this->assertEquals('-789', CustomInvertNumber::exportField(789, $this->profile));
        $this->assertEquals('789', CustomInvertNumber::exportField(-789, $this->profile));
        $this->assertEquals('-123.65', CustomInvertNumber::exportField(123.65, $this->profile));
        $this->assertEquals('123.65', CustomInvertNumber::exportField(-123.65, $this->profile));
    }

    public function testImportField() {
        $this->assertEquals(-789, CustomInvertNumber::importField('789', $this->profile));
        $this->assertEquals(789, CustomInvertNumber::importField('-789', $this->profile));
        $this->assertEquals(-123.65, CustomInvertNumber::importField('123.65', $this->profile));
        $this->assertEquals(123.65, CustomInvertNumber::importField('-123.65', $this->profile));
    }

}
