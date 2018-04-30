<?php
declare(strict_types=1);
require('vendor/autoload.php');
//use App\BowlingScoreSheet;
//use TestBase;
//require_once('BowlingScoreSheet.php');
//require_once('TestBase.php');

/**
 * @group BowlingScoreSheetTest
 */

/**
 * @group Tests
 * @group Tests-BowlingScoreSheetTest
 *
 * php tests/phpunit.phar --group Tests tests/BowlingScoreSheetTest.php
 * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest tests/BowlingScoreSheetTest.php
 *
 */

class BowlingScoreSheetTest extends TestBase
{
    private $ins;
    private $class_name = 'BowlingScoreSheet';
    private $result_of_all_attempts_base = [//投球結果のデータのベースとなるもの
        ['', ''], ['', ''], ['', ''], ['', ''], ['', ''], ['', ''], ['', ''], ['', ''], ['', ''], ['', '', '']
    ];

    /**
     * @dataProvider isValidNumOfFrames_ReturnsExpectedValue
     */
    public function isValidNumOfFrames_ReturnsExpectedValue_DataProvider()
    {

        $result_of_all_attempts = $this->result_of_all_attempts_base;
        $result_of_all_attempts[] = [];
        return [
            //10フレーム分の配列があるなら、trueを返すこと。
            [
                $this->result_of_all_attempts_base,
                true
            ],
            //10フレーム分の配列がないなら、falseを返すこと。
            [
                Arr::unsetElmsAndArrVals($this->result_of_all_attempts_base, 0),
                false
            ],

            [
                $result_of_all_attempts,
                false
            ]
        ];
    }


