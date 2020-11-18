<?php
namespace App\Domain;

use App\Model\Guide as Model_Guide;

class Guide {
	public function getGuide() {
		
		$rs = array();

		$model = new Model_Guide();
		$rs = $model->getGuide();

		return $rs;
	}
}
