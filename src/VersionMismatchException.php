<?php

namespace mromnia\OptimisticLock;

use RuntimeException;

class VersionMismatchException extends RuntimeException
{
    protected $model;
    
    public function setModel($model)
    {
        $this->model = $model;
        $this->message = "Version mismatch on model [{$model}].";
        return $this;
    }
    
    public function getModel()
    {
        return $this->model;
    }
}
