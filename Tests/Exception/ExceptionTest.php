<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVTests\Exception;

use CSVObjects\Exception\CSVObjectsDataException;

/**
 * Exception code test 
 *
 */
class ExceptionTest extends \PHPUnit\Framework\TestCase {

    protected $message = "Test message";
    protected $data = [
        'el1' => 'data1',
        'el2' => 'data2',
    ];

    public function testExceptions_1() {
        try {
            throw new CSVObjectsDataException($this->message);
        } catch (CSVObjectsDataException $ex) {
            $this->assertEquals($this->message, $ex->getMessage());
            return;
        }
        $this->fail("How it could be? Itwon't throw!");
    }

    public function testExceptions_2() {
        try {
            throw new CSVObjectsDataException($this->message, $this->data);
        } catch (CSVObjectsDataException $ex) {
            $this->assertEquals($this->message, $ex->getMessage());
            $this->assertEquals($this->data, $ex->getProblems());
            return;
        }
        $this->fail("How it could be? Itwon't throw!");
    }

}
