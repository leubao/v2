<?php

// +----------------------------------------------------------------------
// | LubTMP Components
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------

namespace Libs\System;

class Components {

    static private $_components = array(/*
        'Cloud' => array(
            'class' => '\\Libs\\System\\Cloud',
            'path' => 'Libs.System.Cloud',
        ),
        'CloudDownload' => array(
            'class' => '\\Libs\\System\\CloudDownload',
            'path' => 'Libs.System.CloudDownload',
        ),*/
        'UploadFile' => array(
            'class' => '\\UploadFile',
        ),
        'Dir' => array(
            'class' => '\\Dir',
            'path' => 'Libs.Util.Dir',
        )
    );

    public function __construct($_components = array()) {
        if (!empty($_components)) {
            $this->setComponents($_components);
        } else {
            $this->setComponents(C('components', NULL, array()));
        }
    }

    public function __get($name) {
        if (isset(self::$_components[$name])) {
            $components = self::$_components[$name];
            if (!empty($components['class'])) {
                $class = $components['class'];
                if ($components['path'] && !class_exists($class, false)) {
                    import($components['path'], PROJECT_PATH);
                }
                unset($components['class'], $components['path']);
                $this->$name = \Think\Think::instance($class);
                return $this->$name;
            }
        }
    }

    /**
     * 连接
     * @access public
     * @param array $_components  配置数组
     * @return void
     */
    static public function getInstance($_components = array()) {
        static $systemHandier;
        if (empty($systemHandier)) {
            $systemHandier = new Components($_components);
        }
        return $systemHandier;
    }

    /**
     * 设置$_components
     * @param type $_components
     */
    public function setComponents($_components = array()) {
        self::$_components = array_merge(self::$_components, $_components);
    }

}