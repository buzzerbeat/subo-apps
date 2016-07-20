<?php


namespace common\components;
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/5
 * Time: 13:01
 */
class Utility extends \yii\base\Component
{

    /**
     * bbboo_jobbr_code
     * 包卜通用码
     * $Author: jobbr $
     * $Date: 2009-03-17 $
     */
    public static function id($sid)
    {
        $offset_array = self::offset_array(2);
        if (strlen($sid) != 11) {
            return 0;
        }
        $edition = substr($sid, 0, 1);
        if (!isset($offset_array[$edition])) {
            return 0;
        }
        $offset = $offset_array[$edition];
        //处理偏移
        $start_code = substr($sid, $offset + 1);
        $end_code = substr($sid, 1, $offset);
        $real_code = $start_code . $end_code;

        //将字符转换成64进制数组,第一位是个位
        $numArray = self::code_to_number($real_code);

        //处理混淆
        $numArray = self::obscure($numArray, -$offset, -1);

        //转换成2进制
        $ret = 0;
        foreach ($numArray as $key => $num) {
            $ret += $num * pow(64, $key);
        }
        return $ret;
    }

    public static function sid($id)
    {
        //取得偏移值和便宜code
        $offset_array = self::offset_array(1);
        $offset = $id % 11;
        $offset_code = $offset_array[$offset];

        //转化何曾64进制数组，第一位是个位
        $numArray = self::number_to_64($id);

        //处理混淆
        $numArray = self::obscure($numArray, $offset, 1);

        //处理转化成字符
        $string = join('', array_reverse(self::number_to_code($numArray)));

        //处理偏移
        $start_code = substr($string, -$offset);
        $end_code = substr($string, 0, -$offset);
        $code = $start_code . $end_code;
        $real_code = $offset_code;
        return $real_code . $code;
    }


    //将数字转换成字符
    private static function number_to_code($numberArr)
    {
        $char_array = self::original_array(1);  //取得数字代表的字符

        $ret = array();
        foreach ($numberArr as $number) {
            $ret[] = $char_array[$number];
        }
        return $ret;
    }


    //字符转换成数字
    private static function code_to_number($code)
    {
        //取得字符代表的数字
        $num_array = self::original_array(2);
        $length = strlen($code);
        $numbers = array();
        for ($i = $length - 1; $i >= 0; $i--) {
            $numbers[] = $num_array[$code{$i}];
        }
        return $numbers;
    }

    //混淆数字
    private static function obscure($numArray, $obscureNum, $z)
    {
        foreach ($numArray as $k => &$num) {
            $num = $num + $k * 3 * $obscureNum + $z * $k * 4;
            $num = $num % 64;
            if ($num < 0) {
                $num += 64;
            }
        }
        return $numArray;
    }

    //将十进制数字转换为六十四进制
    //如果传入为数组，则遍历所有元素返回数组
    private static function number_to_64($number)
    {
        $ret = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        //循环处理数字id，得到64进制字符串
        $i = 0;
        do {
            $remainder = $number % 64;
            $number -= $remainder;
            $number = $number / 64;
            $ret[$i] = $remainder;
            $i++;
        } while ($number >= 1);
        unset($i);
        return $ret;
    }


    //六十四进制 to 十进制
    private static function code_to_64($code)
    {
        //取得字符代表的数字
        $num_array = self::original_array(2);
        $length = strlen($code);
        $ret = array();
        //倒序，计算方便 $j 为当前字符倒序位数
        $ret = array();
        for ($i = $length - 1; $i >= 0; $i--) {
            $ret[] = $num_array[$code{$i}];
        }
        return $ret;
    }

