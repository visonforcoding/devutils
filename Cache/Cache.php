<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-6-5 15:29:34 by 曹文鹏 , caowenpeng1990@126.com
 */

namespace Visonforcoding\Lib\Cache;

class Cache {

    public function fileCache($name, $value = '', $path = DATA_PATH) {
        error_reporting(0);
        $filename = $path . $name ;
        if ('' !== $value) {
            if (is_null($value)) {
                // 删除缓存
                if(file_exists($filename)){
                    unlink($filename);
                }
            } else {
                //开始缓存数据
                $cache_file = fopen($filename, 'wb+');
                fwrite($cache_file, serialize($value));
                fclose($cache_file);
                return true;
            }
        } else {
            if (file_exists($filename)) {
                $cache_file = fopen($filename, 'rb');
                $content = fread($cache_file, filesize($filename));
                fclose($cache_file);
                return unserialize($content);
            } else {
                return false;
            }
        }
    }

}
