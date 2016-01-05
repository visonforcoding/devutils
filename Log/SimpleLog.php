<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-10-25 17:59:20 by allen <blog.rc5j.cn> , caowenpeng1990@126.com
 */

namespace Utils\Log;

class SimpleLog {

    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    const ERROR = 'ERROR';

    /**
     * 简单的日志记录
     * @param type $message
     * @param type $flag
     * @param string $log_path
     * @throws \Utils\Log\Exception
     */
    public static function log($message, $flag = self::INFO, $log_path = '') {
        error_reporting(0);
        if (empty($log_path)) {
            $log_path = dirname(__FILE__) . '/log/';
        }
        $log_name = $log_path . date('Y_m_d') . '.log';
        if (!file_exists($log_name)) {
            fopen($log_name, 'w+');
        }
        $pre_str = '[' . date('Y-m-d H:i:s') . '][' . $flag . ']';
        $message = str_replace(PHP_EOL, null, $message);
        file_put_contents($log_name, $pre_str . $message . "\r\n", FILE_APPEND);
        fclose($log_name);
    }

    public static function praseLog($log) {
        $log = explode(PHP_EOL, $log);
        $last_index = intval(count($log) - 1);
        unset($log[$last_index]);
        $log_arr = [];
        foreach ($log as $key => $value) {
            $patten_time = "/\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})]\[(DEBUG|INFO|ERROR)](.*)/";
            preg_match($patten_time, $value, $matches);
            $time = $matches[1];
            $info = $matches[2];
            $content = $matches[3];
            $log_arr[] = array(
                'time' => $time,
                'info' => $info,
                'content' => $content
            );
        }
        krsort($log_arr);
        return $log_arr;
    }

}
