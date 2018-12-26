<?php
namespace App\Controller;

use Think\Controller;

class TestsController extends Controller
{
    public function test()
    {
        $obj = new \Common\Api\Integral();
        $ret = $obj->storage(203, 7445, 2, 1);
        // dump($ret);
    }
    public function index()
    {
        dump(RSAcode("122334434"));
    }
}
