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
use think\facade\Cache;
use think\facade\Config;

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
		$memberid = $member["member_id"] ?? "";
		Cache::rm(TM_MEMBER_THIRDPARTY_LIST."_".$memberid);

        $data['uid'] = $param['uid'];
        $data['type'] = $param['type'];
        $data['add_time'] = time();
        $data['login_time'] = time();
        if(!empty($param['device_model'])){
            $data['device_model'] = $param['device_model'];
        }
        if(!empty($param['device_type'])){
            $data['device_type'] = $param['device_type'];
        }
        $data['ip'] = $ip;
        $data['member_code'] = $member['member_code'];
        $data['member_id'] = $member['member_id'];
        $data['nick_name'] = $param['member_nickname'] ?? $member['member_nickname'];
        $data['head_url'] = $param['head_pic'] ?? $member['head_pic'];
        if(!empty($param['address'])){
            $data['address'] = $param['address'];
        }
        return Db::table($this->member_third_party_db)->insert($data);
    }

    /**
     * 更新第三方登陆信息
     * @param $condition
     * @param $ip
     * @return bool|int|string
     */
    public function updateOrAddThirdParty($param,$member,$ip){
		$memberid = $member["member_id"] ?? "";
		Cache::rm(TM_MEMBER_THIRDPARTY_LIST."_".$memberid);

        $thirdParty = Db::table($this->member_third_party_db)->where(['uid'=>$param['uid'],'type'=>$param['type']])->find();
        if($thirdParty){
            $data['login_time'] = time();
            $data['ip'] = $ip;
            return Db::table($this->member_third_party_db)->where(['id'=>$thirdParty['id']])->update($data);
        }else{
            return $this->addThirdParty($param,$member,$ip);
        }
    }

    /**
     *获取信息以类型方式整理
     */
    public function ArrayToType($data){
		$data = is_array($data) ? $data : array();
        $returnData = array();
        foreach ($data as $key => $val){
            if($val['type'] == 1){
                $returnData['qq'] = $val;
            }elseif($val['type'] == 2){
                $returnData['wx'] = $val;
            }elseif ($val['type'] == 3){
                $returnData['wb'] = $val;
            }
        }
        return $returnData;
    }
    /**
     * 获取3方信息
     */
    public function getThirdPartyList($condition,$field){
        return Db::table($this->member_third_party_db)->where($condition)->field($field)->select();
    }

	/**
     * 获取3方信息
     */
    public function getThirdPartyListById($memberid,$isCache=true){
		if(empty($memberid)){
			return false;
		}

		$field = 'uid,nick_name,member_id,head_url,address,ip,type';

		if($isCache){
			$re=Cache::get(TM_MEMBER_THIRDPARTY_LIST."_".$memberid);
			if(!empty($re)){
				return $re;
			}
		}

		$condition=array();
		$condition['member_id'] = $memberid;
        $re = $this->getThirdPartyList($condition,$field);
		$re = $this->ArrayToType($re);

		Cache::set(TM_MEMBER_THIRDPARTY_LIST."_".$memberid,$re,Config::get('user_time'));

        return $re;
    }
}