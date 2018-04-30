<?php
declare(strict_types=1);
//Namespace App;
    class BowlingScoreSheet{

        private $result_of_all_attempts = [];
        private $scores = [];
        private $err_msg_lang = [
            'validation' => 'jp',
            'exception' => 'en'
        ];

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
                    'en' => 'The number of knocked down pins in Frame %d must be 0 to 10',
                    'jp' => '第%dフレームで倒したピンの数は0～10ピン以内である必要があります。',
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
            '2001'=>[
                'title'=>'Cannot generate an error message',
                'msgs' => [
                    'en' => 'Cannot generate an error message',
                ]
            ]
        ];


        function __construct(array $result_of_all_attempts){
            $this->result_of_all_attempts = $result_of_all_attempts;
        }

        /**
         * 送られてきた投球結果のフレーム数が正しければtrue、そうでなければfalseを返す
         * @return bool
         */
        private function isValidNumOfFrames(){
            return count($this->result_of_all_attempts) === 10;
        }

        /**
         * 送られてきたフレーム番号の投球結果の合計を返す
         * @param int $frame_num - フレーム内で倒されたピンの合計を取得するフレームの番号
         * @return bool
         */
        private function getSumOfFrame(int $frame_num){
            return array_sum($this->result_of_all_attempts[$frame_num - 1]);
        }

        /**
         * ある一つの投球で倒したピンの数が0～10ピン以内であるかを調べ、その範囲内であるならば、null、
         * そうでなければそれぞれのエラーに応じたエラーコードを返す
         *
         * @param int $result_of_attempt - 倒したピンの数
         * @return null or string - ある一つの投球で倒したピンの数が0～10ピン以内であるかを調べ、その範囲内であるならば、null、
         * そうでなければそれぞれのエラーに応じたエラーコードを返す。
         */
        private function validateAttempt(int $result_of_attempt){
            $res = null;
            if($result_of_attempt > 10){
                $res = '1004';
            }elseif($result_of_attempt < 0){
                $res = '1005';
            }
            return $res;
        }

        /**
         * ある一つのフレーム内の各投球で倒したピンの数が、それぞれ0～10ピン以内であるかを調べる。
         * あくまで一投一投の妥当性を検査し、そのフレーム全体の妥当性を検証するものではない事に注意。
         *
         * @param int $frame_num - ヴァリデーションをかけたい投球を含んだフレームの番号
         * @return null or string - あるフレーム内の各投球で倒したピンの数がそれぞれ0～10ピン以内であるかを調べ、その範囲内であるならば、null、
         * そうでなければそれぞれのエラーに応じたエラーコードを返す。
         */
        private function validateAttempts(int $frame_num){
            $res = null;
            foreach ($this->result_of_all_attempts[$frame_num - 1] as $result_of_attempt) {
                $res = $this->validateAttempt((int)$result_of_attempt);
                if($res !== null){
                    break;
                }
            }
            return $res;
        }

        /**
         *　最終フレームの投球結果のデータにヴァリデーションをかける。
         *
         * @param int $frame_num - ヴァリデーションをかけるフレーム番号
         * @return null or string - 正常なデータであればnullを返し、エラーがあればそれぞれのエラーコードを返す。
         */
        private function validateFinalFrame($frame_num){
            $res = null;
            $final_frame_attempts = $this->result_of_all_attempts[$frame_num - 1];
            if(count($final_frame_attempts) !== 3){
                return '1003';
            }

            $res = $this->validateAttempts($frame_num);
            if($res !== null){
                return $res;
            }

            if(($final_frame_attempts[0] < 10)
                &&  ($final_frame_attempts[0] + $final_frame_attempts[1] > 10)
            ){
                return '1006';
            }

            if(($final_frame_attempts[0] !== 10)
                && ($final_frame_attempts[0] + $final_frame_attempts[1] !== 10)
                &&  ($final_frame_attempts[2] > 0 )
            ){
                return '1007';
            }
            return $res;
        }

        /**
         * あるフレームの投球結果のデータにヴァリデーションをかける。
         *
         * @param int $frame_num - ヴァリデーションをかけるフレーム番号
         * @return null or string - 正常なデータであればnullを返し、エラーがあればそれぞれのエラーコードを返す。
         */
        private function validateFrame($frame_num){
            $sum_of_each_frame = $this->getSumOfFrame($frame_num);
            $res = null;
            if($frame_num !==10){
                $res = $this->validateAttempts($frame_num);
                if($res === null && $sum_of_each_frame > 10){
                    $res = '1002';
                }
            }elseif($frame_num === 10){
                $res = $this->validateFinalFrame($frame_num);
            }
            return $res;
        }

        /**
         * 全ての投球結果のデータにヴァリデーションをかける。
         *
         * @return null or array or string - 正常なデータであればnullを返し、
         * エラーがあればそれぞれのフレームのエラーのエラーコードを格納した配列を返す。
         * フレームの数が10無かった場合は、そのエラーコードを文字列で返す
         */
        private function validateResults(){
            //送られてきた投球結果が10フレームであるかを確認
            if(!$this->isValidNumOfFrames()){//10フレームなかった場合はエラーメッセージを送る。
                return '1001';
            }
            return $this->validateFrames();
        }

        /**
         * 全ての投球結果のデータにヴァリデーションをかける。
         *
         * @return null or array or string - 正常なデータであればnullを返し、
         * エラーがあればそれぞれのフレームのエラーのエラーコードを格納した配列を返す。
         */
        private function validateFrames(){
            $res = null;

            //各フレームのvalidationを行う
            $frame_num = 1;
            $msgs = [];
            foreach ($this->result_of_all_attempts as $result_of_all_attempt) {
                $msg = $this->validateFrame($frame_num);
                if($msg !== null){
                    $msgs[$frame_num] = $msg;
                }
                ++$frame_num;
            }

            if(!empty($msgs)){
                $this->result_of_all_attempts = [];
                $res = $msgs;
            }

            return $res;
        }



        /**
//         * キーにエラーのあったフレームの番号、要素にエラーコードが入った配列から、
//         * エラーメッセージ群を抽出し、それを配列で返す。
//         * @param $err_codes -  キーにエラーのあったフレームの番号、要素にエラーコードが入った配列
//         * @return
//         */
//        public function generateErrorMsgs($err_codes):array{
//
//            $err_msgs = [];
//            if(!empty($err_codes)) {
//                if(is_string($err_codes)){
//                    $err_msgs = $this->generateErrorMsg($err_codes);
//                }elseif(is_array($err_codes)) {
//                    foreach ($err_codes as $frame_num => $err_code) {
//                        $err_msgs[] = $this->generateErrorMsg($err_code,$frame_num);
//                    }
//                }else{
//                    throw Exception('$err_codes is not valid type. Must be string or array.');
//                }
//            }else{
//                throw Exception('$err_codes is empty. Please specify valid error codes');
//            }
//            return $err_msgs;
//        }

        /**
         *
         *
         * @param string $err_code -  エラーコード
         * @param int $frame_num -  エラーのあったフレーム番号
         * @return string $err_msg - エラーメッセージ
         * @throws  - Exception エラーメッセージを作り出す事が出来なかった場合。
         *
         */
        public function generateErrorMsg(string $err_code,int $frame_num = null):string{

            $err_msg =  $this->errors[$err_code]['msgs'][$this->err_msg_lang['validation']];
            if(!empty($err_msg)){
                if($frame_num !== null && strpos($err_msg,'%d')){
                    $err_msg = sprintf($err_msg, $frame_num);
                }
            }else{
                $this->errors['2001']['msgs'][$this->err_msg_lang['exception']];
                throw Exception('Cannot generate error msgs');
            }
            return $err_msg;
        }

        public function calculateScores(){
//
            $res = $this->validateResults();
            if($res !== null){
                return $this->generateErrorMsgs($res);
            }
//            $frame_num = 1;//フレーム番号を定義

            for($i=0;$i<=9;++$i){//todo 内部の処理は関数に纏める。
                $frame_num = $i + 1;
                $score_to_add = $attempt_to_check_in_final_frame = 0;

                if($frame_num === 10 ){//第10フレームのスコアは単純フレームないで倒したピンの数。
                    $score_to_add = $this->getSumOfFrame($frame_num);
                }else{//第1フレームから第10フレームのスコア計算
                    if($this->isStrike($frame_num)){//ストライクだった場合、次がダブルか否かをチェック。
                        $next_frame_num = $frame_num + 1;
                        if($next_frame_num === 10){
                            $attempt_to_check_in_final_frame = 1;
                        }
                        if($this->isStrike($frame_num,$attempt_to_check_in_final_frame)){//ダブルだった場合
                            //20ピン + 次の1投で倒したピンの数
                            $after_next_frame_num = $frame_num + $attempt_to_check_in_final_frame + 2;
                            if($next_frame_num >= 10){
                                $attempt_to_check_in_final_frame = $after_next_frame_num - 10;
                                $after_next_frame_num = 10;
                            }else{
                                $attempt_to_check_in_final_frame = 0;
                            }
                            $score_to_add = 20 + $this->getPinNum($after_next_frame_num,$attempt_to_check_in_final_frame);
                        }else{//ストライクではあるがダブルでない場合、単純に10ピン + 次のフレーム内での2投で倒したピン
                            $score_to_add = 10 + $this->getPinNum($next_frame_num, 1) + $this->getPinNum($next_frame_num,2) ;
                        }
                    }elseif($this->isSpare($frame_num)){//スペアだった場合
                        $score_to_add = 10 + $this->getPinNum($frame_num + 1,1);
                    }else{//ストライクでもスペアでもなかった場合
                        $score_to_add = $this->getSumOfFrame($frame_num);
                    }
                }

                if($frame_num > 1){
                    $score_to_add = $this->scores[$i - 1] + $score_to_add;
                }
                $this->scores[$i] = $score_to_add;
            }

        }

        private function getPinNum(){

        }










