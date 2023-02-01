<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 */

namespace CSVTests;

use CSVObjects\Collection;
use CSVObjects\Profile;
use CSVObjects\Entity;
use CSVObjects\Exception\CSVObjectsException;
use CSVObjects\Exception\CSVObjectsDataException;
use CSVObjects\Exception\CSVObjectsStrictException;

/**
 * Class to test collections
 *
 */
class CollectionTest extends \PHPUnit\Framework\TestCase {

    public function testInvoiceCSV() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $profile = new Profile($testInvoiceProfile);

        //Test normal
        $c = new Collection($profile);
        $c->createFromCSVFile(__DIR__ . '/TestData/invoices.csv');
        $this->assertEquals(3, count($c));
        $this->assertEquals($testInvoiceProcessed, $this->getCollectionResuts($c));

        //Test broken #1
        $c = new Collection($profile);
        $c->createFromCSVString($testInvoiceCSVStringBroken);
        $this->assertEquals(0, count($c));
        $this->assertEquals($testInvoiceCSVStringBrokenNoIdent, $c->getRowsWithoutIdentity());
        $this->assertEquals($testInvoiceCSVStringBrokenEntities, $c->getBrokenEntities());

        //Test missed shared
        $c = new Collection($profile);
        $c->createFromCSVString($testInvoiceMissedShared);
        $this->assertEquals(3, count($c));

        //Test broken fields
        $c = new Collection($profile);
        $c->createFromCSVString($testInvoiceCSVDataBugs);
        $this->assertEquals(1, count($c));
        $this->assertEquals($testInvoiceCSVDataBugsEtities, $c->getBrokenEntities());

        return new Collection($profile);
    }

    /**
     * @depends testInvoiceCSV 
     */
    public function testRequiredFieldExcepion(Collection $c) {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $this->expectException(CSVObjectsDataException::class);
        $c->createFromCSVString($testInvoiceCSVMissReqired);
    }

    public function testInvoiceStrictProvider() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $profile = new Profile(array_merge($testInvoiceProfile, [Profile::OPT_STRICT => true]));
        $c = new Collection($profile);
        $c->createFromCSVString($testInvoiceCSV);
        $this->assertEquals(3, count($c));
        return new Collection($profile);
    }

    /**
     * @depends testInvoiceStrictProvider
     */
    public function testExceptionStrictProfile(Collection $c) {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $this->expectException(CSVObjectsStrictException::class);
        $c->createFromCSVString($testInvoiceCSVWrongOrder);
    }

    public function testSampleEntity() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $profile = new Profile($testInvoiceProfile);
        $list = [];
        $c = new Collection($profile);
        foreach ($entityData as $data) {
            $e = new \CSVObjects\Entity();
            $e->setFromPHP($data['id'], $data['fields'], $data['rows']);
            $c[] = $e;
        }
        $this->assertEquals(3, count($c));
        $c = new Collection($profile);
        $c->createFromPHPCustomArray($entityData);
        $this->assertEquals(3, count($c));
    }

    public function testColectionWrongClass() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $c = new Collection(new Profile($testInvoiceProfile));
        $this->expectException(CSVObjectsException::class);
        $c->createFromPHPCustomArray([], '\UnknownClass');
    }

    public function testColectionNullEntityId() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $c = new Collection(new Profile($testInvoiceProfile));
        $e = new Entity();
        $this->expectException(CSVObjectsDataException::class);
        $c[] = $e;
    }

    public function testColectionNotEntityException() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $c = new Collection(new Profile($testInvoiceProfile));
        $s = new \stdClass();
        $this->expectException(CSVObjectsException::class);
        $c[] = $s;
    }

    public function testDifferentEntityIdsException() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $c = new Collection(new Profile($testInvoiceProfile));
        $e = new Entity();
        $e->setFromCustomData($entityData['G-345']);
        $this->expectException(CSVObjectsException::class);
        $c['DifferentKey'] = $e;
    }

    public function testProcessorValidatePHPRowsFieldException() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $c = new Collection(new Profile(array_merge($testInvoiceProfile, [Profile::OPT_STRICT => true])));
        $data = $entityData['G-345'];
        unset($data['rows'][7]['Item']);
        $e = new Entity();
        $e->setFromCustomData($data);
        $this->expectException(CSVObjectsDataException::class);
        $c[] = $e;
    }

    public function testExport() {
        require __DIR__ . '/TestData/InvoiceTestData.php';
        $profile = new Profile($testInvoiceProfile);
        $c = new Collection($profile);
        $c->createFromPHPCustomArray($entityData);
        $this->assertEquals(3, count($c));
        $this->assertEquals($testInvoiceCSV, $c->exportCSVString());
        $c->exportCSVFile(__DIR__.'/TestData/exportoutput.csv');
        $this->assertEquals($testInvoiceCSV, file_get_contents(__DIR__.'/TestData/exportoutput.csv'));
    }

    protected function getCollectionResuts(Collection $c) {
        $answer = [];
        foreach ($c as $key => $e) {
            $answer[$key] = ['id' => $e->getId(), 'fields' => $e->getFields(), 'rows' => $e->getRows()];
        }
        return var_export($answer, true);
    }

}