    /**
     * php tests/phpunit.phar --group BowlingScoreSheetTest-isValidNumOfFrames_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group BowlingScoreSheetTest-isValidNumOfFrames_ReturnsExpectedValue
     * @dataProvider isValidNumOfFrames_ReturnsExpectedValue_DataProvider
     *
     * isValidNumOfFramesメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param $expected - 期待値
     *
     */
    public function isValidNumOfFrames_ReturnsExpectedValue($result_of_all_attempts, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'isValidNumOfFrames',$this->ins);
        $this->assertEquals($expected, $actual);

    }


    /**
     * @dataProvider getSumOfFrame_ReturnsExpectedValue
     */
    public function getSumOfFrame_ReturnsExpectedValue_DataProvider()
    {
        $result_of_all_attempts = $this->result_of_all_attempts_base;
        $result_of_all_attempts[0] = ['1', '1'];
        $result_of_all_attempts[1] = ['1', ''];
        return [
            //指定した配列の要素を足し、合計を返す事
            [
                $result_of_all_attempts,
                1,
                2
            ],
            //空文字は0と認識して、そのフレームで倒した数の合計を返す事。
            [
                $result_of_all_attempts,
                2,
                1
            ],
        ];
    }


    /**
     *
     * php tests/phpunit.phar --group BowlingScoreSheetTest-getSumOfFrame_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group BowlingScoreSheetTest-getSumOfFrame_ReturnsExpectedValue
     * @dataProvider getSumOfFrame_ReturnsExpectedValue_DataProvider
     *
     * getSumOfFrameメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - フレーム内で倒されたピンの合計を取得するフレームの番号
     * @param $expected - 期待値
     */
    public function getSumOfFrame_ReturnsExpectedValue($result_of_all_attempts, $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'getSumOfFrame',$this->ins, [$frame_num]);
        $this->assertEquals($expected, $actual);

    }


    /**
     * @dataProvider validateFinalFrame_ReturnsExpectedValue
     */
    public function validateFinalFrame_ReturnsExpectedValue_DataProvider()
    {
        for ($i = 0; $i <= 4; ++$i) {
            $result_of_all_attempts_for_tests[$i] = $this->result_of_all_attempts_base;
        }
        $result_of_all_attempts_for_tests[0][9] = ['9', '1', ''];
        $result_of_all_attempts_for_tests[1][9] = ['10', '1', ''];

        $result_of_all_attempts_for_tests[2][9] = ['', ''];
        $result_of_all_attempts_for_tests[3][9] = ['11', '', ''];
        $result_of_all_attempts_for_tests[4][9] = ['-1', '', ''];
        $result_of_all_attempts_for_tests[5][9] = ['9', '2', ''];
        $result_of_all_attempts_for_tests[6][9] = ['8', '1', '1'];

        return [
            [//最終フレームの投球結果が正常な形式であるのならば、nullを返すこと。
                $result_of_all_attempts_for_tests[0],
                10,
                null
            ],
            [//最終フレームの投球結果が正常な形式であるのならば、nullを返すこと。
                //(第10フレームの1投目がストライクであれば、1投目+2投目が10ピンを超えていても正常。)
                $result_of_all_attempts_for_tests[1],
                10,
                null
            ],


            //最終フレームに3投分のデータが無かった場合、然るべきエラーコードを返すこと。
            [
                $result_of_all_attempts_for_tests[2],
                10,
                '1003'

            ],

            //最終フレームに10ピンを超える投球があった場合、然るべきエラーコードを返すこと。
            [
                $result_of_all_attempts_for_tests[3],
                10,
                '1004'
            ],

            //最終フレームにマイナスの値が入った投球があった場合、然るべきエラーコードを返すこと。
            [
                $result_of_all_attempts_for_tests[4],
                10,
                '1005'
            ],

            //第10フレームの1～2投目で倒したピンの数が一投目がストライクでなにのにも関わらず10を超えていた場合、
            //然るべきエラーコードを返すこと。
            [
                $result_of_all_attempts_for_tests[5],
                10,
                '1006'
            ],

            //第10フレームで1投目でストライクを出しておらず、2投目でスペアを出していない場合に3投目で1ピン以上倒していた場合。、
            //然るべきエラーコードを返すこと。
            [
                $result_of_all_attempts_for_tests[6],
                10,
                '1007'
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group BowlingScoreSheetTest-validateFinalFrame_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group BowlingScoreSheetTest-validateFinalFrame_ReturnsExpectedValue
     * @dataProvider validateFinalFrame_ReturnsExpectedValue_DataProvider
     *
     * validateFinalFrameメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - ヴァリデーションをかけるフレームのフレーム番号
     * @param $expected - 期待値
     */
    public function validateFinalFrame_ReturnsExpectedValue($result_of_all_attempts, $frame_num, $expected)
    {

        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateFinalFrame',$this->ins, [$frame_num]);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @dataProvider validateAttempt_ReturnsExpectedValue
     */
    public function validateAttempt_ReturnsExpectedValue_DataProvider()
    {
        for ($i = 0; $i <= 3; ++$i) {
            $result_of_all_attempts_for_tests[$i] = $this->result_of_all_attempts_base;
        }

        $result_of_all_attempts_for_tests[0][0] = ['9', '0'];
        $result_of_all_attempts_for_tests[1][0] = ['0', '0'];
        $result_of_all_attempts_for_tests[2][0] = ['11', '0'];
        $result_of_all_attempts_for_tests[3][0] = ['-1', '0'];

        return [
            //最終フレームの投球結果が正常な形式であるのならば、nullを返すこと。
            [$result_of_all_attempts_for_tests[0],
                null
            ],

            [
                $result_of_all_attempts_for_tests[1],
                null
            ],


            //最終フレームに10ピンを超える投球があった場合、然るべきエラーコードを返すこと。
            [
                $result_of_all_attempts_for_tests[2],
                '1004'
            ],

            //最終フレームにマイナスの値が入った投球があった場合、然るべきエラーコードを返すこと。
            [
                $result_of_all_attempts_for_tests[3],
                '1005'
            ],


        ];
    }


    /**
     *
     * php tests/phpunit.phar --group BowlingScoreSheetTest-validateAttempt_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group BowlingScoreSheetTest-validateAttempt_ReturnsExpectedValue
     * @dataProvider validateAttempt_ReturnsExpectedValue_DataProvider
     *
     * validateAttemptメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param $expected - 期待値
     */
    public function validateAttempt_ReturnsExpectedValue($result_of_all_attempts, $expected)
    {

        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateAttempt',$this->ins, [$result_of_all_attempts[0][0]]);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @dataProvider validateAttempts_ReturnsExpectedValue
     */
    public function validateAttempts_ReturnsExpectedValue_DataProvider()
    {
        for ($i = 0; $i <= 6; ++$i) {
            $result_of_all_attempts_for_tests[$i] = $this->result_of_all_attempts_base;
        }

        //nullを返す事
        $result_of_all_attempts_for_tests[0][0] = ['9', '2'];

        //1004を返す事
        $result_of_all_attempts_for_tests[1][0] = ['11', '0'];
        $result_of_all_attempts_for_tests[2][0] = ['0', '11'];
        $result_of_all_attempts_for_tests[3][0] = ['11', '-1'];

        //1005を返す事
        $result_of_all_attempts_for_tests[4][0] = ['-1', '0'];
        $result_of_all_attempts_for_tests[5][0] = ['0', '-1'];
        $result_of_all_attempts_for_tests[6][0] = ['-1', '10'];

        return [
            [
                $result_of_all_attempts_for_tests[0],
                1,
                null
            ],

            [
                $result_of_all_attempts_for_tests[1],
                1,
                '1004'
            ],


            [
                $result_of_all_attempts_for_tests[2],
                1,
                '1004'
            ],

            [
                $result_of_all_attempts_for_tests[3],
                1,
                '1004'
            ],
            [
                $result_of_all_attempts_for_tests[4],
                1,
                '1005'
            ],

            [
                $result_of_all_attempts_for_tests[5],
                1,
                '1005'
            ],
            [
                $result_of_all_attempts_for_tests[6],
                1,
                '1005'
            ],


        ];
    }


    /**
     *
     * php tests/phpunit.phar --group BowlingScoreSheetTest-validateAttempts_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group BowlingScoreSheetTest-validateAttempts_ReturnsExpectedValue
     * @dataProvider validateAttempts_ReturnsExpectedValue_DataProvider
     *
     * validateAttemptsメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - ヴァリデーションをかけたい投球を含んだフレームの番号
     * @param $expected - 期待値
     */
    public function validateAttempts_ReturnsExpectedValue($result_of_all_attempts, $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateAttempts',$this->ins, [$frame_num]);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @dataProvider validateFrame_ReturnsExpectedValue
     */
    public function validateFrame_ReturnsExpectedValue_DataProvider()
    {
        for ($i = 0; $i <= 6; ++$i) {
            $result_of_all_attempts_for_tests[$i] = $this->result_of_all_attempts_base;
        }

        //nullを返す事
        $result_of_all_attempts_for_tests[0][0] = ['9', '1'];
        $result_of_all_attempts_for_tests[1][0] = ['0', '0'];

        //1004を返す事
        $result_of_all_attempts_for_tests[2][0] = ['11', '0'];
        $result_of_all_attempts_for_tests[3][0] = ['0', '11'];
        $result_of_all_attempts_for_tests[4][0] = ['11', '-1'];

        //1005を返す事
        $result_of_all_attempts_for_tests[5][0] = ['-1', '0'];
        $result_of_all_attempts_for_tests[6][0] = ['0', '-1'];
        $result_of_all_attempts_for_tests[7][0] = ['-1', '10'];

        //1002を返す事
        $result_of_all_attempts_for_tests[8][0] = ['9', '2'];
        return [
            [
                $result_of_all_attempts_for_tests[0],
                1,
                null
            ],

            [
                $result_of_all_attempts_for_tests[1],
                1,
                null
            ],


            [
                $result_of_all_attempts_for_tests[2],
                1,
                '1004'
            ],

            [
                $result_of_all_attempts_for_tests[3],
                1,
                '1004'
            ],
            [
                $result_of_all_attempts_for_tests[4],
                1,
                '1004'
            ],

            [
                $result_of_all_attempts_for_tests[5],
                1,
                '1005'
            ],
            [
                $result_of_all_attempts_for_tests[6],
                1,
                '1005'
            ],
            [
                $result_of_all_attempts_for_tests[7],
                1,
                '1005'
            ],
            [
                $result_of_all_attempts_for_tests[8],
                1,
                '1002'
            ],


        ];
    }


    /**
     *
     * php tests/phpunit.phar --group BowlingScoreSheetTest-validateFrame_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group BowlingScoreSheetTest-validateFrame_ReturnsExpectedValue
     * @dataProvider validateFrame_ReturnsExpectedValue_DataProvider
     *
     * validateFrameメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - ヴァリデーションをかけるフレームの番号
     * @param $expected - 期待値
     */
    public function validateFrame_ReturnsExpectedValue($result_of_all_attempts, $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateFrame',$this->ins, [$frame_num]);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @dataProvider generateErrorMsg_ReturnsExpectedValue
     */
    public function generateErrorMsg_ReturnsExpectedValue_DataProvider(){
        $this->ins = new BowlingScoreSheet($this->result_of_all_attempts_base);//適当な投球結果を挿入しておく。
        $err_msg_lang = $this->getProperty($this->class_name,'err_msg_lang',$this->ins);
        $errors = $this->getProperty($this->class_name,'errors', $this->ins);
        $err_msgs[0] = $errors['1001']['msgs'][$err_msg_lang['validation']];

        return [
            [
                $this->result_of_all_attempts_base,//適当な投球結果を挿入しておく。
                '1001',
                0,
                $err_msgs[0]
            ]
        ];
    }


    /**
     *
     * php tests/phpunit.phar --group BowlingScoreSheetTest-generateErrorMsg_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group BowlingScoreSheetTest-generateErrorMsg_ReturnsExpectedValue
     * @dataProvider generateErrorMsg_ReturnsExpectedValue_DataProvider
     *
     * generateErrorMsgメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param string $err_code -  エラーコード
     * @param int $frame_num -  エラーのあったフレーム番号
     * @param $expected - 期待値
     */
    public function generateErrorMsg_ReturnsExpectedValue($result_of_all_attempts, $err_code, $frame_num, $expected)
    {
//        echo('<pre>');
//        var_dump($this->ins);
//        echo('</pre>');
//        exit;
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'generateErrorMsg',$this->ins, [$err_code,$frame_num]);

//        $method = $this->getInsOfMethod($this->class_name, 'generateErrorMsg');
//        $actual = $method->invoke($this->ins, $frame_num);
        echo('<pre>');
        var_dump($actual);
        var_dump($expected);
        echo('</pre>');
        exit;
        $this->assertEquals($expected, $actual);
    }

}