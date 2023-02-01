<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 */

namespace CSVTests;

use CSVObjects\Entity;
use CSVObjects\Exception\CSVObjectsDataException;

/**
 * Entity test class
 */
class EntityTest extends \PHPUnit\Framework\TestCase {

    public function testEntityMain() {
        require __DIR__ . '/TestData/EntityData.php';
        $e = new Entity;
        $e->setFromCSV($testEntityId, $testEntityFields, $testEntityRows);

        //getId
        $this->assertEquals($testEntityId, $e->getId());

        //getFields
        $this->assertEquals($testEntityFields, $e->getFields());

        //getRows
        $this->assertEquals($testEntityRows, $e->getRows());

        //getField
        $this->assertEquals($testEntityFields['EntityFieldText'], $e->getField('EntityFieldText'));
        $this->assertEquals($testEntityFields['EntityFieldDate'], $e->getField('EntityFieldDate'));
        $this->assertEquals($testEntityFields['EntityFieldInt'], $e->getField('EntityFieldInt'));
        $this->assertEquals($testEntityFields['EntityFieldFloat'], $e->getField('EntityFieldFloat'));

        //getSum
        $this->assertEquals(609, $e->getSum('RowFieldInt'));
        $this->assertEqualsWithDelta(60.9, $e->getSum('RowFieldFloat'), 10e-12);
        $this->assertEquals(0, $e->getSum('RowFieldText'));
        $this->assertEquals(0, $e->getSum('RowFieldDate'));
        $this->assertEqualsWithDelta(233.3, $e->getSum('RowFieldMixed'), 10e-12);
        $this->assertEquals(0, $e->getSum('UnknownField'));

        $e = new Entity;
        $e->setFromPHP($testEntityId, $testEntityFields, $testEntityRows);

        //getId
        $this->assertEquals($testEntityId, $e->getId());

        //getFields
        $this->assertEquals($testEntityFields, $e->getFields());

        //getRows
        $this->assertEquals($testEntityRows, $e->getRows());

        //setField
        $e->setField('EntityFieldText', 'NewValue');
        $this->assertEquals('NewValue', $e->getField('EntityFieldText'));
        $e->setField('EntityFieldText', 333);
        $this->assertEquals(333, $e->getField('EntityFieldText'));
        $d = new \DateTime('now');
        $e->setField('EntityFieldText', $d);
        $this->assertEquals($d, $e->getField('EntityFieldText'));

        $e->setField('NewField', 'NewValue');
        $this->assertEquals('NewValue', $e->getField('NewField'));

        $e->unsetField('NewField');
        $this->assertNull($e->getField('NewField'));

        //Column operations
        $this->assertEquals([103, 203, 303], $e->getColumn('RowFieldInt'));
        $e->setColumn('NewColumn', [1, 2, 3, 4, 5]);
        $this->assertEquals([1, 2, 3], array_column($e->getRows(), 'NewColumn'));
        $e->setColumn('NewColumn', [1, 2]);
        $this->assertEquals([1, 2, null], array_column($e->getRows(), 'NewColumn'));

        $e->setColumnSameValue('NewColumn', 777);
        $this->assertEquals([777, 777, 777], array_column($e->getRows(), 'NewColumn'));

        $e->unsetColumn('NewColumn');
        $this->assertEquals([], $e->getColumn('NewColumn'));

        return $e;
    }

    /**
     * @depends testEntityMain
     */
    public function testFieldException(Entity $e) {
        $this->expectException(CSVObjectsDataException::class);
        $e->setField('RowFieldInt', 1);
    }

    /**
     * @depends testEntityMain
     */
    public function testColumnException(Entity $e) {
        $this->expectException(CSVObjectsDataException::class);
        $e->setColumn('EntityFieldInt', [1, 1, 1]);
    }

    /**
     * @depends testEntityMain
     */
    public function testColumnCustomDataException_1(Entity $e) {
        $this->expectException(CSVObjectsDataException::class);
        $e->setFromCustomData(['fields' => [], 'rows' => []]);
    }

    /**
     * @depends testEntityMain
     */
    public function testColumnCustomDataException_2(Entity $e) {
        $this->expectException(CSVObjectsDataException::class);
        $e->setFromCustomData(['id' => 'id', 'rows' => []]);
    }

    /**
     * @depends testEntityMain
     */
    public function testColumnCustomDataException_3(Entity $e) {
        $this->expectException(CSVObjectsDataException::class);
        $e->setFromCustomData(['id' => 'id', 'fields' => []]);
    }

}
