<?php
// backend/controllers/BaseController.php
class BaseController
{
    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    // Hiển thị view
    public function view($view, $data = [])
    {
        extract($data);
        if (file_exists("../views/$view.php")) {
            require_once "../views/$view.php";
        } else {
            die("View không tồn tại: $view");
        }
    }

    // Trả về JSON cho AJAX
    public function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Trả về HTML cho AJAX
    public function htmlResponse($html)
    {
        echo $html;
        exit;
    }

    // Xử lý yêu cầu (ví dụ: GET, POST)
    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';

        switch ($method) {
            case 'GET':
                if (method_exists($this, $action . 'Action')) {
                    $this->{$action . 'Action'}();
                } else {
                    $this->indexAction();
                }
                break;
            case 'POST':
                if (method_exists($this, $action . 'Action')) {
                    $this->{$action . 'Action'}();
                } else {
                    $this->indexAction();
                }
                break;
            default:
                $this->indexAction();
                break;
        }
    }

    // Hành động mặc định
    public function indexAction()
    {
        $this->view('index');
    }
}