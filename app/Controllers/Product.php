<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Product_model;
use App\Models\Category_model;

class Product extends Controller
{
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
        $category = $this->request->getGet('category');
        $keyword = $this->request->getGet('keyword');

        $data['category'] = $category;
        $data['keyword'] = $keyword;

        $categories = $this->category_model->where('category_status', 'Active')->findAll();
        $data['categories'] = ['' => 'Pilih Category'] + array_column($categories, 'category_name', 'category_id');

        $where = [];
        $like = [];
        $or_like = [];

        if (!empty($category)) {
            $where = ['products.category_id' => $category];
        }
        if (!empty($keyword)) {
            $like   = ['products.product_name' => $keyword];
            $or_like   = ['products.product_sku' => $keyword, 'products.product_description' => $keyword];
        }

        $paginate = 5;
        $data['products'] = $this->product_model->join('categories', 'categories.category_id = products.category_id')->paginate($paginate, 'product');
        $data['pager'] = $this->product_model->pager;
        echo view('product/index', $data);
    }

    public function create()
    {
        # code...
        $categories = $this->category_model->where('category_status', 'Active')->findAll();
        $data['categories'] = ['' => 'Pilih Category'] + array_column($categories, 'category_name', 'category_id');

        echo view('product/create', $data);
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
        if ($validation->run($data, 'product') == FALSE) {
            session()->setFlashdata('inputs', $this->request->getPost());
            session()->setFlashdata('errors', $validation->getErrors());
            return redirect()->to(base_url('product/create'));
        } else {
            $image->move(ROOTPATH . 'public/uploads', $name);
            $simpan = $this->product_model->insertProduct($data);
            if ($simpan) {
                session()->setFlashdata('success', 'Created Produc');
                // session()->setFlashdata('success', 'Created Product successfully');
                return redirect()->to(base_url('product'));
            }
        }
    }

    public function show($id)
    {
        # code...
        $data['product'] = $this->product_model->getProduct($id);
        echo view('product/show', $data);
    }

    public function edit($id)
    {
        # code...
        $categories = $this->category_model->where('category_status', 'Active')->findAll();
        $data['categories'] = ['' => 'Pilih Category'] + array_column($categories, 'category_name', 'category_id');

        $data['product'] = $this->product_model->getProduct($id);
        echo view('product/edit', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('product_id');

        $validation =  \Config\Services::validation();

        // get file
        $image = $this->request->getFile('product_image');
        // random name file
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

        if ($validation->run($data, 'product') == FALSE) {
            session()->setFlashdata('inputs', $this->request->getPost());
            session()->setFlashdata('errors', $validation->getErrors());
            return redirect()->to(base_url('product/edit/' . $id));
        } else {
            // upload
            $image->move(ROOTPATH . 'public/uploads', $name);
            // update
            $ubah = $this->product_model->updateProduct($data, $id);
            if ($ubah) {
                session()->setFlashdata('info', 'Updated Product successfully');
                return redirect()->to(base_url('product'));
            }
        }
    }

    public function delete($id)
    {
        # code...
        $hapus = $this->product_model->deleteProduct($id);
        if ($hapus) {
            session()->setFlashdata('warning', 'Deleted Product successfully');
            return redirect()->to(base_url('product'));
        }
    }
}
