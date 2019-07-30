<?php
namespace App\Repositories;

use App\Producer;

class ProducerRepository
{

    protected $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }
    public function create($attributes)
    {
        return $this->producer->create($attributes);
    }

    public function all($related = null)
    {
        return $this->producer->get();
    }

    public function find($id)
    {
        return $this->producer->find($id);
    }

    public function update($id, $attributes)
    {
        $producer = $this->producer->find($attributes['id']);
        if ($attributes['sheet']){
            $sheet = $attributes['sheet'];
            if(! $producer->sheets->where('title', $sheet['title'])->count() ){
                $sheet = new \App\Sheet(['title' => $sheet['title']]);
                $producer->sheets()->save($sheet);
            }
        }

        return $producer;
    }

    public function delete($id)
    {
        return $this->producer->find($id)->delete();
    }
}
