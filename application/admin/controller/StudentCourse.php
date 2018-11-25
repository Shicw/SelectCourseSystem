<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 11:06
 */
namespace app\admin\controller;

use app\AdminBaseController;
use think\Db;
use think\Validate;

class StudentCourse extends AdminBaseController
{
    public function index(){
        //关键字查询
        $request = input('request.');
        $keyword = '';
        $conditions = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $conditions['u1.name|u1.id|co.id|co.name|u2.name'] = ['like', "%$keyword%"];
        }
        $rows = Db::name('student_course')->alias('sc')
            ->join([
                ['user u1','u1.id=sc.user_id'],
                ['major m','m.id=u1.major_id'],
                ['class c','c.id=u1.class_id'],
                ['course co','co.id=sc.course_id'],
                ['user u2','u2.id=co.teacher_id'],
            ])
            ->field(['u1.name student','u1.id student_id','co.id course_id',
                'co.name course_name','u2.name teacher','sc.id',
                'FROM_UNIXTIME(sc.create_time,\'%Y-%m-%d %H:%i:%s\') create_time'])
            ->where($conditions)
            ->where(['sc.delete_time'=>0])
            ->paginate(15, false, [
                'query' => [
                    'keyword' => $keyword,
                ]
            ]);
        $page = $rows->render();
        $this->assign([
            'rows' => $rows,
            'page' => $page
        ]);
        return $this->fetch();
    }
}