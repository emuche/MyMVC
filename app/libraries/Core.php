<?php
class Core {
    protected $currentController = 'Homes';
    protected $currentMethod = 'index';
    protected $params = [];
    protected $method = false;


    public function __construct(){   
        $url = !empty($this->getUrl()) ? $this->getUrl() : '';

        if (isset($url[0])) {
            $file_exists = file_exists('../app/controllers/'.ucwords($url[0]).'.php');
            if ($file_exists) {
                $this->currentController = ucwords($url[0]);
            }else{
                $this->currentMethod = ucwords($url[0]);
                $this->method = true;
            }
            unset($url[0]);
        }

        require_once '../app/controllers/'.$this->currentController.'.php';
        $this->currentController = new $this->currentController;

        if($this->method && !method_exists($this->currentController, $this->currentMethod)){
            Redirect::to('error404');
        }

        if(isset($url[1])){
            if(!$this->method){
                $method_exists = method_exists($this->currentController, $url[1]);
                if($method_exists){
                    $this->currentMethod = $url[1];
                }else{
                    Redirect::to('error404');
                }
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->currentController, $this->currentMethod], [$this->params]);
    }


    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}