//        private function is_
        private function isStrike($frame_num,$attempt_to_check_in_final_frame = null){
            if($frame_num > 10){
                throw new Exception('invalid frame number in '.__class__."");
            }

            $res = false;
            $key = $frame_num - 1;
            if($frame_num !== 10 && ($this->result_of_all_attempts[$key] === 10)){
                $res = true;
            }elseif($frame_num === 10 && in_array($attempt_to_check_in_final_frame, [1,2,3])  ){
//              $res = $this->getStrikesInFinalFrame($result_of_attempts_in_each_frame);
            }else{
                throw new Exception('To check whether a certain attempt in final frame is strike, please specify 1 or 2 or 3 to $attempt_to_check_in_final_frame '.__class__."");
            }
            return $res;
        }

        private function getStrikesInFinalFrame(array $result_of_attempts_in_final_frame){
            $res = [];
            foreach ($result_of_attempts_in_final_frame as $result_of_attempt_in_final_frame) {
                if($result_of_attempt_in_final_frame === 10 ){
                    $res[] = true;
                }else{
                    $res[] = false;
                }
            }
            return $res;
        }

//        private function is_spare($frame_num,$nth_attempt,$result_of_all_attempts){
//            $res = false;
//            if (count($result_of_all_attempts) !== 2 &&){
//
//            }
//
//
//            if($nth_attempt === 2 && $result_of_attempt ===10){
//                $res = true;
//            }
//            return $res;
//        }

    }
