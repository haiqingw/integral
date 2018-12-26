<?php

namespace Admin\Model;

use Think\Model;

/**
 * Description of UserModel
 *
 * @author HaiQing.Wu
 */
class UserModel extends Model {

	public function _list($where, $refields = "") {
		if (empty($where)) {
			$where = "";
		}
		return $this->field($refields)->where($where)->select();
	}

}
