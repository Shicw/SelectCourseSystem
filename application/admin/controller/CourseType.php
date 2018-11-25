<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 9:32
 */
namespace app\admin\controller;
use app\admin\model\CourseTypeModel;
use app\AdminBaseController;
use think\Db;

class CourseType extends AdminBaseController
{
    /**
     * 专业管理页
     */
    public function index(){
        $rows = Db::name('course_type')
            ->field(['id','name','description'])
            ->where(['delete_time'=>0])
            ->paginate(15,false);
        $page = $rows->render();
        $this->assign([
            'rows' => $rows,
            'page' => $page
        ]);
        return $this->fetch();
    }
    /**
     * 添加专业
     */
    public function addPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $post = $this->request->post();
        if($post['name']==''){
            $this->error('专业名称不能为空');
        }

        $model = new CourseTypeModel();
        $result = $model->doAdd($post);//添加

        if($result['code']==1){
            addLog('添加课程类别:'.$post['name'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 修改课程类别提交
     */
    public function editPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $post = $this->request->post();

        $model = new CourseTypeModel();
        $result = $model->doEdit($post);

        if($result['code']==1){
            addLog('修改课程类别:'.$result['data'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 删除课程类别
     */
    public function delete(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');

        $model = new CourseTypeModel();
        $result = $model->doDelete($id);//删除

        if($result['code']==1){
            addLog('删除课程类别:'.$result['data'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }

    /**
     * 获取详情
     */
    public function getDetail(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');
        $model = new CourseTypeModel();
        $result = $model->getDetail($id);

        $this->success('请求成功','',$result);

    }
}