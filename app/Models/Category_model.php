<?php namespace App\Models;
use CodeIgniter\Model;

class Category_model extends Model{
    protected $table = 'categories';

    public function getCategory($id = false)
    {
        # code...
        if ($id == false){
            return $this->findAll();
        }else{
            return $this->getWhere(['category_id' => $id ]);
        }
    }

    public function insertCategory($data)
    {
        # code...
        return $this->db->table($this->table)->insert($data);
    }
    public function updateCategory($data, $id)
    {
        # code...
        return $this->db->table($this->table)->update($data,['category_id' => $id]);
    }

    public function deleteCategory($id)
    {
        # code...
        return $this->db->table($this->table)->delete(['category_id' => $id]);
    }

}