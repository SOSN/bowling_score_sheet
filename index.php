<?php
declare(strict_types=1);
require('vendor/autoload.php');
//use BowlingScoreSheet;
//var_dump($_POST);

if(!empty($_POST)){

//    require_once('BowlingScoreSheet.php');
//    echo('<pre>');
//    var_dump($_POST);
//    echo('</pre>');
//    exit;
    $bowling_score_sheet = new BowlingScoreSheet($_POST['result_of_all_attempts']);
    echo('<pre>');
    var_dump('ok');
//    var_dump($bowling_score_sheet->getSumOfFrame(1));
    echo('</pre>');
    exit;
//    $scores = $bowling_score_sheet->calculateScores();
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

    </style>
</head>

<body>
<header><h1>ボーリングのスコア計算</h1></header>
<article>
    <div id='scoresheet'>
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
                                    $html .= '<td colspan=\'3\'><input name=\'result_of_all_attempts['.$j.']['.$k.']\' type=\'number\' value=\'\'></td>';
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
                                echo('<td colspan=\''.$colspan.'\'><p></p></td>');
                                break;
                        }
                    }
                    echo('</tr>');
                }
                ?>

            </table>
            <input type="submit" value="計算する">
        </form>
    </div>
</article>
<footer></footer>
</body>
</html>