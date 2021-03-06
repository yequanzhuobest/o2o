<?php
namespace app\admin\controller;
use think\Controller;
class Login extends  Controller
{
    public function index()
    {
        if(request()->isPost()) {
            // 登录的逻辑
            //获取相关的数据
            $data = input('post.');
            // 通过用户名 获取 用户相关信息
            $ret = model('Admin')->get(['username'=>$data['username']]);
            if(!$ret || $ret['status'] != 1) {
                $this->error('该管理员用户不存在或者该管理员信息未被审核通过');
            }

            if($ret->password != md5($data['password'].$ret->code)) {
                $this->error('密码不正确');
            }

            model('Admin')->updateById(['last_login_time'=>time()], $ret->id);
            // 保存用户信息  admin是作用域
            session('adminUser', $ret, 'admin');
            return $this->success('登录成功', url('index/index'));//第四个参数是设置跳转时间


        }else {
            // 获取session 目的：登陆过一次，下次登录时，(xxxx/login)自动跳转到目标页面
            $account = session('adminUser', '', 'admin');
            if($account && $account->id) {
                //跳转页面
                return $this->redirect(url('index/index'));
            }
            return $this->fetch();
        }

    }

    public function logout() {
        // 清除session
        session(null, 'admin');
        // 跳出
        $this->redirect('login/index');
    }

}