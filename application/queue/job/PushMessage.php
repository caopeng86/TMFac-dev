<?php
  /**
   * 文件路径： \application\index\job\Hello.php
   * 这是一个消费者类，用于处理 helloJobQueue 队列中的任务
   */
  namespace app\queue\job;

  use app\member\model\PushMessageModel;
  use think\queue\Job;

  class PushMessage {

      /**
       * fire方法是消息队列默认调用的方法
       * @param Job            $job      当前的任务对象
       * @param array|mixed    $data     发布任务时自定义的数据
       */
      public function fire(Job $job,$data){
          // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
          $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
          if(!$isJobStillNeedToBeDone){
              $job->delete();
              return;
          }
          $isJobDone = $this->doPushMessageJob();

          if ($isJobDone) {
              //如果任务执行成功， 记得删除任务
              $job->delete();
          }else{
              if ($job->attempts() > 3) {
                  //通过这个方法可以检查这个任务已经重试了几次了
                  $job->delete();
                  // 也可以重新发布这个任务
                  //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                  //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
              }
          }
      }

      /**
       * 有些消息在到达消费者时,可能已经不再需要执行了
       * @param array|mixed    $data     发布任务时自定义的数据
       * @return boolean                 任务执行的结果
       */
      private function checkDatabaseToSeeIfJobNeedToBeDone($data){
          return true;
      }

      /**
       * 根据消息中的数据进行实际的业务处理
       * @param array|mixed    $data     发布任务时自定义的数据
       * @return boolean                 任务执行的结果
       */
      private function doPushMessageJob() {
          // 根据消息中的数据进行实际的业务处理...
          //获取推送消息
          $pushMessageModel = new PushMessageModel();
          $condition[] = ['status','=',1];//待推送状态
          $condition[] = ['push_time','<=',time()];//推送Jpush时间
          $pushList = $pushMessageModel->getList($condition,false,200);
          $JPush = new \app\extend\controller\Jpush();
          $returnData = [
              'success'=>0,
              'error'=>0,
          ];
          foreach ($pushList as $val){
              //推送消息
              $extras = array(
                  'title'=>$val['title'],
                  'content'=>$val['content'],
                  'msg_id'=>$val['id'],
                  'iosInfo'=>json_decode($val['ios_info'],true),
                  'androidInfo'=>json_decode($val['android_info'],true)
              );
              $re = $JPush::JPushAll($extras);
              if($re['http_code'] == 200){
                  $pushMessageModel->updateInfo(['id'=>$val['id']],['status'=>2,'cid'=>$re['body']['msg_id']]);//推送成功
                  $returnData['info'][$val['id']] = '成功';
                  $returnData['success']++;
              }else{
                  $returnData['info'][$val['id']] = '失败';
                  $returnData['error']++;
              }
          }
          return true;
      }
  }