<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GroceryCrud\Core\GroceryCrud;
class CCMulti extends Controller
{
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    private function _getDatabaseConnection() {
        $databaseConnection = config('database.default');
        $databaseConfig = config('database.connections.' . $databaseConnection);
        return [
            'adapter' => [
                'driver' => 'Pdo_Mysql',
                'database' => $databaseConfig['database'],
                'username' => $databaseConfig['username'],
                'password' => $databaseConfig['password'],
                'charset' => 'utf8'
            ] 
        ];
    }
    /**
     * Show the datagrid for customers
     *
     * @return \Illuminate\Http\Response
     */
     

     
     public function locationarea()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTable('location_area');
        $crud->setSubject('Product', 'Products');
        $crud->columns(['location_id', 'name']);
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gcmulti', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
     
       public function locationcart()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTable('location_cart');
        $crud->setSubject('Cart', 'Cart Location');
        $crud->columns(['idcode','cart_id', 'area_fk']);
        $crud->setRelation('area_fk','location_area','name');
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gcmulti', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
    
      public function locationshelf()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');
        $crud = new GroceryCrud($config, $database);
        $crud->setTable('location_shelf');
        $crud->setSubject('Product', 'Products');
        $crud->columns(['idcode','shelf_id', 'cart_fk']);
        $crud->setRelation('cart_fk','location_cart','{idcode}{cart_id} - {area_fk}');
        $crud->callbackAddForm(function ($data) {$data['idcode'] = 'SHL';return $data;});
        $crud->unsetColumns(['datelastmodified']);
        $crud->unsetFields(['datelastmodified']);
        $output = $crud->render();
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                  ->header('Content-Type', 'application/json')
                  ->header('charset', 'utf-8');
        }
        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;
        return view('gcmulti', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }
    
    
    
}