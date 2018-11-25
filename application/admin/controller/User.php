<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 14:57
 */
namespace app\admin\controller;

use app\admin\model\UserModel;
use app\AdminBaseController;
use think\Db;
use think\Validate;

class User extends AdminBaseController
{
    /**
     * 用户列表
     * @return mixed
     */
    public function index(){
        //关键字查询
        $request = input('request.');
        $keyword = '';
        $conditions = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];

            $conditions['u.id|mobile|u.name|m.name|c.name'] = ['like', "%$keyword%"];
        }
        $rows = Db::name('user u')
            ->field(['u.id','u.name','u.username','u.sex','u.type','u.mobile','m.name major','c.name class',
                "IF(u.last_login_time>0, FROM_UNIXTIME(u.last_login_time,'%Y-%m-%d %H:%i:%s'), '无') last_login_time",
                "FROM_UNIXTIME(u.create_time,'%Y-%m-%d %H:%i:%s') create_time"
            ])
            ->join([
                ['major m','m.id=u.major_id'],
                ['class c','c.id=u.class_id']
            ])
            ->where($conditions)
            ->where(['u.type'=>['>',0],'u.delete_time'=>0])
            ->order('u.create_time desc')
            ->paginate(15,false, [
                'query' => [
                    'keyword' => $keyword,
                ]
            ]);
        $page = $rows->render();
        $this->assign([
            'rows' => $rows,
            'page' => $page
        ]);
        //获取专业列表
        $majorList = Db::name('major')->field(['id','name'])->where(['delete_time'=>0])->select();
        $this->assign('majorList',$majorList);
        return $this->fetch();
    }
    /**
     * 添加用户
     */
    public function addPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $validate = new Validate([
            'id'       => 'require',
            'name'     => 'require',
            'sex'      => 'require',
            'mobile'   => 'require',
            'type'     => 'require',
            'major_id' => 'require',
            'class_id' => 'require',
        ]);
        $validate->message([
            'id.require'       => '用户编号不能为空',
            'name.require'     => '姓名不能为空',
            'sex.require'      => '性别不能为空',
            'mobile.require'   => '手机号不能为空',
            'type.require'     => '用户类型不能为空',
            'major_id.require' => '专业不能为空',
            'class_id.require' => '班级不能为空',
        ]);
        $post = $this->request->post();
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }

        $model = new UserModel();
        $result = $model->doAdd($post);//添加

        if($result['code']==1){
            addLog('添加用户:'.$post['id'],getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 修改用户提交
     */
    public function editPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $validate = new Validate([
            'id'       => 'require',
            'name'     => 'require',
            'sex'      => 'require',
            'mobile'   => 'require',
            'type'     => 'require',
            'major_id' => 'require',
            'class_id' => 'require',
        ]);
        $validate->message([
            'id.require'       => '用户编号不能为空',
            'name.require'     => '姓名不能为空',
            'sex.require'      => '性别不能为空',
            'mobile.require'   => '手机号不能为空',
            'type.require'     => '用户类型不能为空',
            'major_id.require' => '专业不能为空',
            'class_id.require' => '班级不能为空',
        ]);
        $post = $this->request->post();
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }

        $model = new UserModel();
        $result = $model->doEdit($post);

        if($result['code']==1){
            addLog('修改用户信息:'.$post['id'],getAdminId());
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 删除用户
     */
    public function delete(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');

        $model = new UserModel();
        $result = $model->doDelete($id);//删除

        if($result['code']==1){
            addLog('删除用户:'.$id,getAdminId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 获取用户详情,用于编辑页面
     */
    public function getDetail(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $id = $this->request->post('id');
        $model = new UserModel();
        $result = $model->getDetail($id);//用户详情
        //获取专业列表
        $majorList = Db::name('major')->field(['id','name'])->where(['delete_time'=>0])->select();
        //获取班级列表
        $classList = Db::name('class')->field(['id','name'])->where(['major_id'=>$result['major_id'],'delete_time'=>0])->select();

        $data = [
            'detail' => $result,
            'majorList' => $majorList,
            'classList' => $classList
        ];
        $this->success('请求成功','',$data);
    }
}