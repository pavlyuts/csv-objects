<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 */

namespace CSVTests;

use CSVObjects\Profile;
use CSVObjects\Exception\CSVObjectsException;

/**
 * Test class for Profile
 *
 */
class ProfileTest extends \PHPUnit\Framework\TestCase {

    public function testNormalProfile() {
        require __DIR__ . '/TestData/ProfileTestData.php';
        $profile = new Profile($testProfileBasic);
        $this->assertTrue($profile instanceof Profile);
        $this->assertEquals($testProfileBasicAnswer, (array) $profile);

        $this->assertFalse($profile->isStrict());
        $this->assertEquals($testProfileBasicAnswer["\0*\0entityFieldList"], $profile->getEntityFieldList());
        $this->assertEquals(\CSVObjects\Fields\CustomInvertNumber::class, $profile->getFieldClass('FieldCustom'));
        $this->assertEquals(\CSVObjects\Fields\Generic::class, $profile->getFieldClass('FieldDefault'));
        $this->assertEquals($testProfileBasicAnswer["\0*\0profiles"]['FieldRowCount'], $profile->getFieldProfile('FieldRowCount'));
        $this->assertEquals($testProfileBasicAnswer["\0*\0profiles"], $profile->getFieldProfiles());
        $this->assertEquals('FieldId', $profile->getIdField());
        $this->assertEquals(array_keys($testProfileBasicAnswer["\0*\0profiles"]), $profile->getProfileFieldList());
        $this->assertEquals(['FieldRequired', 'FieldId'], $profile->getRequiredFieldList());
        $this->assertEquals($testProfileBasicAnswer["\0*\0rowFieldList"], $profile->getRowFieldList());
        $this->assertEquals(
                ['FieldDefault', 'FieldShared', 'FieldRequired', 'FieldText'],
                $profile->getFieldListByType(Profile::TYPE_TEXT));
        $this->assertEquals(['FieldDate'], $profile->getFieldListByType(Profile::TYPE_DATE));
        $this->assertEquals([], $profile->getFieldListByType('Unknown'));

        $this->assertEquals('FieldRowCount', $profile->getRowCountFieldName());
        $this->assertEquals('FieldRowIndex', $profile->getRowIndexFieldName());
        $this->assertEquals(\CSVObjects\Entity::class, $profile->getTargetClass([]));

        return $profile;
    }

    /**
     * @depends testNormalProfile
     */
    public function testExceptionUnknownFieldClass(Profile $profile) {
        $this->expectException(CSVObjectsException::class);
        $profile->getFieldClass('Unknown');
    }

    /**
     * @depends testNormalProfile
     */
    public function testExceptionUnknownFieldProfile(Profile $profile) {
        $this->expectException(CSVObjectsException::class);
        $profile->getFieldProfile('Unknown');
    }

    public function testStrictProfile() {
        require __DIR__ . '/TestData/ProfileTestData.php';
        $prf = $testProfileBasic;
        $prf[Profile::OPT_STRICT] = true;

        $profile = new Profile($prf);
        $this->assertTrue($profile->isStrict());
    }

    public function testExceptionEmptyProfile() {
        $this->expectException(CSVObjectsException::class);
        $p = new Profile([]);
    }

    public function testExceptionNoId() {
        $this->expectException(CSVObjectsException::class);
        $p = new Profile([Profile::KEY_FIELD_LIST => []]);
    }

    public function testExceptionDoubleId() {
        $this->expectException(CSVObjectsException::class);
        $p = new Profile([
            Profile::KEY_FIELD_LIST => [
                'Field1' => [
                    Profile::OPT_TYPE => Profile::TYPE_ID,
                ],
                'Field2' => [
                    Profile::OPT_TYPE => Profile::TYPE_ID,
                ],
            ]
        ]);
    }

    public function testExceptionDoubleRowCount() {
        $this->expectException(CSVObjectsException::class);
        $p = new Profile([
            Profile::KEY_FIELD_LIST => [
                'Field1' => [
                    Profile::OPT_TYPE => Profile::TYPE_ROWCOUNT,
                ],
                'Field2' => [
                    Profile::OPT_TYPE => Profile::TYPE_ROWCOUNT,
                ],
            ]
        ]);
    }

    public function testExceptionDoubleRowIndex() {
        $this->expectException(CSVObjectsException::class);
        $p = new Profile([
            Profile::KEY_FIELD_LIST => [
                'Field1' => [
                    Profile::OPT_TYPE => Profile::TYPE_ROWINDEX,
                ],
                'Field2' => [
                    Profile::OPT_TYPE => Profile::TYPE_ROWINDEX,
                ],
            ]
        ]);
    }

    public function testExceptionCustomClassMissed() {
        $this->expectException(CSVObjectsException::class);
        $p = new Profile([
            Profile::KEY_FIELD_LIST => [
                'Field1' => [
                    Profile::OPT_TYPE => Profile::TYPE_CUSTOM,
                ],
            ]
        ]);
    }

    public function testExceptionCustomClassBad() {
        $this->expectException(CSVObjectsException::class);
        $p = new Profile([
            Profile::KEY_FIELD_LIST => [
                'Field1' => [
                    Profile::OPT_TYPE => Profile::TYPE_CUSTOM,
                    Profile::OPT_CLASS => Profile::class,
                ],
            ]
        ]);
    }

    public function testValidateId() {
        $this->assertEquals('TestString', Profile::validateId('TestString'));
        $this->assertEquals(787, Profile::validateId(787));
        $this->assertEquals(3.14, Profile::validateId(3.14));
        $this->expectException(\CSVObjects\Exception\CSVObjectsDataException::class);
        Profile::validateId([]);
    }

}
