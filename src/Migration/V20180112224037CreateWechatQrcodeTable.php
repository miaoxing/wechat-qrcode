<?php

namespace Miaoxing\WechatQrcode\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20180112224037CreateWechatQrcodeTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('weChatQrcode')
            ->id()
            ->int('userId')
            ->int('accountId')
            ->string('sceneId', 64)
            ->int('awardId')
            ->tinyInt('awardRule', 1)->comment('获得奖励的规则')
            ->string('type', 12)->defaults('text')
            ->string('name', 64)
            ->int('totalCount')->comment('总关注数')
            ->int('cancelCount')->comment('总取消数')
            ->int('totalHeadCount')
            ->int('cancelHeadCount')
            ->int('validCount')->comment('积累关注数')
            ->string('content', 2048)
            ->text('articleIds')->comment('关注后弹出的图文编号')
            ->tinyInt('source', 1)
            ->datetime('createTime')
            ->int('createUser')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('weChatQrcode');
    }
}
