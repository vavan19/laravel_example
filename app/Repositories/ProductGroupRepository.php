<?php
namespace App\Repositories;

use App\ProductGroup;

class ProductGroupRepository
{

    protected $productGroup;

    public function __construct(ProductGroup $productGroup)
    {
        $this->productGroup = $productGroup;
    }
    public function create($attributes)
    {
        return $this->productGroup->create($attributes);
    }

    public function all()
    {
        return $this->productGroup->all();
    }

    public function find($id)
    {
        return $this->productGroup->find($id);
    }

    public function update($id, array $attributes)
    {
        return $this->productGroup->find($id)->update($attributes);
    }

    public function delete($id)
    {
        return $this->productGroup->find($id)->delete();
    }
}
