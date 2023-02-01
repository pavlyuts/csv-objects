<?php

/*
 * Profile-based multistring CSV entity handling library
 * 
 * (c) Alexey Pavlyuts
 */

namespace CSVTests;

use CSVObjects\Profile;
use CSVObjects\Processor;

/**
 * Tests of basic CSV processor functons
 *
 */
class ProcessorTest extends \PHPUnit\Framework\TestCase {

    public function testConstructor() {
        $this->assertInstanceOf(
                Processor::class,
                new Processor(new Profile([Profile::KEY_FIELD_LIST => ['f1' => [Profile::OPT_TYPE => Profile::TYPE_ID]]]))
        );
    }

    public function testArrayValuesSame() {
        $a = ['a' => 'aa', 'b' => 'bb', 'c' => 'cc', 'd' => 'dd'];
        $aReorder = ['a' => 'aa', 'c' => 'cc', 'b' => 'bb', 'd' => 'dd'];
        $aMinusElem = ['a' => 'aa', 'b' => 'bb', 'd' => 'dd'];
        $aNoKeys = array_values($a);

        $bStrict = ['3', '3', '3'];
        $bTypeDiff = ['3', 3, '3'];
        $bDiff = ['3', '3', '3', '2'];

        $this->assertTrue(Processor::arrayValuesSame($bStrict));
        $this->assertTrue(Processor::arrayValuesSame($bTypeDiff));
        $this->assertFalse(Processor::arrayValuesSame($bDiff));
        $this->assertTrue(Processor::arrayValuesSameStrict($bStrict));
        $this->assertFalse(Processor::arrayValuesSameStrict($bTypeDiff));
        $this->assertFalse(Processor::arrayValuesSameStrict($bDiff));
    }

    public function testSubarrayKeys() {
        $input = [
            ['a' => 'aa', 'b' => 'bb', 'c' => 'cc', 'd' => 'dd'],
            ['aa', 'bb', 'cc', 'dd'],
            ['a' => 'aa', 'bb', 'cc', 'd' => 'dd'],
        ];
        $answer = [
            ['a', 'b', 'c', 'd'],
            [0, 1, 2, 3],
            ['a', 0, 1, 'd'],
        ];

        $this->assertTrue($answer === Processor::subarrayKeys($input));
        $input[] = 'TestScalar';
        $answer[] = null;
        $this->assertTrue($answer === Processor::subarrayKeys($input));
    }

}
