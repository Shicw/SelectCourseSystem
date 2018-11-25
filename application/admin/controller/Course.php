<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 9:41
 */
namespace app\admin\controller;

use app\admin\model\CourseModel;
use app\AdminBaseController;
use think\Db;
use think\Validate;

class Course extends AdminBaseController
{
    /**
     * 课程列表
     * @return mixed
     */
    public function index()
    {
        //关键字查询
        $request = input('request.');
        $keyword = '';
        $conditions = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $conditions['c.id|c.name|u.name|ct.name'] = ['like', "%$keyword%"];
        }
        $model = new CourseModel();
        $rows = $model->getCoursesList($keyword,$conditions);
        $page = $rows->render();
        $this->assign([
            'rows' => $rows,
            'page' => $page
        ]);
        //获取专业列表
        $majorList = Db::name('major')->field(['id', 'name'])->where(['delete_time' => 0])->select();
        $this->assign('majorList', $majorList);
        return $this->fetch();
    }

    /**
     * 获取该课程已选学生的列表
     */
    public function getJoinList(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');
        $list = Db::name('student_course')->alias('sc')
            ->join([
                ['user u','u.id=sc.user_id'],
                ['major m','m.id=u.major_id'],
                ['class c','c.id=u.class_id'],

            ])
            ->field(['u.id','u.name','u.mobile','u.sex','m.name major','c.name class',
                'FROM_UNIXTIME(sc.create_time,\'%Y-%m-%d %H:%i:%s\') create_time'])
            ->where(['sc.course_id'=>$id,'sc.delete_time'=>0])
            ->select();
        $count = count($list);
        if($count==0){
            $this->error('无数据');
        }else{
            $this->success('请求成功','',['count'=>$count,'list'=>$list]);
        }
    }
}