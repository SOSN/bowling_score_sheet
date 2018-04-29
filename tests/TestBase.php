<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
class TestBase extends TestCase
{

    /**
     * アクセスしたいpublic,またはprivateプロパティのReflectionPropertyのインスタンスを返す。
     * テスト対象となるクラスのpublic,またはprivateプロパティにアクセスしたい時に使う。
     *
     * @param string $name_of_class - アクセスしたいpublic、またはprivateプロパティを持っているクラス
     * @param string $property_name - アクセスしたいpublic、またはprivateプロパティ名
     * @return object $property - アクセスしたいpublic、またはprivateプロパティのReflectionPropertyのインスタンス
     */
    protected static function getInsOfProperty($name_of_class, $property_name)
    {
        $class = new \ReflectionClass($name_of_class);
        $property = $class->getProperty($property_name);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * アクセスしたいpublic,またはprivateメソッドのReflectionMethodのインスタンスを返す。
     * テスト対象となるクラスのpublic,またはprivateメソッドにアクセスしたい時に使う。
     *
     * @param string $name_of_class - アクセスしたいpublic、またはprivateメソッドを持っているクラス
     * @param string $method_name - アクセスしたいpublic、またはprivateメソッド名
     * @return object $property - アクセスしたいpublic、またはprivateメソッドのReflectionMethodのインスタンス
     */
    protected static function getInsOfMethod($name_of_class, $method_name)
    {

        $class = new \ReflectionClass($name_of_class);
        $method = $class->getMethod($method_name);
        $method->setAccessible(true);

        return $method;
    }


    /**
     * タイプヒンティング例外処理時のエラーメッセージが妥当なものか検証する。
     *
     * @param TypeError object $e
     * @param mixed $error_arg - エラーのあった引数の値
     * @param int $error_arg_key - エラーがあった引数のキー。例えば一番目の引数ならば0となる。
     * @param string $method_name - エラーメッセージに表示されるべきメソッド名。
     * @param string $expected_type - 引数に期待される型
     * @param string $class_name - エラーメッセージに表示されるべきオブジェクトに期待されるクラス名。オブジェクトのエラーメッセージの妥当性を確認する際、必要となる。
     * @throws \Exception - クラス付きオブジェクトに対するエラーメッセージを表示するときに、$class_nameが無かった場合、$expected_typeが'array'か'object'で無かった場合に例外処理
     */
    function assertValidTypeErrorMsg(
        TypeError $e,
        $error_arg,
        int $error_arg_key,
        string $method_name,
        string $expected_type,
        string $class_name = ''
    ) {

        $paramType = gettype($error_arg);
        if ($expected_type == 'object') {
            if (empty($class_name)) {
                throw new Exception('To assert validness of error message which is displayed when a class of object is regarded as wrong by typehinting, you need to enter the classname into the argument of $class_name in ' . __METHOD__ . '.');
            }

            if (is_object($error_arg)) {
                $class_name_of_err_ins = get_class($error_arg);
                $givenValue = 'instance of ' . $class_name_of_err_ins;
            } else {
                $givenValue = $paramType;
            }

            $expectedPartialMsg = 'Argument ' . ($error_arg_key + 1) . ' passed to ' . $method_name . '() must be an instance of ' . $class_name . ', ' . $givenValue . ' given';
        } elseif(in_array($expected_type,['boolean','float','integer','string'])) {
            $expectedPartialMsg = 'Argument ' . ($error_arg_key + 1) . ' passed to ' . $method_name . '() must be of the type ' . $expected_type . ', ' . $paramType . ' given';
        }else{
            throw new Exception('$expectedType cannot be handled in ' . __METHOD__ . '.');
        }
        echo('<pre>');
        var_dump($e->getMessage());
        var_dump($expectedPartialMsg);
        echo('</pre>');
        exit;
        $this->assertSame($e->getMessage(),$expectedPartialMsg);
    }




}


