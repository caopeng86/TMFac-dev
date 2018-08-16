<?php
/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2018/8/13
 * Time: 15:41
 */
namespace app\member\model;

use app\api\model\CommonModel;
use think\Db;
use think\Model;
class MemberThirdPartyModel extends CommonModel
{
    /**
     * 新增第三方登陆信息
     * @param $param
     * @param $member
     * @param $ip
     * @return int|string
     */
    public function addThirdParty($param,$member,$ip){
        $data['uid'] = $param['uid'];
        $data['type'] = $param['type'];
        $data['add_time'] = time();
        $data['login_time'] = time();
        $data['device_model'] = $param['device_model'];
        $data['device_type'] = $param['device_type'];
        $data['ip'] = $ip;
        $data['member_code'] = $member['member_code'];
        $data['member_id'] = $member['member_id'];
        return Db::table($this->member_third_party_db)->insert($data);
    }

    /**
     * 更新第三方登陆信息
     * @param $condition
     * @param $ip
     * @return bool|int|string
     */
    public function updateOrAddThirdParty($param,$member){
        $thirdParty = Db::table($this->member_third_party_db)->where(['uid'=>$param['uid'],'type'=>$param['type']])->find();
        if($thirdParty){
            $data['login_time'] = time();
            return Db::table($this->member_third_party_db)->where(['id'=>$thirdParty['id']])->update($data);
        }else{
            return $this->addThirdParty($param,$member,$ip);
        }
    }
}