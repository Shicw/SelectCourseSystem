<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 16:47
 */
namespace app\admin\controller;
use app\admin\model\ClassModel;
use app\AdminBaseController;
use think\Db;

class Classes extends AdminBaseController
{
    /**
     * 班级管理页
     */
    public function index(){
        //获取专业列表
        $majorList = Db::name('major')->field(['id','name'])->where(['id'=>['>',1],'delete_time'=>0])->select();
        $this->assign('majorList',$majorList);
        //获取班级列表
        $rows = Db::name('class c')
            ->field(['c.id','m.name major','c.name class'])
            ->join('major m','m.id=c.major_id')
            ->where(['c.id'=>['>',1],'c.delete_time'=>0])
            ->paginate(15,false);
        $page = $rows->render();
        $this->assign([
            'rows' => $rows,
            'page' => $page
        ]);
        return $this->fetch();
    }
    /**
     * 添加班级
     */
    public function addPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $post = $this->request->post();
        if($post['name']==''){
            $this->error('班级名称不能为空');
        }
        if($post['major_id']==''){
            $this->error('专业不能为空');
        }

        $model = new ClassModel();
        $result = $model->doAdd($post);//添加

        if($result['code']==1){
            addLog('添加班级:'.$post['name'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 获取详情
     */
    public function getDetail(){
        $id = $this->request->param('id');
        //获取班级信息
        $model = new ClassModel();
        $result = $model->getDetail($id);

        $this->success('请求成功','',$result);
    }
    /**
     * 修改班级提交
     */
    public function editPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $post = $this->request->post();

        $model = new ClassModel();
        $result = $model->doEdit($post);

        if($result['code']==1){
            addLog('修改班级:'.$result['data'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 删除班级
     */
    public function delete(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');

        $model = new ClassModel();
        $result = $model->doDelete($id);//删除

        if($result['code']==1){
            addLog('删除班级:'.$result['data'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 获取对应专业下的班级
     */
    public function loadClass(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $majorId = $this->request->post('majorId');

        $result = Db::name('class')->field(['id','name'])
            ->where(['major_id'=>$majorId,'delete_time'=>0])
            ->select();
        if(count($result)>0){
            $this->success('请求成功','',$result);
        }else{
            $this->error('无数据');
        }
    }
}