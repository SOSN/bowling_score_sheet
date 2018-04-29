<?php
declare(strict_types=1);
require_once('./vendor/autoload.php');

/**
 * @group Tests
 * @group Tests-ArrTest
 *
 * php tests/phpunit.phar --group Tests tests/ArrTest.php
 * php tests/phpunit.phar --group Tests-ArrTest tests/ArrTest.php
 *
 */

class ArrTest extends TestBase
{

    /**
     * @dataProvider unsetElmsAndArrVals_ReturnsExpectedValue
     */
    public function unsetElmsAndArrVals_ReturnsExpectedValue_DataProvider()
    {

        return [
            //数値添字の配列から、一つ消したい要素を指定した場合、期待値を返す事。
            [
                [
                    0,1,2
                ],
                1,
                [
                    0,2
                ],
            ],

            //添字の配列から、一つ消したい要素を指定した場合、期待値を返す事。
            [
                [
                    'key0'=>0,
                    'key1'=>1,
                    'key2'=>2,
                ],
                'key1',
                [
                    0,2
                ],
            ],

            //数値添字の配列から、複数の消したい要素を指定した場合、期待値を返す事。
            [
                [
                    0,1,2,3
                ],
                [0,2],
                [
                    1,3
                ],
            ],

            //添字の配列から、複数の消したい要素を指定した場合、期待値を返す事。
            [
                [
                    'key0'=>0,
                    'key1'=>1,
                    'key2'=>2,
                    'key3'=>3,
                ],
                ['key0','key2'],
                [
                    1,3
                ],
            ]

        ];
    }

    /**
     * php tests/phpunit.phar --group Tests-ArrTest-unsetElmsAndArrVals_ReturnsExpectedValue tests/ArrTest.php
     *
     * @test
     * @group Tests-ArrTest-unsetElmsAndArrVals_ReturnsExpectedValue
     * @dataProvider unsetElmsAndArrVals_ReturnsExpectedValue_DataProvider
     *
     * @param array $arr - 消したい要素を含む配列
     * @param $key - 消したい要素のキー
     * @param array $expected - 期待値
     */
    function unsetElmsAndArrVals_ReturnsExpectedValue(array $arr,$key,array $expected){
        $actual = Arr::unsetElmsAndArrVals($arr,$key);
        $this->assertSame($expected,$actual);
    }


    /**
     * @dataProvider unsetElmsAndArrValAndArrVals_ReturnsExpectedValue
     */
    public function unsetElmsAndArrValAndArrVals_ReturnsExpectedValue_DataProvider()
    {

        return [
            //数値添字の配列から、一つ消したい要素を指定した場合、期待値を返す事。
            [
                [
                    0,1,2
                ],
                1,
                [
                    0=>0,
                    2=>2
                ],
            ],

//            //添字の配列から、一つ消したい要素を指定した場合、期待値を返す事。
            [
                [
                    'key0'=>0,
                    'key1'=>1,
                    'key2'=>2,
                ],
                'key1',
                [
                    'key0'=>0,
                    'key2'=>2,
                ],
            ],
//
            //数値添字の配列から、複数の消したい要素を指定した場合、期待値を返す事。
            [
                [
                    0,1,2,3
                ],
                [0,2],
                [
                    1=>1,
                    3=>3
                ],
            ],
//
            //添字の配列から、複数の消したい要素を指定した場合、期待値を返す事。
            [
                [
                    'key0'=>0,
                    'key1'=>1,
                    'key2'=>2,
                    'key3'=>3,
                ],
                ['key0','key2'],
                [
                    'key1'=>1,
                    'key3'=>3,
                ],
            ]

        ];
    }

    /**
     * php tests/phpunit.phar --group Tests-ArrTest-unsetElmsAndArrValAndArrVals_ReturnsExpectedValue tests/ArrTest.php
     *
     * @test
     * @group Tests-ArrTest-unsetElmsAndArrValAndArrVals_ReturnsExpectedValue
     * @dataProvider unsetElmsAndArrValAndArrVals_ReturnsExpectedValue_DataProvider
     *
     * @param array $arr - 消したい要素を含む配列
     * @param $key - 消したい要素のキー
     * @param array $expected - 期待値
     */
    function unsetElmsAndArrValAndArrVals_ReturnsExpectedValue(array $arr,$key,array $expected){
        $actual = Arr::unsetElms($arr,$key);
        $this->assertSame($expected,$actual);
    }

}