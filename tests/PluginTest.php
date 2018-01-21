<?php

namespace MiaoxingTest\WechatQrcode;

use Miaoxing\Wechat\Service\WeChatQrcode;

class PluginTest extends \Miaoxing\Plugin\Test\BaseTestCase
{
    public function testSendAward()
    {
        $award = $this->getModelServiceMock('award', ['__invoke', 'send']);

        $award->expects($this->once())
            ->method('__invoke')
            ->willReturn($award);

        $award->expects($this->once())
            ->method('send')
            ->willReturn([
                [
                    'code' => 1,
                    'message' => '发送成功',
                ],
            ]);

        $res = wei()->tester->weChatReply('<xml><ToUserName><![CDATA[ToUserName]]></ToUserName>
<FromUserName><![CDATA[FromUserName]]></FromUserName>
<CreateTime>1394729701</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[SCAN]]></Event>
<EventKey><![CDATA[1]]></EventKey>
<Ticket><![CDATA[]]></Ticket>
</xml>');

        $this->assertEquals('success', $res);
    }

    public function testSendAwardOnFirstSubscription()
    {
        $this->createQrcodeMock();

        $award = $this->getModelServiceMock('award', ['__invoke', 'send']);

        $award->expects($this->once())
            ->method('__invoke')
            ->willReturn($award);

        $award->expects($this->once())
            ->method('send')
            ->willReturn([
                [
                    'code' => 1,
                    'message' => '发送成功',
                ],
            ]);

        $fromUserName = 'FromUserName' . wei()->seq();
        $res = wei()->tester->weChatReply('<xml><ToUserName><![CDATA[ToUserName]]></ToUserName>
<FromUserName><![CDATA[' . $fromUserName . ']]></FromUserName>
<CreateTime>1394729701</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[qrscene_1]]></EventKey>
<Ticket><![CDATA[]]></Ticket>
</xml>');

        $this->assertEquals('success', $res);
    }

    public function testNotSendAwardOnScan()
    {
        $this->createQrcodeMock();

        $award = $this->getModelServiceMock('award', ['__invoke', 'send']);

        $award->expects($this->never())
            ->method('__invoke');

        $award->expects($this->never())
            ->method('send');

        $res = wei()->tester->weChatReply('<xml><ToUserName><![CDATA[ToUserName]]></ToUserName>
<FromUserName><![CDATA[FromUserName]]></FromUserName>
<CreateTime>1394729701</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[SCAN]]></Event>
<EventKey><![CDATA[1]]></EventKey>
<Ticket><![CDATA[]]></Ticket>
</xml>');

        $this->assertEquals('success', $res);
    }

    public function testNotSendAwardOnReSubscribe()
    {
        // 初始化用户
        wei()->user()->findOrCreate(['wechatOpenId' => 'FromUserName']);

        $this->createQrcodeMock();

        $award = $this->getModelServiceMock('award', ['__invoke', 'send']);

        $award->expects($this->never())
            ->method('__invoke');

        $award->expects($this->never())
            ->method('send');

        // 已存在的用户再次触发关注事件
        $res = wei()->tester->weChatReply('<xml><ToUserName><![CDATA[ToUserName]]></ToUserName>
<FromUserName><![CDATA[FromUserName]]></FromUserName>
<CreateTime>1394729701</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[]]></EventKey>
</xml>');

        $this->assertEquals('success', $res);
    }

    protected function createQrcodeMock()
    {
        $qrcode = $this->getModelServiceMock('weChatQrcode', ['__invoke', 'findOrInit', 'find', 'isNew']);

        $qrcode['awardRule'] = WeChatQrcode::AWARD_RULE_FIRST_SUBSCRIPTION;

        $qrcode->expects($this->any())
            ->method('__invoke')
            ->willReturn($qrcode);

        $qrcode->expects($this->any())
            ->method('findOrInit')
            ->willReturn($qrcode);

        $qrcode->expects($this->any())
            ->method('isNew')
            ->willReturn(false);

        // TODO dealer用到find方法,会将awardRule还原为0
        $qrcode->expects($this->any())
            ->method('find')
            ->willReturn($qrcode);
    }
}