    /********************
     * $type为1返回10进制=>64进制
     * 2返回64进制=>10进制
     ********************/
    private static function original_array($type)
    {
        $original_array = array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => 'a', 11 => 'b', 12 => 'c', 13 => 'd', 14 => 'e', 15 => 'f', 16 => 'g'
        , 17 => 'h', 18 => 'i', 19 => 'j', 20 => 'k', 21 => 'l', 22 => 'm', 23 => 'n', 24 => 'o', 25 => 'p', 26 => 'q', 27 => 'r', 28 => 's', 29 => 't', 30 => 'u', 31 => 'v', 32 => 'w', 33 => 'x', 34 => 'y', 35 => 'z',
            36 => 'A', 37 => 'B', 38 => 'C', 39 => 'D', 40 => 'E', 41 => 'F', 42 => 'G', 43 => 'H', 44 => 'I', 45 => 'J', 46 => 'K', 47 => 'L', 48 => 'M', 49 => 'N', 50 => 'O', 51 => 'P', 52 => 'Q', 53 => 'R', 54 => 'S',
            55 => 'T', 56 => 'U', 57 => 'V', 58 => 'W', 59 => 'X', 60 => 'Y', 61 => 'Z', 62 => '-', 63 => '_');
        if ($type == 1) {
            return $original_array;
        } else {
            return array_flip($original_array);
        }
    }

    private static function get_code_num($code, $offset)
    {
        $codeToNum = self::original_array(2);
        $num = $codeToNum[$code];
        if ($num >= 0) {
        }
    }

    /****************
     * 返回偏移量数组
     * type为1返回数字to字符
     * 2返回字符to数字
     ****************/
    private static function offset_array($type)
    {
        $original_array = array(0 => 'a', 1 => 'f', 2 => 'k', 3 => 'p', 4 => 'u', 5 => 'z', 6 => 'E', 7 => 'J', 8 => 'O', 9 => 'T', 10 => 'Y');
        if ($type == 1) {
            return $original_array;
        } else {
            return array_flip($original_array);
        }
    }


    /* 从某天时间返回当天起始时间和结束时间 */
    /* 支持两种格式，Y-m-d和unix时间戳 */
    function time_to_day_0_24($time){
        $dates = explode('-', $time);
        if (count($dates) != 3){
            $date = date('Y-m-d', $time);
            $dates = explode('-', $date);
        }
        $time_start = mktime(0, 0, 0, $dates[1], $dates[2], $dates[0]);
        $time_end = $time_start + 86400;

        return array($time_start, $time_end);
    }
    /* 取得距离现在的时间 */
    public static function time_get_past($time){
        $time_past = time() - $time;
        if ($time_past > 31536000){
            $time_past = floor($time_past/31536000) . '年前';
        }
        elseif($time_past > 2592000){
            $time_past = floor($time_past/2592000) . '个月前';
        }
        elseif($time_past > 86400){
            $time_past = floor($time_past/86400) . '天前';
        }
        elseif($time_past > 3600){
            $time_past = floor($time_past/3600) . '小时前';
        }
        elseif($time_past > 60){
            $time_past = floor($time_past/60) . '分钟前';
        }
        else{
            $time_past = $time_past . '秒前';
        }
        return $time_past;
    }
    //将月份字符串返回数字
    function time_get_month_from_english($value){
        $value = trim(strtolower($value));
        switch ($value){
            case 'january':
                return 1;
            case 'jan':
                return 1;
            case 'february':
                return 2;
            case 'feb':
                return 2;
            case 'march':
                return 3;
            case 'mar':
                return 3;
            case 'april':
                return 4;
            case 'apr':
                return 4;
            case 'may':
                return 5;
            case 'june':
                return 6;
            case 'jun':
                return 6;
            case 'july':
                return 7;
            case 'jul':
                return 7;
            case 'august':
                return 8;
            case 'aug':
                return 8;
            case 'september':
                return 9;
            case 'sep':
                return 9;
            case 'october':
                return 10;
            case 'oct':
                return 10;
            case 'november':
                return 11;
            case 'nov':
                return 11;
            case 'dec':
                return 12;
            case 'december':
                return 12;
        }
    }
    //年月日标准化函数，显示周几
    function getWeekdayZhou($time = false){
        if ($time === false)
            $time = time();
        switch (date("D", $time)){
            case 'Mon':
                return '周一';
                break;
            case 'Tue':
                return '周二';
                break;
            case 'Wed':
                return '周三';
                break;
            case 'Thu':
                return '周四';
                break;
            case 'Fri':
                return '周五';
                break;
            case 'Sat':
                return '周六';
                break;
            case 'Sun':
                return '周日';
                break;
            default:
                echo '';
        }
    }
    //年月日标准化函数，显示星期几
    function getWeekday($time = false){
        if ($time === false)
            $time = time();
        switch (date("D", $time)){
            case 'Mon':
                return '星期一';
                break;
            case 'Tue':
                return '星期二';
                break;
            case 'Wed':
                return '星期三';
                break;
            case 'Thu':
                return '星期四';
                break;
            case 'Fri':
                return '星期五';
                break;
            case 'Sat':
                return '星期六';
                break;
            case 'Sun':
                return '星期日';
                break;
            default:
                echo '';
        }
    }
    //将标准时间转化为时间戳
    function time_to_unix($time){
        if (strlen($time) != 19 || !preg_match('/[\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2}/', $time)){
            die(__FUNCTION__ . '：错误的参数' . $time);
        }
        $year = substr($time, 0, 4);
        $month = substr($time, 5, 2);
        $date = substr($time, 8, 2);
        $hour = substr($time, 11, 2);
        $minute = substr($time, 14, 2);
        $second = substr($time, 17, 2);
        return mktime($hour, $minute, $second, $month, $date, $year);
    }
    //年月日标准化函数，显示星期几
    function time_get_date($type, $time = false){
        if ($time === false)
            $time = time();
        $times = explode('-', $time);
        if (count($times) == 3){
            $time = mktime('1', '1', '1', $times[1], $times[2], $times[0]);
        }
        switch ($type){
            case '1':
                return date("Y年n月j日", $time);
            case '2':
                return time_get_weekday($time);
            case '3':
                return date("n月j日", $time);
            case '4':
                return $time;
        }
    }

    /*
    * 获取指定长度的随机字符串
    */
    public static function getRandString($length){
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $result = '';
        $l = strlen($str);
        for($i = 0;$i < $length;$i ++){
            $num = rand(0, $l-1);
            $result .= $str[$num];
        }
        return $result;
    }

    public static function getRandNumber($length){
        $str = '1234567890';
        $result = '';
        $l = strlen($str);
        for($i = 0;$i < $length;$i ++){
            $num = rand(0, $l-1);
            $result .= $str[$num];
        }
        return $result;
    }


    public static function fileExt($mimeType)
    {
        $map = array(
            'application/pdf'   => '.pdf',
            'application/zip'   => '.zip',
            'image/gif'         => '.gif',
            'image/jpeg'        => '.jpg',
            'image/png'         => '.png',
            'text/css'          => '.css',
            'text/html'         => '.html',
            'text/javascript'   => '.js',
            'text/plain'        => '.txt',
            'text/xml'          => '.xml',
        );
        if (isset($map[$mimeType]))
        {
            return $map[$mimeType];
        }

        // HACKISH CATCH ALL (WHICH IN MY CASE IS
        // PREFERRED OVER THROWING AN EXCEPTION)
        $pieces = explode('/', $mimeType);
        return '.' . array_pop($pieces);
    }

    public static function command_exist($cmd) {
        $returnVal = shell_exec("which $cmd");
        return (empty($returnVal) ? false : true);
    }

}