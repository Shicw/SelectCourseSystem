<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 21:02
 */
namespace app\admin\controller;
use app\admin\model\MajorModel;
use app\AdminBaseController;
use think\Db;

class Major extends AdminBaseController
{
    /**
     * 专业管理页
     */
    public function index(){
        $rows = Db::name('major')
            ->field(['id','name','description'])
            ->where(['id'=>['>',1],'delete_time'=>0])
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

        $model = new MajorModel();
        $result = $model->doAdd($post);//添加

        if($result['code']==1){
            addLog('添加专业:'.$post['name'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 修改专业提交
     */
    public function editPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $post = $this->request->post();

        $model = new MajorModel();
        $result = $model->doEdit($post);

        if($result['code']==1){
            addLog('修改专业:'.$result['data'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 删除专业
     */
    public function delete(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');

        $model = new MajorModel();
        $result = $model->doDelete($id);//删除

        if($result['code']==1){
            addLog('删除专业:'.$result['data'],getAdminId());//记录日志
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
        $model = new MajorModel();
        $result = $model->getDetail($id);

        $this->success('请求成功','',$result);

    }
}