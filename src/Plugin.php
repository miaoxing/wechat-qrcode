<?php

namespace Miaoxing\WechatQrcode;

use Miaoxing\Plugin\Service\User;
use Miaoxing\Wechat\Service\WechatAccount;
use Miaoxing\Wechat\Service\WeChatQrcode;
use Wei\WeChatApp;

class Plugin extends \Miaoxing\Plugin\BasePlugin
{
    protected $name = '微信二维码';

    protected $description = '包含添加生成二维码功能';

    public function onAdminNavGetNavs(&$navs, &$categories, &$subCategories)
    {
        $navs[] = [
            'parentId' => 'wechat-account',
            'url' => 'admin/wechat-qrcode/index',
            'name' => '二维码管理',
            'sort' => 700,
        ];
    }

    /**
     * 扫描并关注
     *
     * @param WeChatApp $app
     * @param \Miaoxing\Plugin\Service\User $user
     * @param \Miaoxing\Wechat\Service\WechatAccount $account
     */
    public function onWechatScan(WeChatApp $app, User $user, WechatAccount $account)
    {
        $sceneId = $app->getScanSceneId();
        if (!$sceneId) {
            return;
        }

        // 检查二维码是否存在
        $qrcode = wei()->weChatQrcode->findAndCacheBySceneId($sceneId);
        if (!$qrcode || $qrcode->isNew()) {
            return;
        }

        $this->addTags($qrcode, $user);
        $this->incrStat($app, $qrcode, $user);
    }

    protected function addTags(WeChatQrcode $qrcode, User $user)
    {
        if ($qrcode['addTagIds']) {
            $addTagIds = explode(',', $qrcode['addTagIds']);

            if (!wei()->userTag->hasTag() && $defaultTagId = wei()->setting('user.defaultTagId')) {
                $addTagIds[] = $defaultTagId;
            }

            wei()->userTag->updateTag($addTagIds);
        }
    }

    /**
     * 增加扫描的人数和次数
     *
     * @param WeChatApp $app
     * @param \Miaoxing\Wechat\Service\WeChatQrcode $qrcode
     * @param \Miaoxing\Plugin\Service\User $user
     */
    protected function incrStat(WeChatApp $app, WeChatQrcode $qrcode, User $user)
    {
        if ($app->getEvent() !== 'subscribe') {
            return;
        }

        // 判断是否已存在的用户
        if (!wei()->wechatQrcodeLog()->curApp()->find([
            'userId' => $user['id'],
            'type' => 1,
            'wechatQrcodeId' => $qrcode['id'],
        ])) {
            $qrcode->incr('totalHeadCount');
        }
        $qrcode->incr('totalCount');
        $qrcode->incr('validCount');
        $qrcode->save();

        // 记下操作日志
        wei()->wechatQrcodeLog()->setAppId()->save([
            'userId' => $user['id'],
            'type' => 1,
            'wechatQrcodeId' => $qrcode['id'],
        ]);
    }

    /**
     * 取关
     * @param WeChatApp $app
     * @param User $user
     * @param WechatAccount $account
     */
    public function onWechatUnsubscribe(WeChatApp $app, User $user, WechatAccount $account)
    {
        // 判断来源是否是二维码
        if (!$user['source']) {
            return;
        }

        // 判断是否存在二维码
        $qrcode = wei()->weChatQrcode()->findOrInit(['sceneId' => $user['source']]);
        if ($qrcode->isNew()) {
            return;
        }

        // 不存在关注记录的不记录
        if (!wei()->wechatQrcodeLog()->curApp()->find(['userId' => $user['id'], 'type' => 1])) {
            return;
        }

        // 判断是否已存在的用户
        if (!wei()->wechatQrcodeLog()->curApp()->find([
            'userId' => $user['id'],
            'type' => 0,
            'wechatQrcodeId' => $qrcode['id'],
        ])) {
            $qrcode->incr('cancelHeadCount');
        }
        // 记录统计数据
        $qrcode->incr('cancelCount');
        $qrcode->decr('validCount');
        $qrcode->save();

        // 记下操作日志
        wei()->wechatQrcodeLog()->setAppId()->save([
            'userId' => $user['id'],
            'type' => 0,
            'wechatQrcodeId' => $qrcode['id'],
        ]);
    }
}
