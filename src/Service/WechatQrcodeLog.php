<?php

namespace Miaoxing\WechatQrcode\Service;

use miaoxing\plugin\BaseModel;

class WechatQrcodeLog extends BaseModel
{
    protected $table = 'wechatQrcodeLogs';

    protected $providers = [
        'db' => 'app.db'
    ];
}
