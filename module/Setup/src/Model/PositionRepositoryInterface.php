<?php

namespace Setup\Model;

interface PositionRepositoryInterface{
	public function addPosition(Position $position);
	public function editPosition(Position $position,$id);
	public function deletePosition($id);
	public function fetchAll();
	public function fetchById($id);
}
