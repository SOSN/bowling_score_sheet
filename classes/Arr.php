<?php
declare(strict_types=1);
Class Arr {

    /**
     * 配列から指定した要素を消し、数値添字を振り直した配列を返す。
     *
     * @param array $arr - 消したい要素を含む配列
     * @param array or string $keys - 消したい要素群のキー群の配列、またはキーの文字列。
     * @return array - 指定した要素を消し、数値添字を振り直した配列
     */
    static function unsetElmsAndArrVals(array $arr, $keys):array{
        $arr = self::unsetElms($arr, $keys);
        return array_values($arr);
    }

    /**
     * 配列から指定した要素を消す、
     *
     * @param array $arr - 消したい要素を含む配列
     * @param array or string $keys - 消したい要素群のキー群の配列、またはキーの文字列。
     * @return - 指定した要素を消した配列
     */
    static function unsetElms(array $arr, $keys):array{
        if(is_array($keys)){
            foreach ( $keys as $key) {
                unset($arr[$key]);
            }
        }else{
            unset($arr[$keys]);
        }
        return $arr;
    }
}
