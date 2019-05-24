<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 18:14
 */

namespace app\member\model;


use app\api\model\CommonModel;
use think\Db;
use think\Model;
use think\facade\Cache;
use think\facade\Config;

class MemberModel extends CommonModel
{
    /**
     * 计算会员表总条数
     * @param $condition
     * @return int|string
     */
    public function getCount($condition,$orwhere = ''){
        if(empty($orwhere)){
            $re = Db::table($this->member_db)->where($condition)->count('member_id');
        }else{
            $re = Db::table($this->member_db)->where($condition)->where($orwhere)->count('member_id');
        }
        return $re;
    }

    /**
     * 获取会员列表数据
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getMemberList($condition, $field='', $limit='', $order='',$orwhere = ''){
        if(empty($orwhere)){
            $re = Db::table($this->member_db)->join($this->site_db, $this->member_db.'.site_code = '.$this->site_db.'.site_code')
                ->field($field)->where($condition)->limit($limit)->order($order)->select();
        }else{
            $re = Db::table($this->member_db)->join($this->site_db, $this->member_db.'.site_code = '.$this->site_db.'.site_code')
                ->field($field)->where($condition)->where($orwhere)->limit($limit)->order($order)->select();
        }
        return $re;
    }

    /**
     * 获取一条会员信息
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getMemberInfo($condition, $field=''){
        $re = Db::table($this->member_db)->field($field)->where($condition)->find();
        return $re;
    }

	/**
     * 获取一条会员信息
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getMemberInfoById($memberId, $isCache=true){
		if(empty($memberId)){
			return false;
		}
		$field = 'member_id,member_code, member_name, member_nickname, member_real_name,site_code,email,deleted,sex_edit_time,birthday_edit_time,mobile_edit_time,wb_edit_time,wx_edit_time,qq_edit_time,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,password,receive_notice,wifi_show_image,list_auto_play,login_type,member_sn,channel_sources';

		if($isCache){
			$cacheValue=Cache::get(TM_MEMBER_BASE_INFO."_".$memberId);
			if(!empty($cacheValue) && !empty($cacheValue['member_id'])){
				return $cacheValue;
			}
		}

		$condition=array();
		$condition['member_id'] = $memberId;
        $re = Db::table($this->member_db)->field($field)->where($condition)->find();
		if(empty($re)){
			return array();
		}

		$memberId = strval($re['member_id']);

		Cache::set(TM_MEMBER_BASE_INFO."_".$memberId,$re,Config::get('user_time'));
		
        return $re;
    }

	/**
     * 获取一条会员信息
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getMemberInfoByMobile($mobile, $site_code, $isCache=true){
		if(empty($mobile)||empty($site_code)){
			return false;
		}
		$field = 'member_id,member_code, member_name, member_nickname, member_real_name,site_code,email,deleted,sex_edit_time,birthday_edit_time,mobile_edit_time,wb_edit_time,wx_edit_time,qq_edit_time,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,password,receive_notice,wifi_show_image,list_auto_play,login_type,member_sn,channel_sources';

		if($isCache){
			$memberId=Cache::get(TM_MEMBER_MEMBER_ID."_".$site_code."_".$mobile);
			if(!empty($memberId)){
				$cacheValue=Cache::get(TM_MEMBER_BASE_INFO."_".$memberId);
				if(!empty($cacheValue) && !empty($cacheValue['member_id'])){
					return $cacheValue;
				}
			}
		}

		$condition=array();
		$condition['mobile'] = $mobile;
		$condition['site_code'] = $site_code;
        $re = Db::table($this->member_db)->field($field)->where($condition)->find();
		if(empty($re)){
			return array();
		}

		$memberId = strval($re['member_id']);

		Cache::set(TM_MEMBER_MEMBER_ID."_".$site_code."_".$mobile,$memberId,Config::get('user_time'));
		Cache::set(TM_MEMBER_BASE_INFO."_".$memberId,$re,Config::get('user_time'));
		
        return $re;
    }

	/**
     * 获取一条会员信息
     * @param $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws
     */
    public function getMemberInfoByThird($uid, $register_source, $site_code, $isCache=true){
		if(empty($uid) || empty($register_source) || empty($site_code)){
			return false;
		}
		$field = 'member_id,member_code, member_name,member_sn,member_nickname, member_real_name,site_code,email,deleted,sex_edit_time,birthday_edit_time,mobile_edit_time,wb_edit_time,wx_edit_time,qq_edit_time,
         mobile, head_pic, create_time, status, wx, qq, zfb, wb,birthday,sex,ip,point,access_key_create_time,close_start_time,close_end_time,password,receive_notice,wifi_show_image,list_auto_play,login_type,channel_sources';

		if($isCache){
			$memberId=Cache::get(TM_MEMBER_MEMBER_ID."_".$register_source."_".$site_code."_".$uid);
			if(!empty($memberId)){
				$cacheValue=Cache::get(TM_MEMBER_BASE_INFO."_".$memberId);
				if(!empty($cacheValue) && !empty($cacheValue['member_id'])){
					return $cacheValue;
				}
			}
		}

		$condition=array();
		$condition[$register_source] = $uid;
		$condition['site_code'] = $site_code;
        $res = Db::table($this->member_db)->field($field)->where($condition)->find();
		if(empty($res)){
			return array();
		}
		
		$memberId = strval($res['member_id']);

		Cache::set(TM_MEMBER_MEMBER_ID."_".$register_source."_".$site_code."_".$uid, $memberId, Config::get('user_time'));
		Cache::set(TM_MEMBER_BASE_INFO."_".$memberId,$res,Config::get('user_time'));

        return $res;
    }

    /**
     * 新增会员
     * @param $data
     * @return int|string
     */
    public function addMember($data){
        $re = Db::table($this->member_db)->insertGetId($data); 
        return $re;
    }

    /**
     * 修改会员信息
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updateMember($condition, $data){
		$re = Db::table($this->member_db)->where($condition)->update($data);

		$memberId = $condition['member_id'] ?? "";
		if(!empty($memberId)){
			Cache::rm(TM_MEMBER_BASE_INFO."_".$memberId);
		}

        return $re;
    }

	/**
     * 统计分组用户数据
     */
    public function countGroupMember($condition,$group,$field,$cache = false){
        $re = Db::table($this->member_db)->where($condition);
        if($cache > 0){ //缓存
            $re = $re->cache(true,$cache);
        }
        $re = $re->group($group)->field($field)->select();
        return $re;
    }

    /*获取会员注册渠道来源来源*/
    public function getChannelSourcesList(){
       return Db::table($this->member_db)->field("channel_sources")->group("channel_sources")->select();
    }
}