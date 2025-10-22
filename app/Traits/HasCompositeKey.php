<?php

namespace App\Traits;

trait HasCompositeKey
{
    /**
     * Get the value of the model's primary key.
     */
    public function getKey()
    {
        $keys = [];
        foreach ($this->getKeyName() as $keyName) {
            $keys[] = $this->getAttribute($keyName);
        }
        return implode('-', $keys);
    }

    /**
     * Set the keys for a save update query.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keyNames = (array) $this->getKeyName();
        
        foreach ($keyNames as $keyName) {
            $query->where($keyName, $this->getAttribute($keyName));
        }
        
        return $query;
    }

    /**
     * Get the primary key for the model.
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Determine if the model uses incrementing primary keys.
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Perform the actual delete query on this model instance.
     */
    protected function performDeleteOnModel()
    {
        $this->setKeysForSaveQuery($this->newModelQuery())->delete();
    }
}