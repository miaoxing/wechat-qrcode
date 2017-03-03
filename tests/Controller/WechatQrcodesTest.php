<?php

namespace MiaoxingTest\WechatQrcode\Controller;

class WechatQrcodesTest extends \Miaoxing\Plugin\Test\BaseControllerTestCase
{
    protected $statusCodes = [
        'me' => 302,
    ];

    /**
     * @dataProvider providerForActions
     */
    public function testActions($action, $code = null)
    {
        $mock = $this->getServiceMock('wechatApi', [
            'getPermanentQrCodeUrl',
        ]);
        $mock->expects($this->any())
            ->method('getPermanentQrCodeUrl')
            ->willReturn('http://baidu.com');

        return parent::testActions($action, $code);
    }

    public function testGetQrcode()
    {
        $nextSceneId = wei()->weChatQrcode()->getNextSceneId();

        $this->assertUserGetQrcode($nextSceneId);

        $this->assertUserGetQrcode($nextSceneId + 1);
    }

    protected function assertUserGetQrcode($nextSceneId)
    {
        $user = wei()->user()->save();

        wei()->curUser->loginById($user['id']);

        $mock = $this->getServiceMock('wechatApi', [
            'getPermanentQrCodeUrl',
        ]);
        $mock->expects($this->once())
            ->method('getPermanentQrCodeUrl')
            ->willReturn('http://baidu.com');

        wei()->tester()
            ->controller('wechatQrcodes')
            ->action('index')
            ->exec();

        $qrcode = wei()->weChatQrcode()->find(['userId' => $user['id']]);

        $this->assertEquals($nextSceneId, $qrcode['sceneId']);
    }
}
