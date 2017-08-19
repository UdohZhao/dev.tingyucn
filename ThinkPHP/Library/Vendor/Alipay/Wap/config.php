<?php
$config = array (
		//应用ID,您的APPID。
		'app_id' => "2017071007708418",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEA84Ee0LEXgBv4frXbF1lhTFwwQJTCj5DjNJSKeKK16QmYkaES
Dsa6I3M9TJctZwxgUbzuVW99VToxEzhuaASKX4IutOOUG9RB4Ja2a3fC4PcgFQH6
yFZvW6SB3/REoika5zKwyLnwFftkUKhXlw6B8liPwV2qltUaJNuRu/0Ay81eWKk2
2lXW4tLL0dNHGorLhaiYvTW7qupmW6D3nbT+K3RgrBYAYjX/dRd/bOq7VsabiZf4
nSsqrLbTxw+kMC61d0JZduEfe/ZtnnBEx/9SRF7pxDHbjdjHizWsgh5gO9iy7d6g
Qc6Z1K7+uH4o3Hxx4PHu9fuDK253PfuNO3BQIwIDAQABAoIBAQCsgSCvf4XCgA4+
1d5sYCmJyxVz6u8afe435bNjWwG8Icwv/wen6CkdzBn1FHRZuG7T+SBu7hjANAVo
NGYi0nSkzLkB9OeL/4bb8GkzIRix/uB/gXOEUZd6OMS5P3cP2kbw2vxadz8ak4mx
ilDi9qggY5UTy6N6T2XCDrrjjozt0yHhAEPt2dWPbclckj6KhtBlPc9SC0iSvAxN
s1eOPk8a0JD4yHyRYuQQHAFa3dB6bWqTcfBD+85bSlcW8TEF62ydL+nPWqUyvQbS
V+H+7W4/pUW7fpr22rjfmXb25fF0+0Jviagnr6z1jook3W1REi5MfUVFxoPKqxvH
dq8hXbwBAoGBAP3xD8JFGg2btIeqHP+tbsDTAs0lTNTOiZyEZzBeKBUPh2Rp4UwD
W0k1Kmg7pbO1S2smeposc33PpN5KLQ8UHQt11Gs0fB9fUZsCcg8eWx4909ur+RMN
jttoG5bE/pFgsmDid3AsjFgYZL67ylnlK62Ab70Mz3VwJ2brExnDskyBAoGBAPV6
Zq2bcYoD5WyoipHQ0NDeG2/jBFi+wQAh0eOlrg2ES367gQJ7OIz9NRggATCydCAr
6BWDgi1orbmT3ZN12uhxGCPFOeaQVeYhz9Mf4oxO9Fz7xKWI7N8ATqbGVqiSx/g3
7tTxEQDAtDT8EKw8mFdXVfayV8gHdsG0DLI85JqjAoGBAP2yka3iX27s+eT3XNVK
rXVS2l+dPi700KJf3L+DscOoqfj1lrHcQJzY0q8juB3bp6c64A2bDx7IDcxOismf
rIzAgSFBZCfrkJmuTckw6JND7Z5vJv2T8/7a+YUc9b7DvjHwzqZwux1f8XZkInrA
62wA/qD+ZVzMWXEGtSRuUHkBAoGAPTUg5wbMP6KLERXRP1x2xK2s37AWRF6D1xmX
sRB9nqcu/9GW8FxzFEyKcZKBWXgVlnP8MWkSC2p/brdc10japXyVjU2/CytQD8q8
fCMGJQAG1Cx+stu6XDxCYDkyIGRA8jZYGcZl++8Qv+ld6uRNA/Il4BZF5v1dch0H
0WV3hssCgYB/cxQKZP7L2uS1wJfUMl3TBQU3KHGSHTHygsDR7DGqFrlFXP6dMXlN
o4pe59g+LmviBo1DsSqAXcHbHbSxBXNRLNSrwEkttRBPYmPwzal4+SU8csuDFY6y
l2Za8zdk/KZz0maJPGn1u2Bg8N/k2j9qZp3lam09GQKj4/wjegQvyA==",

		//异步通知地址
		'notify_url' => "http://testpay.icunji.com/notify_url.php",

		//同步跳转
		'return_url' => "http://testpay.icunji.com/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgREJDeEc1ho+D/wGtDico3O3ey1celThr9BXDnyNQqcPSy8MtX2jNe3G6iE5unlhRq2B1FmoHzk8tDS2E2juAABcui7p8ZD4C9yU7UYkROCRviwDCYgP4TIev7ZXWtNajVW+pqr4+noUca5XwkN7UF4Y3gFx1XwQ8BxXRLny9mAebUqYs4LvWsZneJX2k09TKzVCZtIvgqBXsgITVyl2Wp897JAI03EOEln6s968EDS7TUcJOpMBA4xyc22+b7bq1yFN4Qr2eP8MGePiz31KdPFsb4Rwzpd/NScB6IdEWF1JKJEtP0TNYoWxanMArV4lZPCl/2JA361dX90iXgslqQIDAQAB",


);