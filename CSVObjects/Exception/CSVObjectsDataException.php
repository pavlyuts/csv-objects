<?php

/*
 *  Profile-based multistring CSV entity handling library
 * 
 *  (c) Alexey Pavlyuts
 */

namespace CSVObjects\Exception;

/**
 * Exception to throw on data check errors which prevents from entity creation
 *
 */
class CSVObjectsDataException extends CSVObjectsException {

    protected $problems;

    public function __construct(string $message = "", array $problems = []) {
        parent::__construct($message);
        $this->problems = $problems;
    }

    public function getProblems() {
        return $this->problems;
    }

}
