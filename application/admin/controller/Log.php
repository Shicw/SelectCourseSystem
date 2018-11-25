<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 11:51
 */
namespace app\admin\controller;
use app\AdminBaseController;
use think\Db;

class Log extends AdminBaseController
{
    /**
     * 操作日志页
     */
    public function index(){
        $rows = Db::name('log l')
            ->join('user u','l.user_id=u.id')
            ->field(['l.id','action','u.username','FROM_UNIXTIME(l.create_time,\'%Y-%m-%d %H:%i:%s\') create_time'])
            ->order('id desc')
            ->paginate(15,false);
        $page = $rows->render();
        $this->assign([
            'rows' => $rows,
            'page' => $page
        ]);
        return $this->fetch();
    }
}