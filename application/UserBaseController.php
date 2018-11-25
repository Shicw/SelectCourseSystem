<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 16:02
 */
namespace app;
use think\Db;
use think\Controller;
class UserBaseController extends Controller
{
    public function _initialize(){
        $this->checkUserLogin();
    }
    //判断用户是否登录
    public function checkUserLogin()
    {
        $userId = getUserId();
        if (empty($userId)) {
            $this->redirect('User/Login/index');
        }
    }
}