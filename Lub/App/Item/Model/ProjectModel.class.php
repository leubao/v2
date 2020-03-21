<?php

/**
 * 项目
 * @Author: IT Work
 * @Date:   2020-01-18 13:16:30
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-01-19 12:49:37
 */
namespace Item\Model;

use Common\Model\Model;
class ProjectModel extends Model{

	protected $_auto = array(
        array('create_time', 'time', 1, 'function')
    );
    
}