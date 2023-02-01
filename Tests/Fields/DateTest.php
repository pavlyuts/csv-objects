<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVTests\Fields;

use CSVObjects\Profile;
use CSVObjects\Fields\Date;
use CSVObjects\Exception\CSVObjectsException;
use CSVObjects\Exception\CSVObjectsDataException;

class DateTest extends \PHPUnit\Framework\TestCase {

    protected $profile = [
        Profile::KEY_FIELD_NAME => 'TestFieldName',
        Profile::OPT_FORMAT => 'Y-m-d H:i',
        Profile::OPT_TIMEZONE => '+05:00',
    ];
    protected $testdate = '2023-02-06 13:49';
    protected $testdateBroken = 'not data';

    public function testValidatePHP() {
        $this->assertNull(Date::validatePHP(new \DateTime('now'), $this->profile));
    }

    public function testValidatePHP_1() {
        $this->expectException(CSVObjectsDataException::class);
        $this->assertNull(Date::validatePHP('', $this->profile));
    }

    public function testExportField() {
        $prf = $this->profile;
        unset($prf[Profile::OPT_TIMEZONE]);
        $now = new \DateTime('now');
        $this->assertEquals($now->format($this->profile[Profile::OPT_FORMAT]), Date::exportField($now, $prf));
        $exp = (clone $now);
        $exp->setTimezone(new \DateTimeZone($this->profile[Profile::OPT_TIMEZONE]));
        $this->assertEquals($exp->format($this->profile[Profile::OPT_FORMAT]), Date::exportField($now, $this->profile));
    }

    public function testImportField() {

        $prf = $this->profile;
        date_default_timezone_set('America/Santo_Domingo');
        $now = new \DateTime('now', new \DateTimeZone('America/Santo_Domingo'));

        $d = Date::importField($this->testdate, $this->profile);
        $this->assertTrue($d instanceof \DateTime);
        $this->assertEquals($this->testdate, $d->format($this->profile[Profile::OPT_FORMAT]));
        $this->assertEquals(18000, $d->getOffset());
        $d = null;

        unset($prf[Profile::OPT_TIMEZONE]);
        $d = Date::importField($this->testdate, $prf);
        $this->assertTrue($d instanceof \DateTime);
        $this->assertEquals($this->testdate, $d->format($this->profile[Profile::OPT_FORMAT]));
        $this->assertEquals($now->getOffset(), $d->getOffset());
    }

    public function testImportField_1() {
        $this->expectException(CSVObjectsDataException::class);
        $d = Date::importField($this->testdateBroken, $this->profile);
    }

    public function testImportField_2() {
        $prf = $this->profile;
        unset($prf[Profile::OPT_FORMAT]);
        $this->expectException(CSVObjectsDataException::class);
        $d = Date::importField($this->testdateBroken, $this->profile);
    }

    public function testValidateCSV() {
        $this->assertNull(Date::validateCSV($this->testdate, $this->profile));
    }

    public function testValidateCSV_1() {
        $this->expectException(CSVObjectsDataException::class);
        $this->assertNull(Date::validateCSV($this->testdateBroken, $this->profile));
    }

    public function testValidateProfileOptions() {

        $prf = $this->profile;
        $this->assertNull(Date::validateProfileOptions($prf));
        unset($prf[Profile::OPT_TIMEZONE]);
        $this->assertNull(Date::validateProfileOptions($prf));
    }

    public function testValidateProfileOptions_1() {
        $this->expectException(CSVObjectsException::class);
        $this->assertNull(Date::validateProfileOptions(array_merge($this->profile, [Profile::OPT_TIMEZONE => 'Eur/Ams'])));
    }

    public function testValidateProfileOptions_2() {
        $prf = $this->profile;
        unset($prf[Profile::OPT_FORMAT]);
        unset($prf[Profile::OPT_FORMAT_IMPORT]);
        unset($prf[Profile::OPT_FORMAT_EXPORT]);
        $this->expectException(CSVObjectsException::class);
        $this->assertNull(Date::validateProfileOptions($prf));
    }

}
