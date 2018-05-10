<?php
declare(strict_types=1);
require('vendor/autoload.php');

/**
 * @group Tests
 * @group Tests-BowlingScoreSheetTest
 *
 * php tests/phpunit.phar  --group=Tests --coverage-html=tests/coverage/ tests/
 * php tests/phpunit.phar  --group=Tests-BowlingScoreSheetTest --coverage-html=tests/coverage/ tests/BowlingScoreSheetTest.php
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
     * 投球結果の雛形となるデータを指定された数だけ配列に入れて返す。
     * @param int $num_of_data -必要な投球結果の雛形の数。
     * @return array $result_of_all_attempts_for_tests - 指定した数の投球結果の雛形を配列に入れて返す。
     */
    function provideAllAttemptsResults(int $num_of_data){
        for ($i = 0; $i < $num_of_data; ++$i) {
            $result_of_all_attempts_for_tests[$i] = $this->result_of_all_attempts_base;
        }
        return $result_of_all_attempts_for_tests;
    }




    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-__constructor_SetsExpectedValueToResultOfAllAttempt tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-__constructor_SetsExpectedValueToResultOfAllAttempt
     *
     * __constructorメソッドが、result_of_all_attemptsプロパティに期待値をセットする事を確認するテスト。
     */
    public function __constructor_SetsExpectedValueToResultOfAllAttempt()
    {
        $expected = $this->result_of_all_attempts_base;
        $this->ins = new BowlingScoreSheet($this->result_of_all_attempts_base);
        $actual = $this->getProperty($this->class_name,'result_of_all_attempts',$this->ins);
        $this->assertSame($expected,$actual);
    }




    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-isValidResult_ReturnsExpectedValues tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-isValidResult_ReturnsExpectedValues
     *
     * isValidResultが、is_valid_resultプロパティの値を返す事を確認するテスト。
     */
    public function isValidResult_ReturnsExpectedValues()
    {
        $this->ins = new BowlingScoreSheet($this->result_of_all_attempts_base);
        $actual = $this->executeMethod($this->class_name, 'isValidResult',$this->ins);
        $expected = $this->getProperty($this->class_name,'is_valid_result',$this->ins);
        $this->assertSame($expected,$actual);
    }




    /**
     * @dataProvider isValidNumOfFrames_ReturnsExpectedValue
     */
    public function isValidNumOfFrames_ReturnsExpectedValue_DataProvider()
    {
        //投球データの準備
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(3);

        //10フレーム分の配列がないなら、falseを返すこと。(11フレームある場合のデータ作成)
        $result_of_all_attempts_for_tests[2][] = [];

        return [

            //#0 10フレーム分の配列があるなら、trueを返すこと。
            [
                $result_of_all_attempts_for_tests[0],
                true
            ],


            // 10フレーム分の配列がないなら、falseを返すこと。
            [//#1 9フレームしかない場合。
                Arr::unsetElmsAndArrVals($result_of_all_attempts_for_tests[1], 0),
                false
            ],

            [//#2 11フレームある場合。
                $result_of_all_attempts_for_tests[2],
                false
            ]

        ];
    }


    /**
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-isValidNumOfFrames_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-isValidNumOfFrames_ReturnsExpectedValue
     * @dataProvider isValidNumOfFrames_ReturnsExpectedValue_DataProvider
     *
     * isValidNumOfFramesメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param $expected - 期待値
     *
     */
    public function isValidNumOfFrames_ReturnsExpectedValue(array $result_of_all_attempts, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'isValidNumOfFrames',$this->ins);
        $this->assertSame($expected, $actual);

    }




    /**
     * @dataProvider getSumOfPinsInFrame_ReturnsExpectedValue
     */
    public function getSumOfPinsInFrame_ReturnsExpectedValue_DataProvider()
    {
        //データ準備
        $result_of_all_attempts = $this->result_of_all_attempts_base;

        //#1 指定した配列の要素を足し、合計を返す事
        $result_of_all_attempts[0] = ['1', '1'];
        $frame_nums[0] = 1; //$frame_numを指定
        $expected[0] = 2; //期待値を指定

        //#2 空文字は0と認識して、そのフレームで倒した数の合計を返す事。
        $result_of_all_attempts[1] = ['1', ''];
        $frame_nums[1] = 2;
        $expected[1] = 1;


        return [

            //#1 指定した配列の要素を足し、合計を返す事
            [
                $result_of_all_attempts,
                $frame_nums[0],
                $expected[0]
            ],

            //#2 空文字は0と認識して、そのフレームで倒した数の合計を返す事。
            [
                $result_of_all_attempts,
                $frame_nums[1],
                $expected[1]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-getSumOfPinsInFrame_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     *
     * @test
     * @group Tests-BowlingScoreSheetTest-getSumOfPinsInFrame_ReturnsExpectedValue
     * @dataProvider getSumOfPinsInFrame_ReturnsExpectedValue_DataProvider
     *
     * getSumOfPinsInFrameメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - フレーム内で倒されたピンの合計を取得するフレームの番号
     * @param $expected - 期待値
     */
    public function getSumOfPinsInFrame_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'getSumOfPinsInFrame',$this->ins, [$frame_num]);
        $this->assertSame($expected, $actual);

    }




    /**
     * @dataProvider validateAttempt_ReturnsExpectedValue
     */
    public function validateAttempt_ReturnsExpectedValue_DataProvider()
    {

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(4);

        //#0 正常な投球の場合はnullを返す事（倒したピンの数の上限を投入）
        $result_of_all_attempts_for_tests[0][0] = ['10', '0'];
        $expected[0] = null;

        //#1 正常な投球の場合はnullを返す事（倒したピンの数の下限を投入）
        $result_of_all_attempts_for_tests[1][0] = ['0', '0'];
        $expected[1] = null;

        //#2 倒したピンの数が10を超える投球だった場合、エラーコードの'1004'を返す事
        $result_of_all_attempts_for_tests[2][0] = ['11', '0'];
        $expected[2] = '1004';

        //#3 倒したピンの数がマイナスの投球だった場合、エラーコードの'1005'を返す事
        $result_of_all_attempts_for_tests[3][0] = ['-1', '0'];
        $expected[3] = '1005';


        return [

            //#0 正常な投球の場合はnullを返す事（倒したピンの数の上限を投入）
            [
                $result_of_all_attempts_for_tests[0],
                $expected[0]
            ],

            //#1 正常な投球の場合はnullを返す事（倒したピンの数の下限を投入）
            [
                $result_of_all_attempts_for_tests[1],
                $expected[1]
            ],

            //#2 倒したピンの数が10を超える投球だった場合、エラーコードの'1004'を返す事
            [
                $result_of_all_attempts_for_tests[2],
                $expected[2]
            ],

            //#3 倒したピンの数がマイナスの投球だった場合、エラーコードの'1005'を返す事
            [
                $result_of_all_attempts_for_tests[3],
                $expected[3]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateAttempt_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateAttempt_ReturnsExpectedValue
     * @dataProvider validateAttempt_ReturnsExpectedValue_DataProvider
     *
     * validateAttemptメソッドが期待値を返す事を確認するテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param $expected - 期待値
     */
    public function validateAttempt_ReturnsExpectedValue(array $result_of_all_attempts, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateAttempt',$this->ins, [$result_of_all_attempts[0][0]]);
        $this->assertSame($expected, $actual);
    }




    /**
     * @dataProvider validateAttempts_ReturnsExpectedValue
     */
    public function validateAttempts_ReturnsExpectedValue_DataProvider()
    {
        //データの準備
        //期待値となるエラーメッセージのを作成するためのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(7);


        //正常なデータである場合、nullを返すこと。
        //#0 上限と下限の値が投入されていて、それぞれの投球の配列のキーが妥当なデータ、
        $result_of_all_attempts_for_tests[0][0] = ['10', '0'];
        $frame_nums[0] = 1; //フレーム番号
        $expected[0] = null; //期待値

        //#1 二つの投球の合計は10を変えているが、一つ一つの投球が上限・下限内のデータ（validateFrameではエラーが出るデータだが、一つ一つの投球は正常値なので、ここではnullを返す）
        $result_of_all_attempts_for_tests[1][0] = ['10', '1'];
        $frame_nums[1] = 1;
        $expected[1] = null;


        //1004のエラーメッセージをを返す事
        //#2  倒したピンの数が10を超えている投球が場合
        $result_of_all_attempts_for_tests[2][0] = ['11', '0'];
        $frame_nums[2] = 1;
        $expected[2] = sprintf($properties['errors']['1004']['msgs'][$properties['err_msg_lang']['validation']],$frame_nums[2]);


        //1005のエラーメッセージをを返す事
        //#3　倒したピンの数がマイナスだった投球がある場合
        $result_of_all_attempts_for_tests[3][0] = ['-1', '0'];
        $frame_nums[3] = 1;
        $expected[3] = sprintf($properties['errors']['1005']['msgs'][$properties['err_msg_lang']['validation']],$frame_nums[3]);


        //#4　投球結果の要素を格納する配列のキーが妥当なものではなかった場合、1009のエラーメッセージを返す事。
        //(1004や1005のエラーよりも優先順位が高い事。)
        unset($result_of_all_attempts_for_tests[4][0][0]);
        $result_of_all_attempts_for_tests[4][0][1] = '';
        $result_of_all_attempts_for_tests[4][0][2] = '-1';
        $frame_nums[4] = 1;
        $expected[4] = sprintf($properties['errors']['1009']['msgs'][$properties['err_msg_lang']['validation']],$frame_nums[4]);


        return [

            //#0 上限と下限の値が投入されていて、それぞれの投球の配列のキーが妥当なデータ、
            [
                $result_of_all_attempts_for_tests[0],
                $frame_nums[0],
                $expected[0]
            ],

            //#1 二つの投球の合計は10を変えているが、一つ一つの投球が上限・下限内のデータ（validateFrameではエラーが出るデータだが、
            //一つ一つの投球は正常値なので、ここではnullを返す）
            [
                $result_of_all_attempts_for_tests[1],
                $frame_nums[1],
                $expected[1]
            ],

            //1004のエラーメッセージをを返す事
            //#2 1投目で倒したピンの数が10を超えていた場合
            [
                $result_of_all_attempts_for_tests[2],
                $frame_nums[2],
                $expected[2]
            ],

            //1005のエラーメッセージをを返す事
            //#3 1投目で倒したピンの数がマイナスだった場合
            [
                $result_of_all_attempts_for_tests[3],
                $frame_nums[3],
                $expected[3]
            ],

            //#4 投球結果の要素を格納する配列のキーが妥当なものではなかった場合、1009のエラーメッセージを返す事。
            //(1004や1005のエラーよりも優先順位が高い事。)
            [
                $result_of_all_attempts_for_tests[4],
                $frame_nums[4],
                $expected[4]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateAttempts_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateAttempts_ReturnsExpectedValue
     * @dataProvider validateAttempts_ReturnsExpectedValue_DataProvider
     *
     * validateAttemptsメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - ヴァリデーションをかけたい投球を含んだフレームの番号
     * @param $expected - 期待値
     */
    public function validateAttempts_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateAttempts',$this->ins, [$frame_num]);
        $this->assertSame($expected, $actual);

    }




    /**
     * @dataProvider validateFinalFrame_ReturnsExpectedValue
     */
    public function validateFinalFrame_ReturnsExpectedValue_DataProvider()
    {
        //データの準備
        //期待値となるエラーメッセージのを作成するためのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(7);

        //最終フレームの投球結果が正常な形式であるのならば、nullを返すこと。(validateAttempts以外の境界値を意識)
        //#0
        $result_of_all_attempts_for_tests[0][9] = ['10', '1', '1'];
        $expected[0] = null;
        //#1
        $result_of_all_attempts_for_tests[1][9] = ['9', '1', '1'];
        $expected[1] = null;
        //#2
        $result_of_all_attempts_for_tests[2][9] = ['8', '1', '0'];
        $expected[2] = null;


        //#3 最終フレームに3投分のデータが無かった場合、然るべきエラーコードを返すこと。
        $result_of_all_attempts_for_tests[3][9] = ['', ''];
        $expected[3] =  $properties['errors']['1003']['msgs'][$properties['err_msg_lang']['validation']];

        //#4 最終フレームにValidateAttemptsに引っかかるエラーがあった場合、然るべきエラーメッセージを返すこと。
        $result_of_all_attempts_for_tests[4][9] = ['11', '', ''];
        $expected[4] =  sprintf($properties['errors']['1004']['msgs'][$properties['err_msg_lang']['validation']],10);

        //#6 第10フレームの1～2投目で倒したピンの数が一投目がストライクでないのにも関わらず10を超えていた場合、
        //然るべきエラーメッセージを返すこと。
        $result_of_all_attempts_for_tests[5][9] = ['9', '2', ''];
        $expected[5] = $properties['errors']['1006']['msgs'][$properties['err_msg_lang']['validation']];

        //#7 第10フレームで1投目でストライクを出しておらず、2投目でスペアを出していない場合に3投目で1ピン以上倒していた場合。、
        //然るべきエラーメッセージを返すこと。
        $result_of_all_attempts_for_tests[6][9] = ['8', '1', '1'];
        $expected[6] = $properties['errors']['1007']['msgs'][$properties['err_msg_lang']['validation']];



        return [
            //最終フレームの投球結果が正常な形式であるのならば、nullを返すこと。(validateAttempts以外の境界値を意識)
            [//#0
                $result_of_all_attempts_for_tests[0],
                10,
                $expected[0]
            ],
            [//#1
                $result_of_all_attempts_for_tests[1],
                10,
                $expected[1]
            ],
            [//#2
                $result_of_all_attempts_for_tests[2],
                10,
                $expected[2]
            ],

            //#3 最終フレームに3投分のデータが無かった場合、然るべきエラーエラーメッセージを返すこと。
            [
                $result_of_all_attempts_for_tests[3],
                10,
                $expected[3]

            ],

            //#4 最終フレームにValidateAttemptsに引っかかるエラーがあった場合、然るべきエラーメッセージを返すこと。
            [
                $result_of_all_attempts_for_tests[4],
                10,
                $expected[4]
            ],


            //#5 第10フレームの1～2投目で倒したピンの数が一投目がストライクでなにのにも関わらず10を超えていた場合、
            //然るべきエラーメッセージを返すこと。
            [
                $result_of_all_attempts_for_tests[5],
                10,
                $expected[5]
            ],

            //#6 第10フレームで1投目でストライクを出しておらず、2投目でスペアを出していない場合に3投目で1ピン以上倒していた場合。、
            //然るべきエラーメッセージを返すこと。
            [
                $result_of_all_attempts_for_tests[6],
                10,
                $expected[6]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateFinalFrame_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateFinalFrame_ReturnsExpectedValue
     * @dataProvider validateFinalFrame_ReturnsExpectedValue_DataProvider
     *
     * validateFinalFrameメソッドが期待値を返す事を確認するテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - ヴァリデーションをかけるフレームの番号
     * @param $expected - 期待値
     */
    public function validateFinalFrame_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateFinalFrame',$this->ins, [$frame_num]);
        $this->assertSame($expected, $actual);
    }




    /**
     * @dataProvider validateFrame_ReturnsExpectedValue
     */
    public function validateFrame_ReturnsExpectedValue_DataProvider()
    {
        //データの準備
        //期待値となるエラーメッセージのを作成するためのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(9);


        //正常値であるならば、nullを返す事
        //#0 1回の投球で倒したピンの数、各フレーム内で倒したピンの数において、上限の値が投入された場合。
        $result_of_all_attempts_for_tests[0][0] = ['10', '0'];
        $frame_num[0] = 1;
        $expected[0] = null;

        //#1 1回の投球で倒したピンの数、各フレーム内で倒したピンの数において、下限の値が投入された場合。
        $result_of_all_attempts_for_tests[1][0] = ['0', '0'];
        $frame_num[1] = 1;
        $expected[1] = null;

        //#2 第1～9フレームで、投球枠が2つでない場合、1010のエラーメッセージを返す事。
        $result_of_all_attempts_for_tests[2][0] = ['0', '0', '0'];
        $frame_num[2] = 1;
        $expected[2] = sprintf($properties['errors']['1010']['msgs'][$properties['err_msg_lang']['validation']],1);

        //#3 一つ一つの投球で倒したピンの数は妥当であるが、1フレーム内で倒したピンの数が10を超えていた場合、1002のエラーメッセージを返す事。
        $result_of_all_attempts_for_tests[3][0] = ['9', '2'];
        $frame_num[3] = 1;
        $expected[3] = sprintf($properties['errors']['1002']['msgs'][$properties['err_msg_lang']['validation']],1);


        return [

            //正常値であるならば、nullを返す事
            [//#0 1回の投球で倒したピンの数、各フレーム内で倒したピンの数において、上限の値が投入された場合。

                $result_of_all_attempts_for_tests[0],
                $frame_num[0],
                $expected[0]
            ],
            [//#1 1回の投球で倒したピンの数、各フレーム内で倒したピンの数において、下限の値が投入された場合。

                $result_of_all_attempts_for_tests[1],
                $frame_num[1],
                $expected[1]
            ],


            //#2 第1～9フレームで、投球枠が2つでない場合、1010のエラーメッセージを返す事。
            [
                $result_of_all_attempts_for_tests[2],
                $frame_num[2],
                $expected[2]
            ],


            //#3 一つ一つの投球で倒したピンの数は妥当であるが、1フレーム内で倒したピンの数が10を超えていた場合、1002のエラーメッセージを返す事。
            [
                $result_of_all_attempts_for_tests[3],
                $frame_num[3],
                $expected[3]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateFrame_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateFrame_ReturnsExpectedValue
     * @dataProvider validateFrame_ReturnsExpectedValue_DataProvider
     *
     * validateFrameメソッドが期待値を返す事を確認するテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - ヴァリデーションをかけるフレーム番号
     * @param $expected - 期待値
     */
    public function validateFrame_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateFrame',$this->ins, [$frame_num]);
        $this->assertSame($expected, $actual);
    }




    /**
     * @dataProvider validateFrame_ExecuteExpectedMethod
     */
    public function validateFrame_ExecuteExpectedMethod_DataProvider()
    {


        return [
            //#0 第1～9フレームで、投球枠が2つである場合、validateAttemptsメソッドが実行され、validateFinalFrameは実行されない事。
            [
                $this->result_of_all_attempts_base,
                9,
                [
                    'times_validate_attempts_executed' => 1,
                    'times_validate_final_frame_executed' => 0
                ]

            ],

            //#1 最終フレームが指定されている場合、validateFinalFrameが実行される事
            [
                $this->result_of_all_attempts_base,
                10,
                [
                    'times_validate_final_frame_executed' => 1
                ]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateFrame_ExecuteExpectedMethod tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateFrame_ExecuteExpectedMethod
     * @dataProvider validateFrame_ExecuteExpectedMethod_DataProvider
     *
     * validateFrameが実行された時、それぞれのデータに応じて、期待したメソッドが実行される事、及びされない事を確認するテスト。
     *
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - ヴァリデーションをかけるフレーム番号
     * @expected boolean $expected_list - キーにプロパティ名、要素に期待値。
     */
    public function validateFrame_ExecuteExpectedMethod(array $result_of_all_attempts, int $frame_num, array $expected_list)
    {

        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $this->executeMethod($this->class_name, 'validateFrame',$this->ins, [$frame_num]);

        foreach ($expected_list as $propety_name => $expected) {
            $actual = $this->getProperty($this->class_name,$propety_name,$this->ins);
            $this->assertSame($expected, $actual);
        }

    }




    /**
     * @dataProvider validateResult_ReturnsExpectedValue
     */
    public function validateResult_ReturnsExpectedValue_DataProvider()
    {
        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //#0 10フレーム無かった場合は然るべき然るべきエラーメッセージを返す事。
        $result_of_all_attempts_for_tests[0] = $this->result_of_all_attempts_base;
        $result_of_all_attempts_for_tests[0] = Arr::unsetElmsAndArrVals($result_of_all_attempts_for_tests[0],0);
        $expected[0][] = sprintf($properties['errors']['1001']['msgs'][$properties['err_msg_lang']['validation']],1);


        return [
            //#0 10フレーム無かった場合は然るべき然るべきエラーメッセージを返す事。
            [
                $result_of_all_attempts_for_tests[0],
                $expected[0]
            ],
        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateResult_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateResult_ReturnsExpectedValue
     * @dataProvider validateResult_ReturnsExpectedValue_DataProvider
     *
     * validateResultメソッドが期待値を返す事を確認するテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param $expected - 期待値
     */
    public function validateResult_ReturnsExpectedValue(array $result_of_all_attempts, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateResult',$this->ins);
        $this->assertSame($expected, $actual);

    }




    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateResult_ExecuteValidateFrames_IfDataHasTenFrames tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateResult_ExecuteValidateFrames_IfDataHasTenFrames
     *
     * 投球結果が10フレームあるのならば、validateFramesが一回実行される事を確認するテスト。
     */
    public function validateResult_ExecuteValidateFrames_IfDataHasTenFrames()
    {

        $this->ins = new BowlingScoreSheet($this->result_of_all_attempts_base);
        $this->executeMethod($this->class_name, 'validateResult',$this->ins);
        $actual = $this->getProperty($this->class_name,'times_validate_frames_executed',$this->ins);
        $this->assertSame(1,$actual);

    }




    /**
     * @dataProvider validateResult_SetsExpectedValueToIsValidResult
     */
    public function validateResult_SetsExpectedValueToIsValidResult_DataProvider()
    {

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);

        //#0 正常な投球結果が渡された場合、is_valid_resultにtrueがセットされている事
        $expected[0] = ['is_valid_result' => true];

        //#1 正常でない投球結果が渡された場合、is_valid_resultにfalseがセットされている事
        $result_of_all_attempts_for_tests[1][0] = [0,0,0];
        $expected[1] = ['is_valid_result' => false];

        return [

            //#0 正常な投球結果が渡された場合、is_valid_resultにtrueがセットされている事
            [
                $result_of_all_attempts_for_tests[0],
                $expected[0]
            ],

            //#1 正常でない投球結果が渡された場合、is_valid_resultにfalseがセットされている事
            [
                $result_of_all_attempts_for_tests[1],
                $expected[1]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateResult_SetsExpectedValueToIsValidResult tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateResult_SetsExpectedValueToIsValidResult
     * @dataProvider validateResult_SetsExpectedValueToIsValidResult_DataProvider
     *
     * validateResultがis_valid_resultに期待値をセットしている事を確認するテスト。
     * @param array $result_of_all_attempts - 全ての投球結果。
     * @param array $expected - 期待値。
     */
    public function validateResult_SetsExpectedValueToIsValidResult(array $result_of_all_attempts,  $expected_list)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $this->executeMethod($this->class_name, 'validateResult',$this->ins);
        foreach ($expected_list as $property_name => $expected) {
            $actual = $this->getProperty($this->class_name,'is_valid_result',$this->ins);
            $this->assertSame($expected,$actual);
        }
    }




    /**
     * @dataProvider validateFrameKey_ReturnsExpectedValue
     */
    public function validateFrameKey_ReturnsExpectedValue_DataProvider()
    {
        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);

        //#0 フレームが格納されている配列のキーが正常ならば、nullを返す事。
        $keys[0] = 0;
        $frame_nums[0] = 1;
        $expected[0] = null;

        //#1 フレームが格納されている配列のキーが望ましいものでなかった場合、そのエラーメッセージを返す事
        $keys[1] = 1;
        $frame_nums[1] = 1;
        $expected[1] = sprintf($properties['errors']['1008']['msgs'][$properties['err_msg_lang']['validation']],1);


        return [

            //#0 フレームが格納されている配列のキーが正常ならば、nullを返す事。
            [
                $result_of_all_attempts_for_tests[0],
                $keys[0],
                $frame_nums[0],
                $expected[0]
            ],

            //#1 フレームが格納されている配列のキーが望ましいものでなかった場合、1008のエラーメッセージを返す事
            [
                $result_of_all_attempts_for_tests[1],
                $keys[1],
                $frame_nums[1],
                $expected[1]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateFrameKey_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateFrameKey_ReturnsExpectedValue
     * @dataProvider validateFrameKey_ReturnsExpectedValue_DataProvider
     *
     * validateFrameKeyメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param $key - フレームを格納する配列に指定されていたキー
     * @param $frame_num - フレーム番号
     * @param $expected - 期待値
     */
    public function validateFrameKey_ReturnsExpectedValue(array $result_of_all_attempts, int $key, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateFrameKey',$this->ins,[$key,$frame_num]);
        $this->assertSame($expected, $actual);

    }




    /**
     * @dataProvider validateFrames_ReturnsExpectedValue
     */
    public function validateFrames_ReturnsExpectedValue_DataProvider()
    {

        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(3);

        //#0 全てのフレームの投球結果が正常な形式であるのならば、nullを返すこと。
        $expected[0] = null;

        //#1 フレームを格納する配列のキーが妥当でなかった場合、他のエラーメッセージよりも
        //そのエラーメッセージを優先して返すようにする。
        unset($result_of_all_attempts_for_tests[1][9]);
        $result_of_all_attempts_for_tests[1][10] = ['11', '0','0'];
        $expected[1][] = sprintf($properties['errors']['1008']['msgs'][$properties['err_msg_lang']['validation']], 10);

        //#2 投球結果に何かエラーがあるならば、エラーメッセージを配列にして返す。
        $result_of_all_attempts_for_tests[2][0] = ['11', '0'];
        $result_of_all_attempts_for_tests[2][1] = ['-1', '0'];
        $result_of_all_attempts_for_tests[2][9] = ['1', '0'];
        $expected[2] = [
            sprintf($properties['errors']['1004']['msgs'][$properties['err_msg_lang']['validation']], 1),
            sprintf($properties['errors']['1005']['msgs'][$properties['err_msg_lang']['validation']], 2),
            sprintf($properties['errors']['1003']['msgs'][$properties['err_msg_lang']['validation']], 10),
        ];

        return [

            //#0 全てのフレームの投球結果が正常な形式であるのならば、nullを返すこと。
            [
                $result_of_all_attempts_for_tests[0],
                $expected[0]
            ],

            //#1 フレームを格納する配列のキーが妥当でなかった場合、他のエラーメッセージよりも
            //そのエラーメッセージを優先して返すようにする。
            [
                $result_of_all_attempts_for_tests[1],
                $expected[1]
            ],

            //#2 投球結果に何かエラーがあるならば、エラーメッセージを配列にして返す。
            [
                $result_of_all_attempts_for_tests[2],
                $expected[2]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateFrames_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-validateFrames_ReturnsExpectedValue
     * @dataProvider validateFrames_ReturnsExpectedValue_DataProvider
     *
     * validateFramesメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param $expected - 期待値
     */
    public function validateFrames_ReturnsExpectedValue(array $result_of_all_attempts, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'validateFrames',$this->ins);
        $this->assertSame($expected, $actual);
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-validateFrames_ExecutesValidateFrameKeyTenTimes tests/BowlingScoreSheetTest.php
     *
     * @test
     * @group Tests-BowlingScoreSheetTest-validateFrames_ExecutesValidateFrameKeyTenTimes
     *
     * validateFramesメソッドがvalidateFrameKeyを指定回数呼び出す事を確認するテスト。
     */
    public function validateFrames_ExecutesValidateFrameKeyTenTimes()
    {
        $this->ins = new BowlingScoreSheet($this->result_of_all_attempts_base);
        $this->executeMethod($this->class_name, 'validateFrames',$this->ins);
        $actual = $this->getProperty($this->class_name,'times_validate_frame_key_executed',$this->ins);
        $this->assertSame(10,$actual);
    }




    /**
     * @dataProvider generateErrorMsg_ReturnsExpectedValue
     */
    public function generateErrorMsg_ReturnsExpectedValue_DataProvider(){

        //データの準備
        //期待値となるエラーメッセージのを作成するためのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);


        //#0 デフォルトでは$err_typeに'validation'が設定され、エラーコード、エラータイプ、言語設定、応じたエラーメッセージを返す事。。
        $err_codes[0] = '1001';
        $err_types[0] = 'default';
        $frame_nums[0] = 1;
        $err_msgs[0] = $properties['errors']['1001']['msgs'][$properties['err_msg_lang']['validation']];

        //#1 エラーコード、エラータイプ('validation')、言語設定、応じたエラーメッセージを返す事。
        $err_codes[1] = '1001';
        $err_types[1] = 'validation';
        $frame_nums[1] = 1;
        $err_msgs[1] = $properties['errors']['1001']['msgs'][$properties['err_msg_lang']['validation']];

        //#2 エラーコード、エラータイプ('exception')、言語設定、応じたエラーメッセージを返す事。
        $err_codes[2] = '2001';
        $err_types[2] = 'exception';
        $frame_nums[2] = 1;
        $err_msgs[2] = $properties['errors']['2001']['msgs'][$properties['err_msg_lang']['exception']];

        //#3 取得したエラーメッセージに%dが入った場合、そこにフレーム番号を入れた上で、
        //エラーコード、エラータイプ、言語設定、応じたエラーメッセージを返す事。
        $err_codes[3] = '1002';
        $err_types[3] = 'validation';
        $frame_nums[3] = 1;
        $err_msgs[3] = sprintf($properties['errors']['1002']['msgs'][$properties['err_msg_lang']['validation']], $frame_nums[3]);


        return [

            [
                //#0 デフォルトでは$err_typeに'validation'が設定され、エラーコード、エラータイプ、言語設定、応じたエラーメッセージを返す事。
                $this->result_of_all_attempts_base,//適当な投球結果を挿入しておく。
                $err_codes[0],
                $err_types[0],
                $frame_nums[0],
                $err_msgs[0]
            ],

            [
                //#1 エラーコード、エラータイプ('validation')、言語設定、応じたエラーメッセージを返す事。
                $this->result_of_all_attempts_base,//適当な投球結果を挿入しておく。
                $err_codes[1],
                $err_types[1],
                $frame_nums[1],
                $err_msgs[1]
            ],

            [
                //#2 エラーコード、エラータイプ('exception')、言語設定、応じたエラーメッセージを返す事。
                $this->result_of_all_attempts_base,//適当な投球結果を挿入しておく。
                $err_codes[2],
                $err_types[2],
                $frame_nums[2],
                $err_msgs[2]
            ],

            [
                //#3 取得したエラーメッセージに%dが入った場合、そこにフレーム番号を入れた上で、
                //エラーコード、エラータイプ、言語設定、応じたエラーメッセージを返す事。
                $this->result_of_all_attempts_base,//適当な投球結果を挿入しておく。
                $err_codes[3],
                $err_types[3],
                $frame_nums[3],
                $err_msgs[3]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-generateErrorMsg_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-generateErrorMsg_ReturnsExpectedValue
     * @dataProvider generateErrorMsg_ReturnsExpectedValue_DataProvider
     *
     * generateErrorMsgメソッドが期待値を返す事を確認するテスト。
     *
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param string $err_code -  エラーコード
     * @param string $err_type -  エラータイプ。現在'validation'、'exception'が利用可能
     * @param int $frame_num -  エラーのあったフレーム番号
     * @param $expected - 期待値
     */
    public function generateErrorMsg_ReturnsExpectedValue(array $result_of_all_attempts, string $err_code, string $err_type, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        if($err_type === 'default'){
            $args = [$err_code];
        }else{
            $args = [$err_code,$err_type,$frame_num];
        }
        $actual = $this->executeMethod($this->class_name, 'generateErrorMsg',$this->ins, $args);

        $this->assertSame($expected, $actual);
    }




    /**
     * @dataProvider generateErrorMsg_ThrowsExpectedException_WhenCannotGenerateErrorMsg
     */
    public function generateErrorMsg_ThrowsExpectedException_WhenCannotGenerateErrorMsg_DataProvider(){

        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //#0 エラーメッセージを作成出来なかった場合、例外を投げ、然るべきエラーメッセージを表示する事
        $err_codes[0] = '99999';
        $err_types[0] = 'validation';
        $frame_nums[0] = 1;
        $err_msgs[0] = $properties['errors']['2001']['msgs'][$properties['err_msg_lang']['exception']].$this->class_name.'::generateErrorMsg.';

        //#1 フレーム番号が指定されていなかった事により、エラーメッセージを作成出来なかった場合、例外を投げ、然るべきエラーメッセージを表示する事
        $err_codes[1] = '1002';
        $err_types[1] = 'validation';
        $frame_nums[1] = null;
        $err_msgs[1] = $properties['errors']['2006']['msgs'][$properties['err_msg_lang']['exception']].$err_codes[1].' '.$this->class_name.'::generateErrorMsg.';

        return [

            [//#0 エラーメッセージを作成出来なかった場合、例外を投げ、然るべきエラーメッセージを表示する事。
                $this->result_of_all_attempts_base,//適当な投球結果を挿入しておく。
                $err_codes[0],//存在しないエラーコード
                $err_types[1],
                $frame_nums[0],
                $err_msgs[0]
            ],

            [//#1 フレーム番号が指定されていなかった事により、エラーメッセージを作成出来なかった場合、例外を投げ、然るべきエラーメッセージを表示する事
                $this->result_of_all_attempts_base,//適当な投球結果を挿入しておく。
                $err_codes[1],//存在しないエラーコード
                $err_types[1],
                $frame_nums[1],
                $err_msgs[1]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-generateErrorMsg_ThrowsExpectedException_WhenCannotGenerateErrorMsg tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-generateErrorMsg_ThrowsExpectedException_WhenCannotGenerateErrorMsg
     * @dataProvider generateErrorMsg_ThrowsExpectedException_WhenCannotGenerateErrorMsg_DataProvider
     *
     * generateErrorMsgメソッドがエラーメッセージを作成出来なかった場合、例外処理を発生させ、然るべきエラーメッセージを表示する事を確認するテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param string $err_code -  エラーコード
     * @param string $err_type -  エラータイプ。現在'validation'、'exception'が利用可能
     * @param int $frame_num -  エラーのあったフレーム番号
     * @param $expected - 期待値
     */
    public function generateErrorMsg_ThrowsExpectedException_WhenCannotGenerateErrorMsg(array $result_of_all_attempts, string $err_code, string $err_type, $frame_num, $expected)
    {
        try{
            $this->ins = new BowlingScoreSheet($result_of_all_attempts);
            $this->executeMethod($this->class_name, 'generateErrorMsg',$this->ins, [$err_code,$err_type,$frame_num]);
            $this->fail('例外発生なし');
        }catch (Exception $e){
            $actual = $e->getMessage();
            $this->assertSame($expected, $actual);
        }
    }




    /**
     * @dataProvider calculateScores_ExecutesExpectedMethods_AccordingToDataGiven
     */
    public function calculateScores_ExecutesExpectedMethods_AccordingToDataGiven_DataProvider()
    {
        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);
        $result_of_all_attempts_for_tests[1][0] = [0,0,0];


        return [

            //#0 正常な投球結果のデータが挿入された場合は'validateResult'を1回実行し、'calculateScore'を複数回実行する事
            [
                $result_of_all_attempts_for_tests[0],
                [
                    'times_validate_results_executed' => 1,
                    'times_calculate_score_executed'=> 10
                ]
            ],

            //#1 妥当でない投球結果のデータが挿入された場合は'validateResult'を1回実行し、'calculateScore'を実行しない事
            [
                $result_of_all_attempts_for_tests[1],
                [
                    'times_validate_results_executed' => 1,
                    'times_calculate_score_executed'=> 0
                ]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-calculateScores_ExecutesExpectedMethods_AccordingToDataGiven tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-calculateScores_ExecutesExpectedMethods_AccordingToDataGiven
     * @dataProvider calculateScores_ExecutesExpectedMethods_AccordingToDataGiven_DataProvider
     *
     * calculateScoresが期待されたメソッドを期待した回数実行するかをテスト。
     * @param array $result_of_all_attempts - 全ての投球結果。
     * @param array $expected_list - メソッド実行後、各プロパティに期待する値。キーにプロパティ名、要素に期待値。
     */
    public function calculateScores_ExecutesExpectedMethods_AccordingToDataGiven(array $result_of_all_attempts, array $expected_list)
    {

        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $this->executeMethod($this->class_name, 'calculateScores',$this->ins);
        foreach ($expected_list as $property_name => $expected) {
            $actual = self::getProperty($this->class_name,$property_name,$this->ins);
            $this->assertSame($expected, $actual);
        }

    }



    /**
     * @dataProvider calculateScores_ReturnsExpectedValues
     */
    public function calculateScores_ReturnsExpectedValues_DataProvider()
    {
        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);

        //#0 スコアを計算し、その結果を返す事。
        $result_of_all_attempts_for_tests[0] = [
            [10,0],
            [10,0],
            [10,0],
            [10,0],
            [10,0],
            [10,0],
            [10,0],
            [10,0],
            [10,0],
            [10,10,10],
        ];
        $expected[0] = [
            30,60,90,120,150,180,210,240,270,300
        ];

        //#1 ヴァリデーションに引っかかれば、そのエラーメッセージを配列で返す事
        $result_of_all_attempts_for_tests[1][0] = [0,0,0];
        $result_of_all_attempts_for_tests[1][9] = [0,0];
        $expected[1][0] =  sprintf($properties['errors']['1010']['msgs'][$properties['err_msg_lang']['validation']], 1);
        $expected[1][1] =  $properties['errors']['1003']['msgs'][$properties['err_msg_lang']['validation']];

        return [

            //#0 スコアを計算し、その結果を返す事。
            [
                $result_of_all_attempts_for_tests[0],
                [
                    30,60,90,120,150,180,210,240,270,300
                ]
            ],

            //#1 ヴァリデーションに引っかかれば、そのエラーメッセージを配列で返す事
            [
                $result_of_all_attempts_for_tests[1],
                $expected[1]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-calculateScores_ReturnsExpectedValues tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-calculateScores_ReturnsExpectedValues
     * @dataProvider calculateScores_ReturnsExpectedValues_DataProvider
     *
     * calculateScoresが期待値を返すかテスト。
     * @param array $result_of_all_attempts - 全ての投球結果。
     * @param array $expected - 期待値。
     */
    public function calculateScores_ReturnsExpectedValues(array $result_of_all_attempts,  $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'calculateScores',$this->ins);
        $this->assertSame($expected, $actual);
    }




    /**
     * @dataProvider calculateScore_ReturnsExpectedValue
     */
    public function calculateScore_ReturnsExpectedValue_DataProvider()
    {
        ////ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(7);


        //1, 第10フレームが指定されていた場合、第10フレームで倒したピンの合計を返す事。
        //#0
        $result_of_all_attempts_for_tests[0][9] = ['10','10','10'];
        $frame_nums[0] = 10;
        $expected[0] = 30;


        //2、第1～9フレームが指定されていて、ストライクが出た場合、10 + その後の2投で倒したピンの数を返す事
        //#1、第8フレームが指定されていて、ストライクではあるがダブルでない場合、10ピン + 次のフレーム内の1～2投目で倒したピンの数、を返す事
        $result_of_all_attempts_for_tests[1][7] = ['10','0'];
        $result_of_all_attempts_for_tests[1][8] = ['8','1'];
        $frame_nums[1] = 8;
        $expected[1] = 19;

        //#2 第7フレームが指定されていて、ダブルだった場合、20ピン + 次の次のフレームの1投目で倒したピンの数、を返す事
        $result_of_all_attempts_for_tests[2][6] = ['10','0'];
        $result_of_all_attempts_for_tests[2][7] = ['10','0'];
        $result_of_all_attempts_for_tests[2][8] = ['9','0'];
        $frame_nums[2] = 7;
        $expected[2] = 29;


        //#3 第9フレームでストライクを出し、10フレーム目の1投目でストライクを出した場合、20ピン + 10フレーム目の2投目で倒したピンの数、を返す事
        $result_of_all_attempts_for_tests[3][8] = ['10','0'];
        $result_of_all_attempts_for_tests[3][9] = ['10','9','0'];
        $frame_nums[3] = 9;
        $expected[3] = 29;


        //3、第1～9フレームが指定されていて、スペアであった場合、10ピン+次のフレームの1投目で倒したピンの数を返す事。
        //#4 第8フレームが指定されていて、スペアであった場合、10ピン+第9フレームの1投目で倒したピンの数を返す事。
        $result_of_all_attempts_for_tests[4][7] = ['9','1'];
        $result_of_all_attempts_for_tests[4][8] = ['9','10'];
        $frame_nums[4] = 8;
        $expected[4] = 19;


        //4、第1～9フレームが指定されていて、ストライクでもスペアでもなかった場合、そのフレーム内の合計スコアを返す事。
        //#5
        $result_of_all_attempts_for_tests[5][8] = ['8','1'];
        $frame_nums[5] = 9;
        $expected[5] = 9;


        return
            [

                //1、第10フレームが指定されていた場合、第10フレームで倒したピンの合計を返す事。
                //#0
                [
                    $result_of_all_attempts_for_tests[0],
                    $frame_nums[0],
                    $expected[0]
                ],


                //2、第1～9フレームが指定されていて、ストライクが出た場合、10 + その後の2投で倒したピンの数を返す事
                //#1 第1～9フレームが指定されていて、ストライクではあるがダブルでない場合、10ピン + 次のフレーム内の1~2投目で倒したピンの数、を返す事
                [
                    $result_of_all_attempts_for_tests[1],
                    $frame_nums[1],
                    $expected[1]

                ],

                //#2 第7フレームが指定されていて、ダブルだった場合、20ピン + 次の次のフレームの1投目で倒したピンの数、を返す事
                [
                    $result_of_all_attempts_for_tests[2],
                    $frame_nums[2],
                    $expected[2]

                ],

                //#3 第9フレームでストライクを出し、10フレーム目の1投目でストライクを出した場合、20ピン + 10フレーム目の2投目で倒したピンの数、を返す事
                [
                    $result_of_all_attempts_for_tests[3],
                    $frame_nums[3],
                    $expected[3]
                ],


                //3 第1～9フレームが指定されていて、スペアであった場合、10ピン+次のフレームの1投目で倒したピンの数を返す事。
                //#4 第8フレームが指定されていて、スペアであった場合、10ピン+第9フレームの1投目で倒したピンの数を返す事。
                [
                    $result_of_all_attempts_for_tests[4],
                    $frame_nums[4],
                    $expected[4]
                ],


                //4 第1～9フレームが指定されていて、ストライクでもスペアでもなかった場合、そのフレーム内の合計スコアを返す事。
                //#5
                [
                    $result_of_all_attempts_for_tests[5],
                    $frame_nums[5],
                    $expected[5]
                ],

            ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-calculateScore_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-calculateScore_ReturnsExpectedValue
     * @dataProvider calculateScore_ReturnsExpectedValue_DataProvider
     *
     * calculateScoreメソッドが期待値を返すかのテスト。
     * @param int $frame_num - スコアを計算したいフレームの番号
     * @param $expected - 期待値
     */
    public function calculateScore_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'calculateScore',$this->ins,[$frame_num]);
        $this->assertSame($expected, $actual);
    }



    /**
     * @dataProvider calculateScore_ThrowsExpectedException_WhenFrameNumIsInvalid
     */
    public function calculateScore_ThrowsExpectedException_WhenFrameNumIsInvalid_DataProvider()
    {

        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);

        $err_msgs['2004'] =  $properties['errors']['2004']['msgs'][$properties['err_msg_lang']['exception']].'BowlingScoreSheet::calculateScore.';


        //フレーム番号が適切出なければ、例外処理を発生させ、然るべきエラーメッセージを返す事。
        //#0 フレーム番号が下限を下回った場合、
        $frame_nums[0] = 0;
        $expected[0] = $err_msgs['2004'];

        //#1 フレーム番号が上限を超えた場合、
        $frame_nums[1] = 11;
        $expected[1] = $err_msgs['2004'];


        return [

            //フレーム番号が適切出なければ、例外処理を発生させ、然るべきエラーメッセージを返す事。
            [#0 フレーム番号が下限を下回った場合、
                $result_of_all_attempts_for_tests[0],
                $frame_nums[0],
                $expected[0]
            ],

            [#1 フレーム番号が上限を超えた場合、
                $result_of_all_attempts_for_tests[1],
                $frame_nums[1],
                $expected[1]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-calculateScore_ThrowsExpectedException_WhenFrameNumIsInvalid tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-calculateScore_ThrowsExpectedException_WhenFrameNumIsInvalid
     * @dataProvider calculateScore_ThrowsExpectedException_WhenFrameNumIsInvalid_DataProvider
     *
     * calculateScoreメソッドの$frame_numに妥当な値が入っていなかった場合、例外処理を発生させ、然るべきエラーメッセージを表示する事を確認するテスト。
     * @param int $frame_num - スコアを計算したいフレームの番号
     * @param $expected - 期待値
     */
    public function calculateScore_ThrowsExpectedException_WhenFrameNumIsInvalid(array $result_of_all_attempts, int $frame_num, $expected)
    {

        try{
            $this->ins = new BowlingScoreSheet($result_of_all_attempts);
            $this->executeMethod($this->class_name, 'calculateScore',$this->ins,[$frame_num]);
            $this->fail('例外発生なし');
        }catch (Exception $e){
            $actual = $e->getMessage();
            $this->assertSame($expected, $actual);
        }

    }




    /**
     * @dataProvider getPinNum_ReturnsExpectedValue
     */
    public function getPinNum_ReturnsExpectedValue_DataProvider()
    {
        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(1);

        $result_of_all_attempts_for_tests[0][0][0] = '1';

        return [

            //#0 指定したフレームの投球で倒したピンの数を返す事。空文字は0として扱う事。
            [
                $result_of_all_attempts_for_tests[0],
                1,
                1,
                1
            ]

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-getPinNum_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-getPinNum_ReturnsExpectedValue
     * @dataProvider getPinNum_ReturnsExpectedValue_DataProvider
     *
     * getPinNumメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - 倒したピンの数を確認したい投球を含んだフレームの番号。
     * @param int $nth_attempt -　倒したピンの数を確認したい投球の番号。
     * @param $expected - 期待値
     */
    public function getPinNum_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, int $nth_attempt, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'getPinNum',$this->ins,[$frame_num, $nth_attempt,]);
        $this->assertSame($expected, $actual);

    }




    /**
     * @dataProvider getPinNum_ThrowsExceptionAndShowsMsg_WhenCannotGetPinNum
     */
    public function getPinNum_ThrowsExceptionAndShowsMsg_WhenCannotGetPinNum_DataProvider()
    {
        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);


        $err_msgs[0] = sprintf($properties['errors']['2003']['msgs'][$properties['err_msg_lang']['exception']],1).'BowlingScoreSheet::getPinNum.';

        return [

            //#0　倒したピンの数が取得出来なかった場合、例外処理を発生させ、然るべきメッセージを表示させる事。
            [
                $result_of_all_attempts_for_tests[0],
                11,
                1,
                $err_msgs[0]
            ]

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-getPinNum_ThrowsExceptionAndShowsMsg_WhenCannotGetPinNum tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-getPinNum_ThrowsExceptionAndShowsMsg_WhenCannotGetPinNum
     * @dataProvider getPinNum_ThrowsExceptionAndShowsMsg_WhenCannotGetPinNum_DataProvider
     *
     * getPinNumが倒したピンの数が取得出来なかった場合、例外処理を発生させ、然るべきメッセージを表示させる事を確認するテスト。
     *
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - 倒したピンの数を確認したい投球を含んだフレームの番号。
     * @param int $nth_attempt -　倒したピンの数を確認したい投球の番号。
     * @param $expected - 期待値
     */
    public function getPinNum_ThrowsExceptionAndShowsMsg_WhenCannotGetPinNum(array $result_of_all_attempts, int $frame_num, int $nth_attempt, $expected)
    {

        try{
            $this->ins = new BowlingScoreSheet($result_of_all_attempts);
            $this->executeMethod($this->class_name, 'getPinNum',$this->ins,[$frame_num, $nth_attempt]);
            $this->fail('例外発生なし');
        }catch (Exception $e){
            $actual = $e->getMessage();
            $this->assertSame($expected, $actual);
        }

    }




    /**
     * @dataProvider isStrike_ReturnsExpectedValue
     */
    public function isStrike_ReturnsExpectedValue_DataProvider()
    {

        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(5);


        //#0 指定したフレーム（第10フレーム以外）がストライクだった場合、trueを返す
        $result_of_all_attempts_for_tests[0][0] = ['10','0'];
        $frame_nums[0] = 1;
        $nth_attempts[0] = null;
        $expected[0] = true;


        //#1 指定したフレーム（第10フレーム以外）がストライクでなかった場合、falseを返す
        $result_of_all_attempts_for_tests[1][0] = ['9','1'];
        $frame_nums[1] = 1;
        $nth_attempts[1] = null;
        $expected[1] = false;


        //第10フレームが指定されていて、かつ、ストライクかどうか判定したい投球番号が1～3の間で、かつ、ストライクだった場合trueを返す
        //#2 1投目が指定されていた場合
        $result_of_all_attempts_for_tests[2][9] = ['10','0','0'];
        $frame_nums[2] = 10;
        $nth_attempts[2] = 1;
        $expected[2] = true;

        //#3 3投目が指定されていた場合
        $result_of_all_attempts_for_tests[3][9] = ['5','5','10'];
        $frame_nums[3] = 10;
        $nth_attempts[3] = 3;
        $expected[3] = true;


        //第10フレームが指定されていて、かつ、ストライクかどうか判定したい投球番号が1～3の間で、かつ、ストライクでなかった場合falseを返す
        //#4 1投目が指定されていた場合
        $result_of_all_attempts_for_tests[4][9] = ['9','0','0'];
        $frame_nums[4] = 10;
        $nth_attempts[4] = 1;
        $expected[4] = false;

        //#5 3投目が指定されていた場合
        $result_of_all_attempts_for_tests[5][9] = ['5','5','9'];
        $frame_nums[5] = 10;
        $nth_attempts[5] = 3;
        $expected[5] = false;


        return [

            //#0 指定したフレーム（第10フレーム以外）がストライクだった場合、trueを返す
            [
                $result_of_all_attempts_for_tests[0],
                $frame_nums[0],
                $nth_attempts[0],
                $expected[0]
            ],

            //#1 指定したフレーム（第10フレーム以外）がストライクでなかった場合、falseを返す
            [
                $result_of_all_attempts_for_tests[1],
                $frame_nums[1],
                $nth_attempts[1],
                $expected[1]
            ],

            //第10フレームが指定されていて、かつ、ストライクかどうか判定したい投球番号が1～3の間で、かつ、ストライクだった場合trueを返す
            [//#2 1投目が指定されていた場合
                $result_of_all_attempts_for_tests[2],
                $frame_nums[2],
                $nth_attempts[2],
                $expected[2]
            ],
            [//#3 3投目が指定されていた場合
                $result_of_all_attempts_for_tests[3],
                $frame_nums[3],
                $nth_attempts[3],
                $expected[3]
            ],

            //第10フレームが指定されていて、かつ、ストライクかどうか判定したい投球番号が1～3の間で、かつ、ストライクでなかった場合falseを返す
            [//#4 1投目が指定されていた場合
                $result_of_all_attempts_for_tests[4],
                $frame_nums[4],
                $nth_attempts[4],
                $expected[4]
            ],
            [//#5 3投目が指定されていた場合
                $result_of_all_attempts_for_tests[5],
                $frame_nums[5],
                $nth_attempts[5],
                $expected[5]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-isStrike_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-isStrike_ReturnsExpectedValue
     * @dataProvider isStrike_ReturnsExpectedValue_DataProvider
     *
     * isStrikeメソッドが期待値を返すかのテスト。
     * @param int $frame_num - ストライクか否かをチェックしたいフレームの番号。
     * @param int or null $attempt_to_check_in_final_frame - ストライクか否かを判定する最終フレームの投球番号。1 or 2 or 3のみ指定可能。
     * @param $expected - 期待値
     */
    public function isStrike_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, $attempt_to_check_in_final_frame, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'isStrike',$this->ins,[$frame_num,$attempt_to_check_in_final_frame]);
        $this->assertSame($expected, $actual);

    }





    /**
     * @dataProvider isStrike_ThrowsExpectedException_WhenArgsAreInvalid
     */
    public function isStrike_ThrowsExpectedException_WhenArgsAreInvalid_DataProvider()
    {
        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(4);

        $err_msgs['2004'] =  $properties['errors']['2004']['msgs'][$properties['err_msg_lang']['exception']].'BowlingScoreSheet::isStrike.';
        $err_msgs['2002'] =  $properties['errors']['2002']['msgs'][$properties['err_msg_lang']['exception']].'BowlingScoreSheet::isStrike.';



        //フレーム番号が妥当なものでなかった場合、例外処理を発生させ、然るべきメッセージを表示させる。
        //#0 フレーム番号が下限を下回っていた場合
        $frame_nums[0] = 0;
        $nth_attempts[0] = null;
        $expected[0] = $err_msgs['2004'];

        //#1 フレーム番号が上限を上回っていた場合
        $frame_nums[1] = 11;
        $nth_attempts[1] = null;
        $expected[1] = $err_msgs['2004'];


        //第10フレームが指定されていて、
        //かつ、ストライクかどうか判定したい投球の値が1、2、3のいずれかで無い場合
        //例外を発生させ、然るべきエラーメッセージを表示させる事。
        //#2 投球番号が下限を下回っていた場合。
        $frame_nums[2] = 10;
        $nth_attempts[2] = 0;
        $expected[2] = $err_msgs['2002'];

        //#3 投球番号が上限を超えていた場合。
        $frame_nums[3] = 10;
        $nth_attempts[3] = 4;
        $expected[3] = $err_msgs['2002'];


        return [

            //フレーム番号が妥当なものでなかった場合、例外処理を発生させ、然るべきメッセージを表示させる。
            [//#0 フレーム番号が下限を下回っていた場合
                $result_of_all_attempts_for_tests[0],
                $frame_nums[0],
                $nth_attempts[0],
                $expected[0]
            ],
            [//#1 フレーム番号が上限を上回っていた場合
                $result_of_all_attempts_for_tests[1],
                $frame_nums[1],
                $nth_attempts[1],
                $expected[1]
            ],

            //第10フレームが指定されていて、
            //かつ、ストライクかどうか判定したい投球の値が1、2、3のいずれかで無い場合
            //例外を発生させ、然るべきエラーメッセージを表示させる事。
            [//#2 投球番号が下限を下回っていた場合。
                $result_of_all_attempts_for_tests[2],
                $frame_nums[2],
                $nth_attempts[2],
                $expected[2]
            ],
            [//#3 投球番号が上限を超えていた場合。
                $result_of_all_attempts_for_tests[3],
                $frame_nums[3],
                $nth_attempts[3],
                $expected[3]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-isStrike_ThrowsExpectedException_WhenArgsAreInvalid tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-isStrike_ThrowsExpectedException_WhenArgsAreInvalid
     * @dataProvider isStrike_ThrowsExpectedException_WhenArgsAreInvalid_DataProvider
     *
     * isStrikeメソッドに妥当な引数が入っていなかった場合、例外処理を発生させ、然るべきメッセージを表示する事を確認するテスト。
     * @param int $frame_num - ストライクか否かをチェックしたいフレームの番号。
     * @param int or null $attempt_to_check_in_final_frame - ストライクか否かを判定する最終フレームの投球番号。1 or 2 or 3のみ指定可能。
     * @param $expected - 期待値
     */
    public function isStrike_ThrowsExpectedException_WhenArgsAreInvalid(array $result_of_all_attempts, int $frame_num, $attempt_to_check_in_final_frame, $expected)
    {
        try{
            $this->ins = new BowlingScoreSheet($result_of_all_attempts);
            $this->executeMethod($this->class_name, 'isStrike',$this->ins,[$frame_num,$attempt_to_check_in_final_frame]);
            $this->fail('例外発生なし');
        }catch (Exception $e){
            $actual = $e->getMessage();
            $this->assertSame($expected, $actual);
        }
    }




    /**
     * @dataProvider isSpare_ReturnsExpectedValue
     */
    public function isSpare_ReturnsExpectedValue_DataProvider()
    {
        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);

        //#0 第1～9フレームの内のある1つのフレームが指定されていて、そのフレームがスペアだった場合、trueを返す事。
        $result_of_all_attempts_for_tests[0][0] = [9,1];
        $frame_nums[0] = 1;
        $expected[0] = true;

        //#1 第1～9フレームの内のある1つのフレームが指定されていて、そのフレームがスペアでなかった場合、falseを返す事。
        $result_of_all_attempts_for_tests[1][8] = [8,1];
        $frame_nums[1] = 9;
        $expected[1] = false;

        return [

            //#0 第1～9フレームの内のある1つのフレームが指定されていて、そのフレームがスペアだった場合、trueを返す事。
            [
                $result_of_all_attempts_for_tests[0],
                $frame_nums[0],
                $expected[0],
            ],

            //#1 第1～9フレームの内のある1つのフレームが指定されていて、そのフレームがスペアでなかった場合、falseを返す事。
            [
                $result_of_all_attempts_for_tests[1],
                $frame_nums[1],
                $expected[1],
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-isSpare_ReturnsExpectedValue tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-isSpare_ReturnsExpectedValue
     * @dataProvider isSpare_ReturnsExpectedValue_DataProvider
     *
     * isSpareメソッドが期待値を返すかのテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - スペアか否かを確認したいフレームの番号。
     * @param $expected - 期待値
     */
    public function isSpare_ReturnsExpectedValue(array $result_of_all_attempts, int $frame_num, $expected)
    {
        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
        $actual = $this->executeMethod($this->class_name, 'isSpare',$this->ins,[$frame_num]);
        $this->assertSame($expected, $actual);

    }




    /**
     * @dataProvider isSpare_ThrowsExpectedException_WhenFrameNumIsInvalid
     */
    public function isSpare_ThrowsExpectedException_WhenFrameNumIsInvalid_DataProvider()
    {
        //期待されるエラーメッセージを作成するため、BowlingScoreSheetクラスのプロパティを取得する。
        $properties_to_get = ['err_msg_lang','errors'];
        $properties = $this->getProperties($this->class_name,$properties_to_get,$this->result_of_all_attempts_base);

        //ベースとなる投球結果を、テストの数の分、用意する
        $result_of_all_attempts_for_tests = $this->provideAllAttemptsResults(2);

        $expected[0] = $expected[1] =  $properties['errors']['2005']['msgs'][$properties['err_msg_lang']['exception']].'BowlingScoreSheet::isSpare.';

        return [

            //フレーム番号に1～9以外の値が指定されていた場合、例外処理を発生させ、然るべきメッセージを表示させる事。
            [//#0 フレーム番号が9を超えていた場合
                $result_of_all_attempts_for_tests[0],
                10,
                $expected[0]
            ],

            [//#1 フレーム番号が1を下回っていた場合
                $result_of_all_attempts_for_tests[1],
                0,
                $expected[1]
            ],

        ];
    }


    /**
     *
     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-isSpare_ThrowsExpectedException_WhenFrameNumIsInvalid tests/BowlingScoreSheetTest.php
     * @test
     * @group Tests-BowlingScoreSheetTest-isSpare_ThrowsExpectedException_WhenFrameNumIsInvalid
     * @dataProvider isSpare_ThrowsExpectedException_WhenFrameNumIsInvalid_DataProvider
     *
     * isSpareメソッドのフレーム番号に1～9以外の値が指定されていた場合、例外処理を発生させ、然るべきメッセージを表示させる事を確認するテスト。
     * @param array $result_of_all_attempts - 全ての投球結果
     * @param int $frame_num - スペアか否かを確認したいフレームの番号。
     * @param $expected - 期待値
     */
    public function isSpare_ThrowsExpectedException_WhenFrameNumIsInvalid(array $result_of_all_attempts, int $frame_num, $expected)
    {

        try{
            $this->ins = new BowlingScoreSheet($result_of_all_attempts);
            $actual = $this->executeMethod($this->class_name, 'isSpare',$this->ins,[$frame_num]);
            $this->fail('例外発生なし');
        }catch (Exception $e){
            $actual = $e->getMessage();
            $this->assertSame($expected, $actual);
        }

    }





//実際の入力画面での動作確認で使用したデータのスコア（http://www.bowlinggenius.com/で計算を行なったもの）と、
//calculateScoresでそのデータを元に計算したスコアを照合するテスト。
//このテストを行う場合、以下のコメントアウトを外す。
//    /**
//     * @dataProvider calculateScores_ReturnsExpectedValue_WhenActualResultIsGiven
//     */
//    public function calculateScores_ReturnsExpectedValue_WhenActualResultIsGiven_DataProvider()
//    {
//
//
//        return [
//
//            [
//                [
//                    ['1', '0'], ['4', '6'], ['3', '5'], ['7', '3'], ['2', '1'], ['0', '2'], ['2', '0'], ['1', '0'], ['2', '2'], ['10', '4', '1']
//                ],
//                [
//                    1,14,22,34,37,39,41,42,46,61
//                ]
//            ],
//            [
//                [
//                    ['3', '4'], ['10', ''], ['9', '1'], ['10', ''], ['10', ''], ['5', '4'], ['10', ''], ['10', ''], ['9', '1'], ['9', '1', '10']
//                ],
//                [
//                    7,27,47,72,91,100,129,149,168,188
//                ]
//            ],
//
//            [
//                [
//                    ['5', '5'], ['10', ''], ['10', ''], ['9', '1'], ['5', '4'], ['10', ''], ['8', ''], ['10', ''], ['10', ''], ['8', '2', '10']
//                ],
//                [
//                    20,49,69,84,93,111,119,147,167,187
//                ]
//            ],
//            [
//                [
//                    ['7', '3'], ['7', '3'], ['10', ''], ['5', '5'], ['9', '1'], ['10', ''], ['7', '3'], ['10', ''], ['7', '2'], ['7', '3', '10']
//                ],
//                [
//                    17,37,57,76,96,116,136,155,164,184
//                ]
//            ],
//
//            [
//                [
//                    ['10', ''], ['9', '1'], ['10', ''], ['7', '3'], ['10', ''], ['9', '1'], ['10', ''], ['8', '2'], ['10', ''], ['9', '1', '10']
//                ],
//                [
//                    20,40,60,80,100,120,140,160,180,200
//                ]
//            ],
//            [
//                [
//                    ['10', ''], ['9', '1'], ['5', '5'], ['6', '4'], ['7', '1'], ['10', ''], ['7', '3'], ['10', ''], ['10', ''], ['10', '10', '10']
//                ],
//                [
//                    20,35,51,68,76,96,116,146,176,206
//                ]
//            ],
//            [
//                [
//                    ['5', '3'], ['7', '3'], ['1', '4'], ['6', '3'], ['1', '3'], ['5', '2'], ['4', '6'], ['3', '6'], ['7', '3'], ['2', '8', '6']
//                ],
//                [
//                    8,19,24,33,37,44,57,66,78,94
//                ]
//            ],
//            [
//                [
//                    ['10', ''], ['9', '1'], ['10', ''], ['10', ''], ['9', '1'], ['8', '2'], ['9', '1'], ['10', ''], ['10', ''], ['7', '3', '10']
//                ],
//                [
//                    20,40,69,89,107,126,146,173,193,213
//                ]
//            ],
//            [
//                [
//                    ['9', '1'], ['5', '5'], ['7', '3'], ['3', '7'], ['7', '3'], ['3', '3'], ['9', '1'], ['9', '1'], ['9', '1'], ['10', '10', '10']
//                ],
//                [
//                    15,32,45,62,75,81,100,119,139,169
//                ]
//            ],
//            [
//                [
//                    ['10', ''], ['9', '1'], ['10', ''], ['9', '1'], ['10', ''], ['10', ''], ['9', '1'], ['10', ''], ['9', '1'], ['10', '10', '10']
//                ],
//                [
//                    20,40,60,80,109,129,149,169,189,219
//                ]
//            ],
//        ];
//    }
//
//
//    /**
//     *
//     * php tests/phpunit.phar --group Tests-BowlingScoreSheetTest-calculateScores_ReturnsExpectedValue_WhenActualResultIsGiven tests/BowlingScoreSheetTest.php
//     * @test
//     * @group Tests-BowlingScoreSheetTest-calculateScores_ReturnsExpectedValue_WhenActualResultIsGiven
//     * @dataProvider calculateScores_ReturnsExpectedValue_WhenActualResultIsGiven_DataProvider
//     *
//     * calculateScoresに実際のデータが入れられた場合期待値を返すかテスト。
//     * @param array $result_of_all_attempts - 全ての投球結果。
//     * @param array $expected - 期待値。
//     */
//    public function calculateScores_ReturnsExpectedValue_WhenActualResultIsGiven(array $result_of_all_attempts,  $expected)
//    {
//        $this->ins = new BowlingScoreSheet($result_of_all_attempts);
//        $actual = $this->executeMethod($this->class_name, 'calculateScores',$this->ins);
//        $this->assertSame($expected, $actual);
//    }
}