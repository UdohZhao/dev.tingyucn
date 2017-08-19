<?php
return array(
    'ADMIN_TITLE'           =>'后台',
    'SITENAME'           =>'听娱神游约玩',
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '112.74.46.113', // 服务器地址
    'DB_NAME'               =>  'darling',          // 数据库名
    'DB_USER'               =>  'iego',      // 用户名
    'DB_PWD'                =>  'iego',          // 密码

    // 阿里大于
    'APPKEY'   =>    '24451576',
    'APPSECRET'   =>    'f476150eda547b37d0cd7abeefb82e2b',
    // 签名
    'SIGN_NAME'   =>  '阿里大于测试专用',
    // 短信内容
    'PRODUCT'    =>  '听娱约玩',
    'SIGNATRUE'  =>'听娱神游约玩',
    'TEMPLATE_CODE'   =>  array(
        // 注册验证码消息模板
        'SIGN_IN' =>  'SMS_70515128',
        //修改密码验证模板
        'ALTER_IN' =>  'SMS_70515126',
    ),

    'ALIPAY' => array(
        //应用ID,您的APPID。
        'app_id' => "2017071007708418",

        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => "MIIEpAIBAAKCAQEA84Ee0LEXgBv4frXbF1lhTFwwQJTCj5DjNJSKeKK16QmYkaESDsa6I3M9TJctZwxgUbzuVW99VToxEzhuaASKX4IutOOUG9RB4Ja2a3fC4PcgFQH6yFZvW6SB3/REoika5zKwyLnwFftkUKhXlw6B8liPwV2qltUaJNuRu/0Ay81eWKk22lXW4tLL0dNHGorLhaiYvTW7qupmW6D3nbT+K3RgrBYAYjX/dRd/bOq7VsabiZf4nSsqrLbTxw+kMC61d0JZduEfe/ZtnnBEx/9SRF7pxDHbjdjHizWsgh5gO9iy7d6gQc6Z1K7+uH4o3Hxx4PHu9fuDK253PfuNO3BQIwIDAQABAoIBAQCsgSCvf4XCgA4+1d5sYCmJyxVz6u8afe435bNjWwG8Icwv/wen6CkdzBn1FHRZuG7T+SBu7hjANAVoNGYi0nSkzLkB9OeL/4bb8GkzIRix/uB/gXOEUZd6OMS5P3cP2kbw2vxadz8ak4mxilDi9qggY5UTy6N6T2XCDrrjjozt0yHhAEPt2dWPbclckj6KhtBlPc9SC0iSvAxNs1eOPk8a0JD4yHyRYuQQHAFa3dB6bWqTcfBD+85bSlcW8TEF62ydL+nPWqUyvQbSV+H+7W4/pUW7fpr22rjfmXb25fF0+0Jviagnr6z1jook3W1REi5MfUVFxoPKqxvHdq8hXbwBAoGBAP3xD8JFGg2btIeqHP+tbsDTAs0lTNTOiZyEZzBeKBUPh2Rp4UwDW0k1Kmg7pbO1S2smeposc33PpN5KLQ8UHQt11Gs0fB9fUZsCcg8eWx4909ur+RMNjttoG5bE/pFgsmDid3AsjFgYZL67ylnlK62Ab70Mz3VwJ2brExnDskyBAoGBAPV6Zq2bcYoD5WyoipHQ0NDeG2/jBFi+wQAh0eOlrg2ES367gQJ7OIz9NRggATCydCAr6BWDgi1orbmT3ZN12uhxGCPFOeaQVeYhz9Mf4oxO9Fz7xKWI7N8ATqbGVqiSx/g37tTxEQDAtDT8EKw8mFdXVfayV8gHdsG0DLI85JqjAoGBAP2yka3iX27s+eT3XNVKrXVS2l+dPi700KJf3L+DscOoqfj1lrHcQJzY0q8juB3bp6c64A2bDx7IDcxOismfrIzAgSFBZCfrkJmuTckw6JND7Z5vJv2T8/7a+YUc9b7DvjHwzqZwux1f8XZkInrA62wA/qD+ZVzMWXEGtSRuUHkBAoGAPTUg5wbMP6KLERXRP1x2xK2s37AWRF6D1xmXsRB9nqcu/9GW8FxzFEyKcZKBWXgVlnP8MWkSC2p/brdc10japXyVjU2/CytQD8q8fCMGJQAG1Cx+stu6XDxCYDkyIGRA8jZYGcZl++8Qv+ld6uRNA/Il4BZF5v1dch0H0WV3hssCgYB/cxQKZP7L2uS1wJfUMl3TBQU3KHGSHTHygsDR7DGqFrlFXP6dMXlNo4pe59g+LmviBo1DsSqAXcHbHbSxBXNRLNSrwEkttRBPYmPwzal4+SU8csuDFY6yl2Za8zdk/KZz0maJPGn1u2Bg8N/k2j9qZp3lam09GQKj4/wjegQvyA==",

        //异步通知地址
        'notify_url' => 'http://'.$_SERVER['HTTP_HOST']."/index.php/Myorder/notifyUrl",

        //同步跳转
        'return_url' => 'http://'.$_SERVER['HTTP_HOST']."/index.php/Myorder/returnUrl",

        //编码格式
        'charset' => "UTF-8",

        //签名方式
        'sign_type'=>"RSA2",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgREJDeEc1ho+D/wGtDico3O3ey1celThr9BXDnyNQqcPSy8MtX2jNe3G6iE5unlhRq2B1FmoHzk8tDS2E2juAABcui7p8ZD4C9yU7UYkROCRviwDCYgP4TIev7ZXWtNajVW+pqr4+noUca5XwkN7UF4Y3gFx1XwQ8BxXRLny9mAebUqYs4LvWsZneJX2k09TKzVCZtIvgqBXsgITVyl2Wp897JAI03EOEln6s968EDS7TUcJOpMBA4xyc22+b7bq1yFN4Qr2eP8MGePiz31KdPFsb4Rwzpd/NScB6IdEWF1JKJEtP0TNYoWxanMArV4lZPCl/2JA361dX90iXgslqQIDAQAB",
    ),
);