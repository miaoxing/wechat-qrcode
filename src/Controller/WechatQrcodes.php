<?php

namespace Miaoxing\WechatQrcode\Controller;

class WechatQrcodes extends \Miaoxing\Plugin\BaseController
{
    /**
     * 留空控制该页面需要登录
     *
     * {@inheritdoc}
     */
    protected $guestPages = [];

    public function indexAction($req)
    {
        $ret = $this->event->until('preWechatQrcodesIndexAction', [$req]);
        if ($ret) {
            return $ret;
        }

        // 默认显示自己的二维码,也可以传入用户ID,显示指定用户的二维码
        if ($req['userId']) {
            $user = wei()->user()->findOneById($req['userId']);
        } else {
            $user = $this->curUser;
        }

        // 获取用户的二维码
        $title = $this->setting('dist.titleDist', '分销商') . '的二维码';
        $qrcode = wei()->weChatQrcode()->findOrCreateByUser($user, $title);

        $account = wei()->wechatAccount->getCurrentAccount();
        $api = $account->createApiService();
        $image = $api->getPermanentQrCodeUrl($qrcode['sceneId']);

        $headerTitle = ($user->getNickName() ?: '用户' . $user['id']) . '的二维码';
        $this->page->hideHeader();
        $this->page->hideFooter();

        return get_defined_vars();
    }

    public function meAction()
    {
        return $this->response->redirect(wei()->url('wechat-qrcodes', ['userId' => $this->curUser['id']]));
    }
}
