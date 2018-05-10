<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
class TestBase extends TestCase
{

    /**
     * アクセスしたいプロパティのReflectionPropertyのインスタンスを返す。
     * 主にテスト対象となるクラスのprivateプロパティにアクセスしたい時に使う。
     *
     * @param string $class_name - アクセスしたいプロパティを持っているクラス名
     * @param string $property_name - アクセスしたいプロパティ名
     * @return object $property - アクセスしたいプロパティのReflectionPropertyのインスタンス
     */
    protected static function getInsOfProperty(string $class_name,string $property_name)
    {
        $class = new \ReflectionClass($class_name);
        $property = $class->getProperty($property_name);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * 取得したいプロパティを返す。
     * テスト対象となるクラスのprivateプロパティに取得したい時に使う。
     *
     * @param string $class_name - 取得したいプロパティを持っているクラス名
     * @param string $property_name - 取得したいプロパティ名
     * @param object $ins - 取得したいプロパティを持っているクラスのインスタンス
     * @return mixed - 指定したクラスのプロパティ
     */
    protected static function getProperty(string $class_name, string $property_name, $ins)
    {
        return self::getInsOfProperty($class_name, $property_name)->getValue($ins);
    }


    /**
     * 複数の取得したいプロパティを返す
     * @param string $class_name - 取得したいプロパティを持っているクラス名
     * @param array $property_names - 取得したいプロパティ名の配列
     * @param array $constructor_args  - クラスのコンストラクタの引数
     * @return array $properties - 指定したクラスのプロパティ
     *
     */
    public function getProperties(string $class_name, array $property_names ,array $constructor_args = null):array{

        $ins = new $class_name(...$constructor_args);
        $properties = [];
        foreach ($property_names as $property_name) {
            $properties[$property_name] = $this->getProperty($class_name,$property_name,$ins);
        }
        return $properties;
    }

    /**
     * アクセスしたいメソッドのReflectionMethodのインスタンスを返す。
     * テスト対象となるクラスのprivateメソッドにアクセスしたい時に使う。
     *
     * @param string $class_name - アクセスしたいメソッドを持っているクラス
     * @param string $method_name - アクセスしたいメソッド名
     * @return object $property - アクセスしたいメソッドのReflectionMethodのインスタンス
     */
    protected static function getInsOfMethod(string $class_name, string $method_name)
    {

        $class = new \ReflectionClass($class_name);
        $method = $class->getMethod($method_name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * ReflectionMethodを通してメソッドを実行する。
     * 主にテスト対象となるprivateメソッドを実行したい時に使う。
     *
     * @param string $class_name - 実行したいメソッドを持っているクラス
     * @param string $method_name -　実行したいメソッド名
     * @param object $ins - 実行したいメソッドを持っているクラスのインスタンス
     * @param array $args - 実行したいメソッドの引数
     * @return mixed - 指定したメソッドの返り値
     */
    protected static function executeMethod(string $class_name, string $method_name, $ins, array $args = [])
    {

        return self::getInsOfMethod($class_name, $method_name)->invokeArgs($ins,$args);
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

        $this->assertSame($e->getMessage(),$expectedPartialMsg);
    }




}


