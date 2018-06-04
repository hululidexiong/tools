<?php
/**
 * Created by PhpStorm.
 * User: mhx
 * Date: 2017/9/27
 * Time: 16:43
 */

namespace BBear\Tools\Base;


class Tools
{

    /**
     * mhx
     * 说明：重写 empty ， 因为 empty 当值等于 0 时 返回 false
     * @param $value
     * @return bool
     */
    public static function isEmpty($value)
    {
        return $value === null || $value === [] || $value === '';
    }



    /**
     * mhx
     * 说明：格式化价格(分转元) 存储之前使用
     * @param int $price
     * @return int
     */
    public static function beforePrice($price = 0){
        return $price * 100;
    }

    /**
     * mhx
     * 说明：格式化价格(元转分) 读取之后使用
     * 注：sprintf  末位会因精度导致四舍五入
     * @param int $price
     * @return float
     */
    public static function lastPrice($price = 0){
        return floatval(self::float2($price/100));
    }

    /**
     * mhx
     * 说明：保留两位小数
     * 注：sprintf  末位会因精度导致四舍五入
     * @param int $n
     * @return string
     */
    public static function float2($n = 0){
        return sprintf('%1.2f',$n);
    }

    /**
     * mhx
     * 说明：
     * @param int $page
     * @param int $count
     * @param int $pagesize
     * @return mixed
     * @throws \Exception
     */
    public static function page( int $page , int $count , int $pagesize = 10) :array
    {

//        if(empty($count)){
//            throw new \Exception('Tools page throw : count must is integer and greater than 0!');
//        }
        if( empty($page) || !is_numeric($page) ){
            $page = 1;
        }

        if( empty($pagesize) || !is_numeric($pagesize)){
            $pagesize = 10;
        }

        $maxpage = ceil($count / $pagesize);//获取最大页数

        $limit = [($page - 1) * $pagesize,$pagesize];

        return [
            'page' => $page,
            'pagesize' => $pagesize,
            'count' => $count,
            'maxpage' => $maxpage,
            'limit' => $limit
        ];
    }



    /**
     * mhx
     * 说明：
     * @param int $page
     * @param array $C 必须是索引数组 ， 返回的数组 同$C 循序相同
     * @param int $pagesize
     * @return mixed
     */
    public static function multiPage(int $page ,array $C , int $pagesize = 10 ):array
    {
        if(!self::is_indexArray($C)){
            throw new \Exception('Tools multiPage throw : $C must is indexed array!');
        }
        //区间数组
        $scopeC = array();
        //区间指针记录
        $scopePointInteger = 0;
        //记录在数轴上个区间位置
        foreach($C as $k => $v){
            $scopeC[$k] = array($scopePointInteger ,$scopePointInteger + $v);
            $scopePointInteger += intval($v);
        }

//        if($scopePointInteger <= 0 ){
//            throw new \Exception('Tools multiPage throw : sum value of array $C must be greater than 0!');
//        }

        $count = $scopePointInteger;
        //print_r($scopeC);
        //最大页数
        $maxPage = ceil($count / $pagesize);
        //实际分页数据区间
        $OriginScopeL = ($page - 1) * $pagesize;
        $OriginScopeR = $OriginScopeL + $pagesize;

        //初始化limit 数组 , 使其下标同 $C一致
        $ScopeLimit = array();
        for($i=0;$i < count($C);$i++){
            $ScopeLimit[$i] = false;
        }
        foreach ($scopeC as $key => $value) {
            if($value[1] <= $OriginScopeL){
                //不取 0 ~ 起始点的数据
                //$ScopeLimit[$key] = false;
            }else{
                //起始点以后的数据
                //获取起始点位置 , 起始点大于阶段的起始点的时候需要借位 ， 反之等于当前区间的第一位 针对limit 等于 0 , ?
                $start = $value[0] > $OriginScopeL ? 0 : $OriginScopeL - $value[0];
                //该区间的结束点 针对Limit
                $maxEnd = $C[$key] - $start;
                //获取结束点 位置 , 看结束点是否大于区间结束点
                if($OriginScopeR > $value[1]){
                    $end = $maxEnd;
                    $ScopeLimit[$key] = ' limit '.$start.','.$end;
                }else{
                    //反之 等于本区间结束点
                    //取差值
                    $Borrow = $value[1] - $OriginScopeR;
                    //取剩余数
                    $end = $maxEnd - $Borrow;
                    $ScopeLimit[$key] = ' limit '.$start.','.$end;
                    //echo $ScopeLimit[$key];
                    break;
                }
            }
        }

        return [
            'page' => $page,
            'pagesize' => $pagesize,
            'count' => $count,
            'maxpage' => $maxPage,
            'limit' => $ScopeLimit
        ];

    }

