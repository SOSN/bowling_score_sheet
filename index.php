<?php
declare(strict_types=1);
require('vendor/autoload.php');

if(!empty($_POST)){
    $result_of_all_attempts = $_POST['result_of_all_attempts'];
    $bowling_score_sheet = new BowlingScoreSheet($result_of_all_attempts);
    $scores_to_display = $bowling_score_sheet->calculateScores();
    if($bowling_score_sheet->isValidResult() === false && !empty($scores_to_display)){
        $err_msgs = $scores_to_display;
        unset($scores_to_display);
    };
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Bowling Score Board</title>
    <meta name="description" content="ボーリングのスコアを計算します。This page calculates scores of bowling">
    <meta name="keywords" content="ボーリング,スコア,計算,bowling,score,calculation">
    <link rel="stylesheet" href="style.css">
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->
    <style>
        input[type ='number']{
            width: 40px;
        }
        table, td, th{
            border:2px black solid;
        }
        input[type ='submit']{
        }

        div#err_msgs_box p {
            margin: 0;
            color: red;
            font-weight: 600;
        }

        div#err_msgs_box{
            margin: 0 0 10px 0;
        }

    </style>
</head>

<body>
<header><h1>ボーリングのスコア計算</h1></header>
<article>
    <div id='scoresheet'>

        <div id="err_msgs_box">
            <?php
                if(!empty($err_msgs)){
                    foreach ($err_msgs as $err_msg) {
                        echo('<p>・'.$err_msg.'</p>');
                    }
                }
            ?>
        </div>
        <form action='#' method='POST'>
            <table id='scoresheet_table' class='scoresheet' cellpadding='1' cellspacing='0'>
                <?php
                $num_of_frames = 10;
                for ($i = 0; $i <= 2; ++$i){
                    echo('<tr>');
                    for ($j = 0; $j < $num_of_frames; ++$j){
                        switch ($i) {
                            case 0://スコアシートの1行目(フレーム番号)
                                $colspan = 6;
                                if ($j === $num_of_frames - 1){
                                    $colspan = 9;
                                }
                                echo('<th colspan=\''.$colspan.'\'>Frame ' . ($j + 1) . '</th>');
                                break;
                            case 1://スコアシートの2行目(投球結果)
                                $html = '';
                                for($k = 0; $k <= 2; ++$k){
                                    if(empty($result_of_all_attempts[$j][$k]) && $result_of_all_attempts[$j][$k] !== '0' ){
                                        $result_of_all_attempts[$j][$k] = '';
                                    }
                                    $html .= '<td colspan=\'3\'><input name=\'result_of_all_attempts['.$j.']['.$k.']\' type=\'number\' min="0" max="10" step="1" value=\''.htmlspecialchars($result_of_all_attempts[$j][$k], ENT_QUOTES, "UTF-8").'\'></td>';
                                    if ($j <= 8 && $k == 1 ){
                                        break;
                                    }
                                }
                                echo($html);
                                break;
                            case 2://スコアシートの3行目（スコア）
                                $colspan = 6;
                                if ($j === $num_of_frames - 1){
                                    $colspan = 9;
                                }
                                if(empty($scores_to_display[$j]) && !is_int($scores_to_display[$j])){
                                    $scores_to_display[$j] = '';
                                }
                                echo('<td colspan=\''.$colspan.'\'><p>'.$scores_to_display[$j].'</p></td>');
                                break;
                        }
                    }
                    echo('</tr>');
                }
                ?>

            </table>
            <input type="submit" value="計算する">
            <input type="reset" value="リセット" onclick="location.href='http://192.168.33.10';">
        </form>
    </div>
</article>
<footer></footer>
</body>
</html>