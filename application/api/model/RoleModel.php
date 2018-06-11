<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 13:01
 */

namespace app\api\model;


use think\Db;
use think\Model;

class RoleModel extends CommonModel
{
    /**
     * 获取角色列表,带组织机构名
     * @param $condition
     * @param string $field
     * @param string $limit
     * @param string $order
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRoleBranchList($condition, $field='', $limit='', $order=''){
        $re = Db::table($this->role_db)->join($this->branch_db, $this->role_db.'.branch_code = '.$this->branch_db.'.branch_code', 'LEFT')
            ->where($condition)->field($field)->limit($limit)->order($order)->select();
        return $re;
    }

    /**
     * 新增角色
     * @param $data
     * @return int|string
     * @throws
     */
    public function addRole($data){
        $re = Db::table($this->role_db)->insert($data);
        return $re;
    }

    /**
     * 修改角色
     * @param $condition
     * @param $data
     * @return int|string
     * @throws
     */
    public function updateRole($condition, $data){
        $re = Db::table($this->role_db)->where($condition)->update($data);
        return $re;
    }

    /**
     * 删除角色及其关联数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteRole($condition){
        $re = Db::table($this->role_db)->where($condition)->delete();//角色表数据
        return $re;
    }

    /**
     * 删除角色表及关联表数据
     * @param $condition
     * @return bool
     */
    public function deleteAboutRole($condition){
        $status = false;
        //开启事务
        Db::startTrans();
        try{
            $this->deleteRoleComponent($condition);
            $this->deleteRolePrivilege($condition);
            $this->deleteRoleSite($condition);
            $this->deleteRoleUser($condition);
            $this->deleteRole($condition);
            // 提交事务
            Db::commit();
            $status = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        return $status;
    }

    /**
     * 角色站点表修改数据
     * @param $condition
     * @param $data
     * @return bool
     */
    public function saveRoleSiteAll($condition, $data){
        $status = false;
        //开启事务
        Db::startTrans();
        try{
            //先删除相关数据,再新增,避免重复数据
            $this->deleteRoleSite($condition);
            $this->addRoleSiteAll($data);
            // 提交事务
            Db::commit();
            $status = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        return $status;
    }

    /**
     * 角色应用表修改数据
     * @param $condition
     * @param $data
     * @return bool
     */
    public function saveRoleComponent($condition, $data){
        $status = false;
        //开启事务
        Db::startTrans();
        try{
            //先删除相关数据,再新增,避免重复数据
            $this->deleteRoleComponent($condition);
            $this->addRoleComponentAll($data);
            // 提交事务
            Db::commit();
            $status = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        return $status;
    }

    /**
     * 角色权限表修改数据
     * @param $condition
     * @param $data
     * @return bool
     */
    public function saveRolePrivilege($condition, $data){
        $status = false;
        //开启事务
        Db::startTrans();
        try{
            //先删除相关数据,再新增,避免重复数据
            $this->deleteRolePrivilege($condition);
            $this->addRolePrivilegeAll($data);
            // 提交事务
            Db::commit();
            $status = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        return $status;
    }

    /**
     * 角色权限表修改数据
     * @param $condition
     * @param $data
     * @return bool
     */
    public function saveRoleUser($condition, $data){
        $status = false;
        //开启事务
        Db::startTrans();
        try{
            //先删除相关数据,再新增,避免重复数据
            $this->deleteRoleUser($condition);
            $this->addRoleUserAll($data);
            // 提交事务
            Db::commit();
            $status = true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        return $status;
    }

/************************************************角色站点中间表********************************************************/
    /**
     * 删除角色站点表中数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteRoleSite($condition){
        $re = Db::table($this->role_site_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 新增角色站点表中数据
     * @param $data
     * @return mixed
     */
    public function addRoleSiteAll($data){
        $re = Db::table($this->role_site_db)->insertAll($data);
        return $re;
    }

    /**
     * 获取角色站点数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRoleSiteList($condition, $field=''){
        $re = Db::table($this->role_site_db)->field($field)->where($condition)->select();
        return $re;
    }

    /**
     * 获取角色对应的站点详细数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRoleSiteListDetail($condition, $field=''){
        $re = Db::table($this->role_site_db)->join( $this->site_db, $this->role_site_db.'.site_code = '.$this->site_db.'.site_code')
            ->where($condition)->field($field)->select();
        return $re;
    }
/************************************************角色插件中间表********************************************************/
    /**
     * 删除角色插件表中数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteRoleComponent($condition){
        $re = Db::table($this->role_component_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 新增角色插件表中数据
     * @param $data
     * @return mixed
     * @throws
     */
    public function addRoleComponentAll($data){
        $re = Db::table($this->role_component_db)->insertAll($data);
        return $re;
    }

    /**
     * 获取角色插件数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRoleComponentList($condition, $field=''){
        $re = Db::table($this->role_component_db)->field($field)->where($condition)->select();
        return $re;
    }

    /**
     * 获取角色对应的应用详细数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRoleComponentListDetail($condition, $field=''){
        $re = Db::table($this->role_component_db)->join($this->component_db, $this->role_component_db.'.component_code ='.$this->component_db.'.component_code')
            ->where($condition)->field($field)->select();
        return $re;
    }
/************************************************角色权限中间表********************************************************/
    /**
     * 删除角色权限表中数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteRolePrivilege($condition){
        $re = Db::table($this->role_privilege_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 新增角色权限表中数据
     * @param $data
     * @return mixed
     */
    public function addRolePrivilegeAll($data){
        $re = Db::table($this->role_privilege_db)->insertAll($data);
        return $re;
    }

    /**
     * 获取角色权限数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRolePrivilegeList($condition, $field=''){
        $re = Db::table($this->role_privilege_db)->field($field)->where($condition)->select();
        return $re;
    }

    /**
     * 获取角色对应的权限详细数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRolePrivilegeListDetail($condition, $field=''){
        $re = Db::table($this->role_privilege_db)->join($this->privilege_db, $this->role_privilege_db.'.privilege_code ='.$this->role_privilege_db.'.privilege_code')
            ->where($condition)->field($field)->select();
        return $re;
    }
/************************************************角色用户中间表********************************************************/
    /**
     * 删除角色插件表中数据
     * @param $condition
     * @return int
     * @throws
     */
    public function deleteRoleUser($condition){
        $re = Db::table($this->role_user_db)->where($condition)->delete();
        return $re;
    }

    /**
     * 新增角色插件表中数据
     * @param $data
     * @return mixed
     * @throws
     */
    public function addRoleUserAll($data){
        $re = Db::table($this->role_user_db)->insertAll($data);
        return $re;
    }

    /**
     * 获取角色用户数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRoleUserList($condition, $field=''){
        $re = Db::table($this->role_user_db)->field($field)->where($condition)->select();
        return $re;
    }

    /**
     * 获取用户对应的角色详细数据
     * @param $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws
     */
    public function getRoleUserListDetail($condition, $field=''){
        $re = Db::table($this->role_user_db)->join($this->role_db, $this->role_user_db.'.role_code = '.$this->role_db.'.role_code')
            ->where($condition)->field($field)->select();
        return $re;
    }

}