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

    public function arrCompare()
    {
        $a1 = ['a', 'b', 'c'];
        $a2 = ['a', 'c', 'd', 'e'];

        $ret0 = array_intersect($a1, $a2); // 交集（共同的数据）
        $ret1 = array_diff($a1, $ret0); // 差集（老的数据）
        $ret2 = array_diff($a2, $ret0); // 差集（新的数据）

        echo '<pre>';
        var_dump($ret0);
        var_dump($ret1);
        var_dump($ret2);
    }


}