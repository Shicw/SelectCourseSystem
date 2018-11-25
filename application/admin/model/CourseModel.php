<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 9:43
 */
namespace app\admin\model;
use think\Db;
use think\Exception;
use think\Model;

class CourseModel extends Model
{
    protected $table = 'course';

    /**
     * 获取课程列表
     * @param $keyword
     * @param $conditions
     * @param null $userId
     * @return \think\Paginator
     */
    public function getCoursesList($keyword,$conditions,$userId=null){
        if($userId!=null) $conditions['c.teacher_id'] = $userId;
        $rows = $this->alias('c')
            ->field(['c.id','c.name','u.name teacher','ct.name type','c.day',
                'c.classroom','c.begin_lesson','c.end_lesson','c.limit_student_count',
                'c.real_student_count', "c.create_time"
            ])
            ->join([
                ['user u', 'u.id=c.teacher_id'],
                ['course_type ct','ct.id=c.type']
            ])
            ->where($conditions)
            ->where(['c.delete_time' => 0])
            ->order('c.create_time desc')
            ->paginate(15, false, [
                'query' => [
                    'keyword' => $keyword,
                ]
            ]);
        return $rows;
    }
    /**
     * 创建课程
     * @param $data
     * @param $userId
     * @return array
     */
    public function doCreate($data,$userId){
        $result = $this->insert([
            'name'=>$data['name'],
            'teacher_id'=>$userId,
            'type'=>$data['type'],
            'day'=>$data['day'],
            'classroom'=>$data['classroom'],
            'begin_lesson'=>$data['begin_lesson'],
            'end_lesson'=>$data['end_lesson'],
            'limit_student_count'=>$data['limit_student_count'],
            'create_time'=>time(),
        ]);
        if ($result) {
            return ['code' => 1, 'msg' => '创建成功'];
        } else {
            return ['code' => 0, 'msg' => '创建失败'];
        }
    }

    /**取消课程
     * @param $courseId
     * @param $userId
     * @return array
     */
    public function doCancel($courseId,$userId){
        //判断是否存在该课程
        $find = $this->where(['id'=>$courseId,'teacher_id'=>$userId,'delete_time'=>0])->find();
        if(!$find){
            return ['code' => 0, 'msg' => '不存在该课程'];
        }
        //查看该课程下是否有已选学生
        $haveJoin = Db::name('student_course')->where(['course_id'=>$courseId,'delete_time'=>0])->find();
        if($haveJoin){
            $this->startTrans();//开启事务处理
            try {
                $result1 = Db::name('student_course')->where(['course_id' => $courseId, 'delete_time' => 0])->update(['delete_time' => time()]);
                if (!$result1) {
                    throw new Exception("取消课程失败");
                }
                $result2 = $this->where(['id' => $courseId, 'delete_time' => 0])->update(['delete_time' => time()]);
                if (!$result2) {
                    throw new Exception("取消课程失败");
                }
                $this->commit();// 提交事务
            } catch (\Exception $e) {
                $this->rollback();// 回滚事务
                return ['code' => 0, 'msg' => $e->getMessage()];
            }
            return ['code' => 1, 'msg' => '取消课程成功'];
        }else{
            $result = $this->where(['course_id' => $courseId, 'delete_time' => 0])->update(['delete_time' => time()]);
            if($result){
                return ['code' => 1, 'msg' => '取消课程成功'];
            }else{
                return ['code' => 0, 'msg' => '取消课程失败'];
            }
        }
    }
    /**
     * 选课
     * @param $courseId
     * @param $userId
     * @return array
     */
    public function doJoin($courseId,$userId){
        //判断是否存在该课程
        $find = $this->where(['id'=>$courseId,'delete_time'=>0])->find();
        if(!$find){
            return ['code' => 0, 'msg' => '不存在该课程'];
        }
        if($find['limit_student_count']==$find['real_student_count']){
            return ['code' => 0, 'msg' => '该课程已满员'];
        }
        //判断该学生是否已经选择该课程
        $ifJoined = Db::name('student_course')->where(['course_id'=>$courseId,'user_id'=>$userId,'delete_time'=>0])->find();
        if($ifJoined){
            return ['code' => 0, 'msg' => '您已选择过该课程'];
        }
        $this->startTrans();//开启事务处理
        try {
            $result1 = Db::name('student_course')->insert([
                'course_id'=>$courseId,
                'user_id'=>$userId,
                'create_time'=>time()
            ]);
            if (!$result1) {
                throw new Exception("选课失败");
            }
            $result2 = $this->where('id',$courseId)->setInc('real_student_count',1);
            if (!$result2) {
                throw new Exception("选课失败");
            }
            $this->commit();// 提交事务
        } catch (\Exception $e) {
            $this->rollback();// 回滚事务
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
        return ['code' => 1, 'msg' => '选课成功'];
    }

    /**
     * 退选课程
     * @param $courseId
     * @param $userId
     * @return array
     */
    public function doDropOut($courseId,$userId){
        //判断是否存在该课程
        $find = $this->where(['id'=>$courseId,'delete_time'=>0])->find();
        if(!$find){
            return ['code' => 0, 'msg' => '不存在该课程'];
        }
        $this->startTrans();//开启事务处理
        try {
            $result1 = Db::name('student_course')
                ->where(['course_id'=>$courseId,'user_id'=>$userId])
                ->update(['delete_time'=>time()]);
            if (!$result1) {
                throw new Exception("退选失败");
            }
            $result2 = $this->where('id',$courseId)->setDec('real_student_count',1);
            if (!$result2) {
                throw new Exception("退选失败");
            }
            $this->commit();// 提交事务
        } catch (\Exception $e) {
            $this->rollback();// 回滚事务
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
        return ['code' => 1, 'msg' => '退选成功'];
    }
}