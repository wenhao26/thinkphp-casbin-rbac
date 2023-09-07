<?php


namespace app\controller;

use app\BaseController;
use tauthz\facade\Enforcer;
use think\facade\View;

/**
 * 权限管理
 *
 * Class Purview
 * @package app\controller
 */
class Purview extends BaseController
{
    /**
     * 策略管理
     */
    public function addPolicy()
    {
        if (request()->isAjax()) {
            $group      = input('group', '', 'trim');
            $policyList = input('policy/a');

            // 给某个角色组规划对应的权限列表
            if ($policyList) {
                foreach ($policyList as $policy) {
                    $arr = explode('/', $policy);
                    Enforcer::addPolicy($group, strtolower($arr[0]), strtolower($arr[1]));
                }
            }
            echo json_encode(['code' => 0, 'msg' => '', 'data' => []]);
            die;
        }

        return View::fetch();
    }

    /**
     * 用户管理
     */
    public function addUser()
    {
        if (request()->isAjax()) {
            $group    = input('group', '', 'trim');
            $username = input('username', '', 'trim');

            // 给用户赋予某个角色
            Enforcer::addRoleForUser($username, $group);
            echo json_encode(['code' => 0, 'msg' => '', 'data' => []]);
            die;
        }

        return View::fetch();
    }


}