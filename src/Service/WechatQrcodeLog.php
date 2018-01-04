<?php

namespace Miaoxing\WechatQrcode\Service;

use Miaoxing\Plugin\BaseModel;

class WechatQrcodeLog extends BaseModel
{
    protected $table = 'wechatQrcodeLogs';

    protected $providers = [
        'db' => 'app.db',
    ];
}
