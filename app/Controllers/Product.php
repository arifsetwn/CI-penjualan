<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Product_model;
use App\Models\Category_model;

class Product extends Controller{
    protected $helper = [];

    public function __construct()
    {
        # code...
        helper(['form']);
        $this->category_model = new Category_model();
        $this->product_model = new Product_model();
    }

    public function index()
    {
        # code...
        $data['products'] = $this->product_model->getProduct();
        echo view('product/index',$data);
    }

    public function create()
    {
        # code...
        $categories = $this->category_model->where('category_status', 'Active')->findAll();
        $data['categories'] = ['' => 'Pilih Category'] + array_column($categories, 'category_name', 'category_id');
    
        echo view('product/create',$data);
    }

    public function store()
    {
        # code...
        $validation = \Config\Services::validation();

        $image = $this->request->getFile('product_image');
        $name = $image->getRandomName();
 
        $data = array(
            'category_id'           => $this->request->getPost('category_id'),
            'product_name'          => $this->request->getPost('product_name'),
            'product_price'         => $this->request->getPost('product_price'),
            'product_sku'           => $this->request->getPost('product_sku'),
            'product_status'        => $this->request->getPost('product_status'),
            'product_image'         => $name,
            'product_description'   => $this->request->getPost('product_description'),
        );
        if ($validation->run($data, 'product') == FALSE){
            session()->setFlashdata('inputs',$this->request->getPost());
            session()->setFlashdata('errors', $validation->getErrors());
            return redirect()->to(base_url('product/create'));
        }else{
            $image->move(ROOTPATH . 'public/uploads',$name);
            $simpan = $this->product_model->insertProduct($data);
            if ($simpan){
                session()->setFlashdata('success','Created Produc');
               // session()->setFlashdata('success', 'Created Product successfully');
                return redirect()->to(base_url('product'));
            }
        }
        
    }
}
