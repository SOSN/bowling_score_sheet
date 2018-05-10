<?php
declare(strict_types=1);
//Namespace App;
    class BowlingScoreSheet{

        //全てのフレームの投球結果を格納する。
        private $result_of_all_attempts = [];

        //各フレームのスコアを加算形式で格納する。
        private $scores_to_display = [];

        //妥当な投球結果がresult_of_all_attemptsプロパティに入れられればtrue、そうでなければfalseが入る
        private $is_valid_result = false;

        //テスト用。validateFinalFrameメソッドが実行された回数。
        private $times_validate_final_frame_executed = 0;

        //テスト用。validateFramesメソッドが実行された回数。
        private $times_validate_frames_executed = 0;

        //テスト用。validateResultメソッドが実行された回数。
        private $times_validate_results_executed = 0;

        //テスト用。calculateScoreメソッドが実行された回数。
        private $times_calculate_score_executed = 0;

        //テスト用。validateFrameKeyメソッドが実行された回数。
        private $times_validate_frame_key_executed = 0;

        //テスト用。validateAttemptsメソッドが実行された回数。
        private $times_validate_attempts_executed = 0;

        //エラーメッセージの言語設定
        private $err_msg_lang = [
            'validation' => 'jp', //ヴァリデーションのエラーメッセージの言語設定
            'exception' => 'en' //例外処理のエラーメッセージの言語設定
        ];

        //エラー設定。エラーコード、エラータイトル、各言語毎のエラーメッセージが格納される。
        private $errors = [
            '1001'=>[
                'title'=>'Invalid Number Of Frames',
                'msgs' => [
                    'en' => 'The number of frames must be 10',
                    'jp' => '投球結果は10フレーム分ある必要があります。',
                ]
            ],
            '1002'=>[
                'title'=>'Invalid Sum In A Frame',
                'msgs' => [
                    'en' => 'The sum of knocked down pins in Frame %d must be 0 to 10',
                    'jp' => '第%dフレームで倒したピンの合計は0～10ピン以内である必要があります。',
                ]
            ],
            '1003'=>[
                'title'=>'Invalid Num Of Attempts In A Final Frame',
                'msgs' => [
                    'en' => 'The number of possible attempts in Frame 10 must be three',
                    'jp' => '第10フレームは3投分のデータが必要です。',
                ]
            ],
            '1004'=>[
                'title'=>'Over Pin Num In An Attempt',
                'msgs' => [
                    'en' => 'There is an attempt whose number of knocked down pins is over 10 in Frame %d',
                    'jp' => '第%dフレームに10を超えるピンを倒した投球があります。',
                ]
            ],
            '1005'=>[
                'title'=>'Negative Pin Num In An Attempt',
                'msgs' => [
                    'en' => 'There is an attempt whose number of knocked down pins is negative in Frame %d',
                    'jp' => '第%dフレームにマイナスの数値が入った投球があります。',
                ]
            ],
            '1006'=>[
                'title'=>'Invalid Sum Of 1st And 2nd Attempts In A Final Frame',
                'msgs' => [
                    'en' => 'The number of knocked down pins in 1st and 2nd attempts in Frame 10 is invalid',
                    'jp' => '第10フレームの1～2投目で倒したピンの数が不正です。',
                ]
            ],
            '1007'=>[
                'title'=>'Invalid Sum Of 1st And 2nd Attempts In A Final Frame',
                'msgs' => [
                    'en' => 'To throw third attempt in Frame 10, the player need to strike in first attempt or spare in 2nd attempt',
                    'jp' => '第10フレームの3投目を投げるには、1投目でストライクを出すか、2投目でスペアを出す必要性があります。',
                ]
            ],
            '1008'=>[
                'title'=>'Invalid Keys For Frames',
                'msgs' => [
                    'en' => 'The key of the array which contains Frame %d is invalid.',
                    'jp' => '第%dフレームが格納されている配列のキーが妥当な値ではありません。',
                ]
            ],
            '1009'=>[
                'title'=>'Invalid Keys For Attempt',
                'msgs' => [
                    'en' => 'At least one of the keys of the attempt in Frame %d is invalid.',
                    'jp' => '第%dフレームの投球が格納されている要素のキーの内の少なくとも一つが、妥当な値ではありません。',
                ]
            ],
            '1010'=>[
                'title'=>'Invalid Num Of Attempts',
                'msgs' => [
                    'en' => 'The number of possible attempts in Frame %d must be two',
                    'jp' => '第%dフレームの投球枠は2つである必要があります。',
                ]
            ],

            //'The key of the array which contains an attempt in Frame %d is invalid.'
            '2001'=>[
                'title'=>'Cannot Generate Error Message',
                'msgs' => [
                    'en' => 'Cannot generate an error message at ',
                ]
            ],
            '2002'=>[
                'title'=>'Cannot Generate Error Message',
                'msgs' => [
                    'en' => 'To check whether a certain attempt in final frame is strike or not, please specify 1 or 2 or 3 to $attempt_to_check_in_final_frame of ',
                ]
            ],
            '2003'=>[
                'title'=>'Cannot Get The Num Of Knocked-Down Pins',
                'msgs' => [
                    'en' => 'Cannot get the number of knocked-down pins of the attempt you specified in ',
                ]
            ],
            '2004'=>[
                'title'=>'Invalid Frame Number 1',
                'msgs' => [
                    'en' => '$frame_num has to be in the range between 1 to 10 and a type of integer in ',
                ]
            ],
            '2005'=>[
                'title'=>'Invalid Frame Number 2',
                'msgs' => [
                    'en' => '$frame_num has to be in the range between 1 to 9 and a type of integer in ',
                ]
            ],
            '2006'=>[
                'title'=>'Cannot Generate Error Message',
                'msgs' => [
                    'en' => '$frame_num is necessary to make the error message of ',
                ]
            ],


        ];

        /**
         * コンストラクタ。全てのフレームの投球結果を要求し、それをresult_of_all_attemptsプロパティに格納。
         * @param array $result_of_all_attempts - 全てのフレームの投球結果
         *
         */
        function __construct(array $result_of_all_attempts){
            $this->result_of_all_attempts = $result_of_all_attempts;
        }

        /**
         * is_valid_resultプロパティの値を返す。妥当な投球結果が入れられればtrue、そうでなければfalseを返す。
         * @return bool - is_valid_resultプロパティ
         */
        function isValidResult():bool{
            return $this->is_valid_result;
        }

        /**
         * 投球結果の総フレーム数が10フレームであればtrue、そうでなければfalseを返す
         * @return bool
         */
        private function isValidNumOfFrames():bool{
            return count($this->result_of_all_attempts) === 10;
        }

        /**
         * 指定されたフレームの投球結果の合計を返す
         * @param int $frame_num - 倒されたピンの合計を取得したいフレームの番号
         * @return int - そのフレームで倒されたピンの合計。
         */
        private function getSumOfPinsInFrame(int $frame_num):int{
            return array_sum($this->result_of_all_attempts[$frame_num - 1]);
        }

        /**
         * ある一つの投球で倒したピンの数が0～10ピン以内であるかを調べ、その範囲内であるならば、null、
         * そうでなければそれぞれのエラーに応じたエラーコードを返す
         *
         * @param int $result_of_attempt - 倒したピンの数
         * @return null or string $err_code - ある一つの投球で倒したピンの数が0～10ピン以内であるかを調べ、その範囲内であるならば、null、
         * そうでなければそれぞれのエラーに応じたエラーコードを返す。
         */
        private function validateAttempt(int $result_of_attempt){
            $err_code = null;
            if($result_of_attempt > 10){
                $err_code = '1004';
            }elseif($result_of_attempt < 0){
                $err_code = '1005';
            }
            return $err_code;
        }


        /**
         * 指定したフレーム内の各投球で倒したピンの数が、それぞれ0～10ピン以内であるかを調べる。
         * あくまで一投一投の妥当性を検査し、そのフレーム全体の投球の妥当性を検証するものではない事に注意。
         *
         * @param int $frame_num - ヴァリデーションをかけたい投球を含むフレームの番号
         * @return null or string $err_msg - あるフレーム内の各投球で倒したピンの数がそれぞれ0～10ピン以内であるかを調べ、
         * その範囲内であるならばnull、そうでなければそれぞれのエラーに応じたエラーメッセージを返す。
         */
        private function validateAttempts(int $frame_num){
            $err_msg = null;
            $this->times_validate_attempts_executed += 1;
            $i = 0;
            $err_type = 'validation';
            foreach ($this->result_of_all_attempts[$frame_num - 1] as $key => $result_of_attempt) {
                if($i !== $key ){
                    $err_msg = $this->generateErrorMsg('1009', $err_type, $frame_num);
                    break;
                }else{
                    $err_code = $this->validateAttempt((int)$result_of_attempt);
                    if($err_code !== null){
                        $err_msg =  $this->generateErrorMsg($err_code, $err_type, $frame_num);
                        break;
                    }
                }
                ++$i;
            }
            return $err_msg;
        }


        /**
         * 最終フレームの投球結果にヴァリデーションをかける。
         *
         * @param int $frame_num - ヴァリデーションをかけるフレームの番号
         * @return null or string $err_msg - 正常なデータであればnullを返し、エラーがあればそれぞれのエラーに応じたエラーメッセージを返す。
         */
        private function validateFinalFrame(int $frame_num){
            $this->times_validate_final_frame_executed += 1;
            $err_msg = null;
            $final_frame_attempts = $this->result_of_all_attempts[$frame_num - 1];

            if(count($final_frame_attempts) !== 3){//最終フレームの投球枠が３つ無い場合、エラーメッセージを返す。
                return $this->generateErrorMsg('1003');
            }

            $err_msg = $this->validateAttempts($frame_num);
            if($err_msg !== null){//各投球結果が0～10の範囲内でない場合、エラーメッセージを返す。
                return $err_msg;
            }

            if(((int)$final_frame_attempts[0] !== 10)
                &&  ((int)$final_frame_attempts[0] + (int)$final_frame_attempts[1] > 10)
            ){//一投目がストライクで無いのに、一投目と二投目の合計が10を超えていた場合、エラーメッセージを返す
                return $this->generateErrorMsg('1006');
            }

            if(((int)$final_frame_attempts[0] !== 10)
                && ((int)$final_frame_attempts[0] + (int)$final_frame_attempts[1] !== 10)
                &&  ((int)$final_frame_attempts[2] > 0 )
            ){//1投目がストライクでなく、2投目がスペアでも無い場合に、3投目でピンが倒れていた場合、エラーメッセージを返す
                return $this->generateErrorMsg('1007');
            }
            return $err_msg;
        }


        /**
         * 指定したフレームの投球結果のデータにヴァリデーションをかける。
         *
         * @param int $frame_num - ヴァリデーションをかけるフレーム番号
         * @return null or string $err_msg - 正常なデータであればnullを返し、エラーがあればそれぞれに応じたエラーメッセージを返す。
         */
        private function validateFrame(int $frame_num){
            $sum_of_each_frame = $this->getSumOfPinsInFrame($frame_num);
            $err_msg = null;
            if($frame_num !==10){//最終フレーム以外のヴァリデーション
                if(count($this->result_of_all_attempts[$frame_num - 1]) !== 2){
                    $err_msg = $this->generateErrorMsg('1010', 'validation', $frame_num);
                }else{
                    $err_msg = $this->validateAttempts($frame_num);
                    if($err_msg === null && $sum_of_each_frame > 10){
                        $err_msg = $this->generateErrorMsg('1002', 'validation', $frame_num);
                    }
                }
            }elseif($frame_num === 10){//最終フレームのヴァリデーション
                $err_msg = $this->validateFinalFrame($frame_num);
            }
            return $err_msg;
        }

        /**
         * 全ての投球結果のデータにヴァリデーションをかける。
         *
         * @return null or array - 正常なデータであればnullを返し、
         * エラーがあればそれぞれのフレームのエラーメッセージを格納した配列を返す。
         * フレームの数が10無かった場合は、そのエラーメッセージを配列に入れて返す。
         */
        public function validateResult(){
            $this->times_validate_results_executed += 1;
            //送られてきた投球結果が10フレームであるかを確認
            if(!$this->isValidNumOfFrames()){//10フレームなかった場合はエラーメッセージを送る。
                return [$this->generateErrorMsg('1001')];
            }

            $err_msgs = $this->validateFrames();
            if($err_msgs === null){
                $this->is_valid_result = true;
            }

            return $err_msgs;
        }


        /**
         * フレームを格納する配列に指定されているキーが妥当なものかを検査する。
         * @param $key - フレームを格納する配列に指定されていたキー
         * @param $frame_num - フレーム番号
         * @return null or string $err_msg - フレームを格納する配列に指定されていたキーが妥当ならnull、そうでなければエラーメッセージを返す
         */
        private function validateFrameKey($key,int $frame_num){
            $this->times_validate_frame_key_executed += 1;
            $err_msg = null;
            if(($frame_num - 1) !== $key){
                $err_msg = $this->generateErrorMsg('1008', 'validation', $frame_num);
            }
            return $err_msg;
        }


        /**
         * 全てのフレームの投球結果の妥当性を検査する。
         *
         * @return null or array - 正常なデータであればnullを返し、
         * エラーがあればそれぞれのフレームのエラーメッセージを格納した配列を返す。
         */
        private function validateFrames(){
            $this->times_validate_frames_executed += 1;

            //各フレームのvalidationを行う
            $frame_num = 1;
            $err_msgs = [];
            foreach ($this->result_of_all_attempts as $key => $result_of_all_attempt) {

                $err_msg = $this->validateFrameKey($key,$frame_num);
                if($err_msg === null){
                    $err_msg = $this->validateFrame($frame_num);
                }

                if($err_msg !== null){
                    $err_msgs[] = $err_msg;
                }
                ++$frame_num;
            }

            if(empty($err_msgs)){//エラーメッセージが無い場合nullを返す。
                $err_msgs = null;
            }

            return $err_msgs;
        }



        /**
         *
         * エラーコードとフレーム番号からエラーメッセージを作成する。
         *
         * @param string $err_code -  エラーコード
         * @param string $err_type -  エラータイプ。現在'validation'、'exception'が利用可能
         * @param int $frame_num -  エラーのあったフレームの番号
         * @return string $err_msg - エラーメッセージ
         * @throws Exception - エラーメッセージを作り出す事が出来なかった場合。
         *
         */
        public function generateErrorMsg(string $err_code, string $err_type = 'validation', int $frame_num = null):string{
            try {
                $err_msg =  $this->errors[$err_code]['msgs'][$this->err_msg_lang[$err_type]];
                if(!empty($err_msg) && strpos($err_msg,'%d')){
                    if($frame_num !== null){
                        $err_msg = sprintf($err_msg, $frame_num);
                    }else{
                        throw new Exception('2006');
                    }
                }
                return $err_msg;
            } catch (Exception $e) {
                if($e->getMessage() === '2006'){
                    throw new Exception($this->errors['2006']['msgs'][$this->err_msg_lang['exception']].$err_code.' '.__method__.'.');
                }else{
                    throw new Exception($this->errors['2001']['msgs'][$this->err_msg_lang['exception']].__method__.'.');
                }

            }

        }

        /**
         * 送られてきた全ての投球結果にヴァリデーションをかけた後、スコアシートに表示する得点を配列で返す。
         *
         * @return array - スコアシートに表示する得点、またはエラーメッセージを配列で返す。
         */
        public function calculateScores(){
            $err_msgs = $this->validateResult();
            if($err_msgs !== null){
                return $err_msgs;
            }
            $this->scores_to_display = [];
            for($i=0;$i<=9;++$i){

                $frame_num = $i + 1;
                $score_to_add = $this->calculateScore($frame_num);

                if($frame_num > 1){
                    $score_to_add = $this->scores_to_display[$i - 1] + $score_to_add;
                }
                $this->scores_to_display[$i] = $score_to_add;
            }
            return $this->scores_to_display;
        }
        
        /**
         * 指定したフレームのスコアを計算する。
         * @param int $frame_num - スコアを計算したいフレームの番号
         * @return int $score - そのフレームのスコア
         */
        private function calculateScore(int $frame_num):int{
            $this->times_calculate_score_executed += 1 ;

            $err_type = "exception";
            if($frame_num < 1 || $frame_num > 10){
                throw new Exception($this->generateErrorMsg('2004', $err_type).__method__.".");
            }

            $attempt_to_check = 0;
            if($frame_num === 10 ){//第10フレームのスコアは単純フレーム内で倒したピンの合計。
                $score = $this->getSumOfPinsInFrame($frame_num);

            }else{//第1フレームから第9フレームのスコア計算
                if($this->isStrike($frame_num)){//ストライクだった場合、次がダブルか否かをチェック。
                    $next_frame_num = $frame_num + 1;
                    if($next_frame_num === 10){
                        $attempt_to_check = 1;
                    }
                    if($this->isStrike($next_frame_num,$attempt_to_check)){
                        //ダブルだった場合、20ピン + 次の1投で倒したピンの数
                        $after_next_frame_num = $frame_num + $attempt_to_check + 2;
                        if($after_next_frame_num  > 10){
                            $attempt_to_check = $after_next_frame_num - 10;
                            $after_next_frame_num = 10;
                        }else{
                            $attempt_to_check = 1;
                        }
                        $score = 20 + $this->getPinNum($after_next_frame_num,$attempt_to_check);
                    }else{//ストライクではあるがダブルでない場合、単純に10ピン + 次のフレーム内での2投で倒したピン
                        $score = 10 + $this->getPinNum($next_frame_num, 1) + $this->getPinNum($next_frame_num,2) ;
                    }
                }elseif($this->isSpare($frame_num)){//スペアだった場合
                    $score = 10 + $this->getPinNum($frame_num + 1,1);
                }else{//ストライクでもスペアでもなかった場合
                    $score = $this->getSumOfPinsInFrame($frame_num);
                }
            }
            return $score;
        } 
        

        /**
         * 指定したフレームの投球で倒したピンの数を取得する事。
         *
         * @param int $frame_num - 倒したピンの数を確認したい投球を含んだフレームの番号。
         * @param int $nth_attempt -　倒したピンの数を確認したい投球の番号。
         * @return int $pins - 倒したピンの数。
         * @throw Exception - 倒したピンの数が取得出来なかった場合、例外処理。
         */
        private function getPinNum(int $frame_num,int $nth_attempt):int{

            try{
                return (int)$this->result_of_all_attempts[$frame_num - 1][$nth_attempt - 1];
            }catch (Exception $e){
                throw new Exception($this->generateErrorMsg('2003', 'exception').__method__.'.');
            }

        }


        /**
         *　あるフレーム、あるいは最終フレームの特定の投球がストライクか否かを調べる。
         *
         * @param int $frame_num - ストライクか否かをチェックしたいフレームの番号。
         * @param int or null $attempt_to_check_in_final_frame -
         * ストライクか否かを判定する最終フレームの投球番号。1 or 2 or 3のみ指定可能。
         * @return bool $is_strike - ストライクがだった場合true、そうでなければfalseを返す。
         *
         * @throws Exception - $frame_numに1～10の整数以外が入っていた場合に例外処理。
         * $frame_numが10の場合、$attempt_to_check_in_final_frameに1,2,3以外の値が入っていた場合に例外処理。
         */
        private function isStrike(int $frame_num,int $attempt_to_check_in_final_frame = null):bool{
            $err_type = "exception";
            if($frame_num < 1 || $frame_num > 10){
                throw new Exception($this->generateErrorMsg('2004', $err_type).__method__.".");
            }
            if($frame_num !== 10){
                $is_strike = (int)$this->result_of_all_attempts[$frame_num - 1][0] === 10;
            }else{
                if(in_array($attempt_to_check_in_final_frame, [1,2,3])){
                    $is_strike = (int)$this->result_of_all_attempts[$frame_num - 1][$attempt_to_check_in_final_frame - 1 ] === 10;
                }else{
                    throw new Exception($this->generateErrorMsg('2002',$err_type).__method__.".");
                }
            }
            return $is_strike;
        }



        /**
         * あるフレームがスペアであるか否かを判定する。第1～9フレームのみ対応。第10フレームは不要であるので対応していない。
         *
         * @param int $frame_num - スペアか否かをチェックしたいフレームの番号
         * @return bool - スペアだった場合true、そうでなければfalseを返す。
         *
         * @throws Exception - $frame_numに1～10の整数以外が入っていた場合、例外処理を投げる。
         *
         */
        private function isSpare(int $frame_num):bool{
            if($frame_num < 1 || $frame_num > 9){
                throw new Exception($this->generateErrorMsg('2005','exception').__method__.".");
            }
            return ($this->getSumOfPinsInFrame($frame_num) === 10);
        }

    }
