<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class lib_crypt3Des{

    public static $key = 'GiMHXuOiPRykvOWAx9yzAjeil7PjW+bl'; // 测试环境KEY
    
    public static $iv =  '00000000';

    //加密
    public static function encrypt($input){

        $size = mcrypt_get_block_size('tripledes', 'ecb');

        $input = self::pkcs5_pad($input, $size); //pkcs5填充方式

        $key = base64_decode(self::$key);

        $td = mcrypt_module_open( MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        
        //使用MCRYPT_3DES算法,ecb模式
        mcrypt_generic_init($td, $key, self::$iv);

        //初始处理
        $data = mcrypt_generic($td, $input);

        //加密
        mcrypt_generic_deinit($td);

        //结束
        mcrypt_module_close($td);

        $data = self::removeBR(base64_encode($data));

        return $data;
    }

    //解密
    public static function decrypt($encrypted){

        $encrypted = base64_decode($encrypted);

        $key = base64_decode(self::$key);

        $td = mcrypt_module_open( MCRYPT_3DES,'',MCRYPT_MODE_ECB,'');

        //使用MCRYPT_3DES算法,ecb模式
        mcrypt_generic_init($td, $key, self::$iv);

        //初始处理
        $decrypted = mdecrypt_generic($td, $encrypted);

        //解密
        mcrypt_generic_deinit($td);

        //结束
        mcrypt_module_close($td);

        $decrypted = self::pkcs5_unpad($decrypted); //pkcs5填充方式

        return $decrypted;
    }

    //删除回车和换行
    public static function removeBR( $str ){

        $len = strlen( $str );

        $newstr = "";

        $str = str_split($str);

        for ($i = 0; $i < $len; $i++ ){

            if ($str[$i] != '\n' and $str[$i] != '\r'){

                $newstr .= $str[$i];
            }
        }

        return $newstr;
    }

    public static function pkcs5_pad($text, $blocksize){

        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }

    public static function pkcs5_unpad($text){

        $pad = ord($text{strlen($text)-1});

        if ($pad > strlen($text)) return false;

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;

        return substr($text, 0, -1 * $pad);
    }

}
