<?php
namespace App\Repositories;

use App\Product;

class ProductRepository
{

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
    public function create($attributes)
    {
        return $this->product->create($attributes);
    }

    public function all()
    {
        return $this->product->all();
    }

    public function find($id)
    {
        return $this->product->find($id);
    }
    public function where(array $attributes)
    {
        return $this->product->where($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->product->find($id)->update($attributes);
    }

    public function updateFromParse($id, array $attributes)
    {
        $product = $this->product->find($id);
        if($product->product_group_id){
            $attributes['product_group_id'] = $product->product_group_id;
        }

        return $product->update($attributes);
    }

    public function updateOrCreate(array $attributes)
    {
        return $this->product->updateOrCreate($attributes);
    }


    public function delete($id)
    {
        return $this->product->find($id)->delete();
    }

    public function getFillable()
    {
        return $this->product->getFillable();
    }

    public function existsWhere(array $attributes){
        if( $product = $this->product->where($attributes)->first() )
            return $product->id;
        else
            return false;
    }
}
