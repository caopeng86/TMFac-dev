<?php
namespace app\api\model;

use think\Model;

class CommonModel extends Model
{
    //天马工场定义的表名
    protected $answer_db=TM_PREFIX.'answer';
    protected $branch_db=TM_PREFIX.'branch';
    protected $classes_db=TM_PREFIX.'classes';
    protected $component_db=TM_PREFIX.'component';
    protected $fix_item_db=TM_PREFIX.'component';
    protected $kind_db=TM_PREFIX.'kind';
    protected $member_db=TM_PREFIX.'member';
    protected $member_comment_db=TM_PREFIX.'member_comment';
    protected $member_history_db=TM_PREFIX.'member_history';
    protected $member_star_db=TM_PREFIX.'member_star';
    protected $myprize_db=TM_PREFIX.'myprize';
    protected $parameterex_db=TM_PREFIX.'parameterex';
    protected $portal_db=TM_PREFIX.'portal';
    protected $privilege_db=TM_PREFIX.'privilege';
    protected $prize_db=TM_PREFIX.'prize';
    protected $push_db=TM_PREFIX.'push';
    protected $question_db=TM_PREFIX.'question';
    protected $role_db=TM_PREFIX.'role';
    protected $role_component_db=TM_PREFIX.'role_component';
    protected $role_site_db=TM_PREFIX.'role_site';
    protected $role_privilege_db=TM_PREFIX.'role_privilege';
    protected $role_user_db=TM_PREFIX.'role_user';
    protected $ruleprize_db=TM_PREFIX.'ruleprize';
    protected $site_db=TM_PREFIX.'site';
    protected $user_db = TM_PREFIX.'user';
    protected $user_log_db=TM_PREFIX.'user_Log';
}