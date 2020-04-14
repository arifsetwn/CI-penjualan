<?php namespace App\Models;
use CodeIgniter\Model;

class Product_model extends Model{
    protected $table = 'products';

    public function getProduct($id=false)
    {
        # code...
        if ($id == false){
            return $this->table('products')
                        ->join('categories','categories.category_id = products.category_id')
                        ->get()
                        ->getResultArray();
        } else {
            return $this->table('products')
                        ->join('categories','categories.category_id = products.category_id')
                        ->where('products.product_id',$id)
                        ->get()
                        ->getRowArray();
        }
    }

    public function insertProduct($data)
    {
        # code...
        return $this->db->table($this->table)->insert($data);
    }

    public function updateProduct($data,$id)
    {
        # code...
        return $this->db->table($this->table)->update($data,['product_id' => $id]);
    }

    public function deleteProduct($id)
    {
        # code...
        return $this->db->table($this->table)->delete(['product_id' => $id]);
    }

}