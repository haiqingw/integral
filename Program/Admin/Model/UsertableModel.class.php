<?php

namespace Admin\Model;

use Think\Model;

/**
 * Description of UsertableModel
 *
 * @author HaiQing.Wu
 */
class UsertableModel extends Model {

	/**
	 * 列表
	 */
	public function getList($where) {

		if (empty($where)) {
			return false;
		} else {
			return $this->where($where)->select();
		}
	}

}
