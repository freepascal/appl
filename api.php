<?php

$grabber = new RequestGrabber;

$api_config = array(
    'model2table' => array(
        'User'  => 'users',
        'Group' => 'groups',
        'Post'  => 'posts'
    )
);

header('Content-type:application/json;charset:utf-8');

if ($grabber->method() === 'GET') {
    // define your APIs here
    echo json_encode(array('Appl RESTful APIs'), JSON_UNESCAPED_UNICODE);
    exit(0);

} else if ($grabber->method() === 'POST') {
    // define your APIs here

} else if ($grabber->method() === 'PUT') {
    // define your APIs here

} else if ($grabber->method() === 'DELETE') {
    // define your APIs here

}

exit(0);

class RequestGrabber {

    // default method is GET
    protected $method = 'GET';

    // must acccess via params() method
    // there are $_POST/$_GET filtered
    protected $params = array();

    // Laravel action: index, create, store, show, edit, update, destroy
    // Api action: create, read, update, delete
    public $action;

    public function get($name, $default = null) {
        return isset($this->params[$name])
                ? $this->params[$name]
                : $default;
    }

    public function debug($say_something = null) {
        if ($say_something) {
            error_log($say_something);
        }

        if ($this->method == 'POST') {
            error_log(sprintf("POST %s/\n", $_GET['model']));
        } else if (in_array($this->method, ['GET', 'PUT', 'DELETE'])) {
            error_log(sprintf("%s %s/%s\n", $this->method, $_GET['model'], $_GET['id']));
        }

        if (in_array($this->method, ['POST', 'PUT', 'DELETE'])) {
            foreach($this->params() as $k => $v) {
                error_log( sprintf("PARAM $k=$v \n"));
            }
        }
    }

    public function method() {
        return $this->method;
    }

    public function params() {
        return $this->params;
    }

    public function hasOnlyParams($params = array()) {
        return array_keys($this->params()) == $params;
    }

    public static function getHasOnlyParams($params = array(), $except = array()) {
        $excepts = array();
        foreach($except as $k) {
            $excepts[$k] = $_GET[$k];
        }
        return (array_keys(array_diff_key($_GET, $excepts))) == $params;
    }

    function __construct() {

        // first, detect http method
        $this->method = $_SERVER['REQUEST_METHOD'];

        if ($this->method === 'GET') {
            $this->params = array_diff_key($_GET, array('model' => $_GET['model']));

        } else if ($this->method === 'PUT' || $this->method === 'DELETE') {
            parse_str(file_get_contents('php://input'), $this->params);

        } else if ($this->method === 'POST') {
            // Now detect method is put or delete
            // method is put if $_POST['_METHOD'] = PUT
            $this->method = isset($_POST['_METHOD'])
                            ? (
                                in_array(strtoupper(trim($_POST['_METHOD'])), ['PUT', 'DELETE'])
                                ? strtoupper(trim($_POST['_METHOD']))
                                : 'POST'
                            )
                            : 'POST';

            if ($this->method === 'POST') {
                $this->params = $_POST;
            } else {
                $this->params = array_diff_key($_POST, array('_METHOD' => $_POST['_METHOD']));
            }
        }
    }
}

?>
