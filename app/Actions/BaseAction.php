<?php

namespace App\Actions;

abstract class BaseAction
{
    /**
     * Execute the action with the given data.
     *
     * @param array $data Input data for the action
     * @return mixed The result of the action
     */
    abstract public function handle(array $data): mixed;

    /**
     * Run the action. Override this method to add pre/post processing.
     *
     * @param array $data Input data for the action
     * @return mixed The result of the action
     */
    public function run(array $data): mixed
    {
        return $this->handle($data);
    }
}
