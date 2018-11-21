<?php
/**
 * 文件路径： \application\index\controller\JobTest.php
 * 该控制器的业务代码中借助了thinkphp-queue 库，将一个消息推送到消息队列
 */
namespace app\queue\controller;
use think\Exception;
use think\Controller;
use think\Queue;

class Job extends Controller{

    public function actionPushMessage(){

        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'app\queue\job\PushMessage';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName  	  = "pushMessage";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData       	  = [ 'ts' => time(), 'bizId' => uniqid() ] ;
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push( $jobHandlerClassName , $jobData , $jobQueueName );
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){
            return true;
        }else{
            return false;
        }
    }

    public function actionGetRes(){

        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'app\queue\job\GetRes';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName  	  = "pushMessage";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData       	  = [ 'ts' => time(), 'bizId' => uniqid() ] ;
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push( $jobHandlerClassName , $jobData , $jobQueueName );
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){
            return true;
        }else{
            return false;
        }
    }
}