    /**
     * mhx
     * 说明：按计时方式显示时间
     * @param int $second
     * @return array
     */
    public static function secondToWords(int $second){
        $day = $minute = $hour = $sec = 0;
        $str = '';
        $sec = $second % 60;
        $minute = intval($second / 60) % 60;
        $hour = intval($second / (60 * 60)) % 24;
        $day = intval($hour / 24);
        if($hour){
            $str .= ($hour . '时');
        }
        if(strlen($minute) == 1){
            $minute = '0' . $minute;
        }
        if(strlen($sec) == 1){
            $sec = '0' . $sec;
        }
        $str .=  ($minute . '分' . $sec . '秒');
        return [
            'str' => $str,
            'day' => $day,
            'hour' => $hour,
            'minute' => $minute,
            'sec' => $sec
        ];
    }

    /**
     * mhx
     * 说明： 验证是否是索引数组
     * @param $array
     * @return bool
     */
    public static function is_indexArray($array){
        if(is_array($array)) {
            $keys = array_keys($array);
            return $keys === array_keys($keys);
        }
        return false;
    }



    /**
     * mhx
     * 说明：symmetry 简单对称加密算法之加密
     * @param String $string 需要加密的字串
     * @param String $skey 加密EKY  需要和解码时对应
     * @param array $mix 混淆字符也可以用来解决urlEncode中对'+'和' '的编码问题 需要和解码时对应
     * @return String
     */
    public static function sEncode($string = '', $skey = 'wenzi' ,$mix = array('#b1'=>'+' , '#b2'=>'/' , '@A' => ' ')) {

        $string = base64_encode($string);
        $strlen = strlen($string);
        $skLen = strlen($skey);
        $str = '';
        for($i=0; $i < $strlen ; $i++){
            $sI =  $i % $skLen;
            $str .= $string[$i] ^ $skey[$sI];
        }
        if($mix){
            $arrayKey = array_keys($mix);
            $arrayValue = array_values($mix);
            $str = str_replace($arrayValue, $arrayKey, $str);
        }

        return base64_encode($str);
    }

    /**
     * mhx
     * 说明：symmetry 简单对称加密算法之解密
     * @param string $string 需要解密的字串
     * @param string $skey $skey 解密KEY
     * @param array $mix
     * @return string
     */
    public static function sDecode($string = '', $skey = 'wenzi' , $mix = array('#b1'=>'+' , '#b2'=>'/' , '@A' => ' ')) {
        $string = base64_decode( $string , TRUE  );
        if($mix){
            $arrayKey = array_keys($mix);
            $arrayValue = array_values($mix);
            $string = str_replace($arrayKey,$arrayValue , $string);
        }
        $strlen = strlen($string);
        $skLen = strlen($skey);
        $str = '';
        for($i=0; $i < $strlen ; $i++){
            $sI =  $i % $skLen;
            $str .= $string[$i] ^ $skey[$sI];
        }

        return base64_decode($str , true);
    }

    public static function UrlParse($url,$value,$method = 'add_param'){

        $rUrl = '';
        $parse = parse_url($url);
        if($parse){
            switch($method){
                case 'add_param':
                    if($parse['query']){
                        $rUrl = $url . '&' . $value;
                    }else{
                        $rUrl = $url . '?' . $value;
                    }
                    break;
            }
        }
        return $rUrl;
    }

    public static function ip(){
        $reIP = $_SERVER["REMOTE_ADDR"];
        return $reIP;
    }

}