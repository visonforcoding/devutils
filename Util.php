<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-5-22 17:19:18 by 曹文鹏 , caowenpeng1990@126.com
 */

namespace visonforcoding;

class Util {

    public function getMenu($list, $pid_key = 'pid', $id_key = 'id') {
        foreach ($list as $key => $value) {
            foreach ($list as $k => $v) {
                if ($value[$id_key] == $v[$pid_key]) {
                    $list[$key]['child'][] = $v;
                    unset($list[$k]);
                }
            }
        }
        $list = array_values($list);
        return $list;
    }

    /**
     * 无限分类的简单格式化
     * @param type $list
     * @param type $pid
     * @param type $key_val
     * @param type $pid_val
     * @param type $level
     * @param type $html
     * @return type
     */
    public function tree($list, $pid = 0, $key_val = 'id', $pid_val = 'pid', $level = 0, $html = '--') {
        $tree = array();
        foreach ($list as $v) {
            if ($v[$pid_val] == $pid) {
                $v['sort'] = $level;
                $v['html'] = str_repeat($html, $level);
                $tree[] = $v;
                $tree = array_merge($tree, self::tree($list, $v[$key_val], $key_val, $pid_val, $level + 1, $html));
            }
        }
        return $tree;
    }

    /**
     *  递归显示目录文件 
     * @param string $source_dir   目录名
     * @param type $directory_depth  深度
     * @param type $hidden  是否显示隐藏文件
     * @return boolean
     */
    public function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE) {
        if ($fp = @opendir($source_dir)) {
            $filedata = array();
            $new_depth = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if (!trim($file, '.') OR ( $hidden == FALSE && $file[0] == '.')) {
                    continue;
                }

                if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir . $file)) {
                    $filedata[$file] = $this->directory_map($source_dir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden);
                } else {
                    $filedata[] = $file;
                }
            }

            closedir($fp);
            return $filedata;
        }

        return FALSE;
    }

    /**
     *  递归显示目录文件 
     * @param string $source_dir   目录名
     * @param type $directory_depth  深度
     * @param type $hidden  是否显示隐藏文件
     * @return boolean
     */
    public function directory_tree($source_dir) {
        $realPath = APP_PATH.'public/'.$source_dir;
        if ($fp = @opendir($realPath)) {
            $filedata = array();
//            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if (!trim($file, '.') OR ( $file[0] == '.')) {
                    continue;
                }
                if (is_dir($realPath .'/'. $file)) {
                    $filedata[] = array(
                        'isDir' => true,
                        'name' => $file,
                        'path' => $source_dir .'/'. $file
                    );
                } else {
                    $filedata[] = array(
                        'isDir' => false,
                        'name' => $file,
                         'path'=>$source_dir.'/'.$file   
                    );
                }
            }
            closedir($fp);
            return $filedata;
        }
        return FALSE;
    }

}
