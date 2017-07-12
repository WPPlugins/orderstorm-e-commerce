<?php
/*
	Copyright (C) 2010-2017 OrderStorm Ecommerce, Inc. (e-mail: wordpress-ecommerce@orderstorm.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class jsonResultSet
{
	private $fieldNames;
	private $rowData;
	private $metaData;

	public function __construct(&$jsonResponse)
	{
		$this->fieldNames = array();
		$this->rowData = array();
		$this->metaData = array();

		if (is_array($jsonResponse) &&
			count($jsonResponse) > 1 &&
			array_key_exists(0, $jsonResponse) &&
			array_key_exists(1, $jsonResponse) &&
			is_array($jsonResponse[0]) &&
			is_array($jsonResponse[1])
		) {
			$intColumnsCount = 0;
			foreach ($jsonResponse[0] as $fieldName)
			{
				$this->fieldNames[$fieldName] = $intColumnsCount;
				$intColumnsCount += 1;
			}
			$this->rowData = $jsonResponse[1];
			if (isset($jsonResponse[2]))
			{
				if (is_array($jsonResponse[2]))
				{
					$this->metaData = $jsonResponse[2];
				}
			}
		}
	}

	public function rowCount()
	{
		return count($this->rowData);
	}

	public function fieldValue($rowIndex, $fieldName)
	{
		$return = NULL;

		if (!empty($this->fieldNames))
		{
			$return = $this->rowData[$rowIndex][$this->fieldNames[$fieldName]];
		}

		return $return;
	}

	public function row($rowIndex)
	{
		$return = array();

		if ($rowIndex >= 0 && $rowIndex < count($this->rowData))
		{
			foreach($this->fieldNames as $key => $value)
			{
				$return[$key] = $this->rowData[$rowIndex][$value];
			}
		}

		return $return;
	}

	public function metaData()
	{
		return $this->metaData;
	}

	public function getFieldNames()
	{
		return $this->fieldNames;
	}
}
?>
