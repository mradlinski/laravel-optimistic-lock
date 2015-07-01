<?php

namespace mromnia\OptimisticLock;

trait VersionedTrait {
	
	public function save(array $options = array())
	{
		if ($this->exists)
		{
			$this->__set(
				$this->version_field, 
				$this->__get($this->version_field) + 1
			);
		}
		else
		{
			$this->__set(
				$this->version_field, 
				0
			);
		}
		
		$query = $this->newQuery();

		if ($this->fireModelEvent('saving') === false) {
			return false;
		}
		else if ($this->exists)
		{
			if(!array_get($options, 'ignore_version', false))
			{
				$query = $this->newQuery()
					->where('id', '=', $this->id)
					->where(
						$this->version_field, 
						'=', 
						$this->__get($this->version_field) - 1
					);
			}
 
  			$result = $query->update($this->getDirty()) === 1;
  			if ($result)
 			{
 				$this->fireModelEvent('updated');
 				$saved = $result;
 			}
  			else
 			{
 				$this->__set(
					$this->version_field, 
					$this->__get($this->version_field) - 1
				);
                throw (new VersionMismatchException())->setModel($this);
 			}
		}
		else
		{
			$saved = $this->performInsert($query);
		}
		
		if ($saved) {
			$this->finishSave($options);
		}
		
		return $saved;
	}
}
