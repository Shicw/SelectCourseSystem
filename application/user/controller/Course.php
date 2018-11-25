<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/25
 * Time: 11:01
 */
namespace app\user\controller;

use app\admin\model\CourseModel;
use app\admin\model\CourseTypeModel;
use app\UserBaseController;
use think\Db;
use think\Validate;

class Course extends UserBaseController
{
    /**
     * 课程列表
     */
    public function index(){
        //获取登录用户信息
        $user = getUser();
        $this->assign($user);

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
        return $this->fetch();
    }
    /**
     * 我创建的课程
     */
    public function myCreate(){

        //关键字查询
        $request = input('request.');
        $keyword = '';
        $conditions = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $conditions['c.id|c.name|u.name|ct.name'] = ['like', "%$keyword%"];
        }
        $model = new CourseModel();
        $rows = $model->getCoursesList($keyword,$conditions,getUserId());
        $page = $rows->render();
        $this->assign([
            'rows' => $rows,
            'page' => $page
        ]);
        //获取登录用户信息
        $user = getUser();
        $this->assign($user);
        return $this->fetch();
    }
    /**
     * 创建课程
     */
    public function create(){
        //获取登录用户信息
        $user = getUser();
        $this->assign($user);
        //获取课程类别
        $typeList = Db::name('course_type')->where('delete_time',0)->select();
        $this->assign('typeList',$typeList);
        return $this->fetch();
    }
    /**
     * 创建课程提交
     */
    public function createPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $validate = new Validate([
            'name'     => 'require',
            'type'      => 'require',
            'day'   => 'require',
            'classroom'     => 'require',
            'begin_lesson' => 'require|integer|min:1|max:12',
            'end_lesson' => 'require|integer|min:1|max:12',
            'limit_student_count' => 'require|integer',
        ]);
        $validate->message([
            'name.require'     => '课程名称不能为空',
            'type.require'      => '类别不能为空',
            'day.require'   => '上课日不能为空',
            'classroom.require'     => '上课地点不能为空',
            'begin_lesson.require' => '开始节数不能为空',
            'end_lesson.require' => '结束节数不能为空',
            'limit_student_count.require' => '限选人数不能为空',
        ]);
        $post = $this->request->post();
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }
        if($post['end_lesson'] < $post['begin_lesson']){
            $this->error('课程结束节数需大于开始节数');
        }
        $model = new CourseModel();
        $result = $model->doCreate($post,getUserId());

        if($result['code']==1){
            addLog('创建课程:'.$post['name'],getUserId());
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
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
    public function myJoin(){
        //关键字查询
        $request = input('request.');
        $keyword = '';
        $conditions = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $conditions['c.id|c.name|u.name|ct.name'] = ['like', "%$keyword%"];
        }
        $rows = Db::name('student_course')->alias('sc')
            ->field(['c.id','c.name','u.name teacher','ct.name type','c.day',
                'c.classroom','c.begin_lesson','c.end_lesson','c.limit_student_count',
                'c.real_student_count', "c.create_time"
            ])
            ->join([
                ['course c','c.id=sc.course_id'],
                ['user u', 'u.id=c.teacher_id'],
                ['course_type ct','ct.id=c.type'],
            ])
            ->where($conditions)
            ->where(['sc.delete_time' => 0,'sc.user_id' => getUserId()])
            ->order('sc.create_time desc')
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
        //获取登录用户信息
        $user = getUser();
        $this->assign($user);
        return $this->fetch();
    }
    /**
     * 选课
     */
    public function join(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');//课程号
        $userId = getUserId();
        //判断是否是选课时间
        $startStopTime = Db::name('option')->where('name','select_start_and_stop_time')->value('value');
        $startStopTime = json_decode($startStopTime,1);
        $now = time();
        if($now<$startStopTime['start_time'] || $now>$startStopTime['stop_time']){
            $this->error('现在不是选课时间');
        }

        $model = new CourseModel();
        $result = $model->doJoin($id,$userId);
        if($result['code']==1){
            addLog('选课:'.$userId.'加入'.$id,getUserId());
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 退选
     */
    public function dropOut(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');//课程号
        $userId = getUserId();
        //判断是否是选课时间
        $startStopTime = Db::name('option')->where('name','select_start_and_stop_time')->value('value');
        $startStopTime = json_decode($startStopTime,1);
        $now = time();
        if($now<$startStopTime['start_time'] || $now>$startStopTime['stop_time']){
            $this->error('现在不是选课时间,您不能退选课程');
        }

        $model = new CourseModel();
        $result = $model->doDropOut($id,$userId);
        if($result['code']==1){
            addLog('退选:'.$userId.'退出'.$id,getUserId());
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 取消课程
     */
    public function cancel(){
        if (!$this->request->isPost()) {
            $this->error('请求失败');
        }
        $id = $this->request->post('id');//课程号
        $model = new CourseModel();
        $result = $model->doCancel($id,getUserId());
        if($result['code']==1){
            addLog('取消课程:'.$id,getUserId());
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
}