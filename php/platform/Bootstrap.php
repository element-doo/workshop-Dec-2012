<?php
abstract class Bootstrap
{
    const API_HOST = 'api.dsl-platform.com';

    public static function init()
    {
        self::performChecks();

        $configPath = __DIR__.'/bootstrap/Config.php';
        if (!file_exists($configPath)) {
            self::ensureBootstrapLoaded();
        }

        require_once $configPath;
    }

    private static $httpOK;
    private static $curlOK, $curlSsl, $curlZLib;

    public static function performChecks()
    {
        self::$httpOK = in_array('http', stream_get_wrappers());
        self::$curlOK = in_array('curl', get_loaded_extensions());

        if (!self::$curlOK) {
            self::remotePredefinedError('curl-missing');
        }

        self::$curlSsl = false;
        self::$curlZLib = false;
        if (self::$curlOK) {
            $curlVersion = curl_version();
            $curlFeatures = $curlVersion['features'];
            self::$curlSsl = ($curlFeatures & CURL_VERSION_SSL) !== 0;
            self::$curlZLib = ($curlFeatures & CURL_VERSION_LIBZ) !== 0;
        }

        if (!self::$curlSsl) {
            self::remotePredefinedError('curl-no-ssl');
        }
    }

    private static function remotePredefinedError($code)
    {
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error', true, 500);
        }

        if (PHP_SAPI !== 'cli' && self::$httpOK) {
            header('Content-Type: text/html; charset=UTF-8');
            $errorUrl = 'http://'.self::API_HOST.'/alpha/error/bootstrap/'.$code;

            $message = @file_get_contents($errorUrl);
            if ($message !== false) {
                echo $message;
                exit(500);
            }
        }

        header('Content-Type: text/plain; charset=UTF-8');
        echo 'DSL Platform could not be initialized.
Error was: '.$code;
        exit(500);
    }

    const CA_CERT = '-----BEGIN CERTIFICATE-----
MIIHyTCCBbGgAwIBAgIBATANBgkqhkiG9w0BAQUFADB9MQswCQYDVQQGEwJJTDEW
MBQGA1UEChMNU3RhcnRDb20gTHRkLjErMCkGA1UECxMiU2VjdXJlIERpZ2l0YWwg
Q2VydGlmaWNhdGUgU2lnbmluZzEpMCcGA1UEAxMgU3RhcnRDb20gQ2VydGlmaWNh
dGlvbiBBdXRob3JpdHkwHhcNMDYwOTE3MTk0NjM2WhcNMzYwOTE3MTk0NjM2WjB9
MQswCQYDVQQGEwJJTDEWMBQGA1UEChMNU3RhcnRDb20gTHRkLjErMCkGA1UECxMi
U2VjdXJlIERpZ2l0YWwgQ2VydGlmaWNhdGUgU2lnbmluZzEpMCcGA1UEAxMgU3Rh
cnRDb20gQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwggIiMA0GCSqGSIb3DQEBAQUA
A4ICDwAwggIKAoICAQDBiNsJvGxGfHiflXu1M5DycmLWwTYgIiRezul38kMKogZk
pMyONvg45iPwbm2xPN1yo4UcodM9tDMr0y+v/uqwQVlntsQGfQqedIXWeUyAN3rf
OQVSWff0G0ZDpNKFhdLDcfN1YjS6LIp/Ho/u7TTQEceWzVI9ujPW3U3eCztKS5/C
Ji/6tRYccjV3yjxd5srhJosaNnZcAdt0FCX+7bWgiA/deMotHweXMAEtcnn6RtYT
Kqi5pquDSR3l8u/d5AGOGAqPY1MWhWKpDhk6zLVmpsJrdAfkK+F2PrRt2PZE4XNi
HzvEvqBTViVsUQn3qqvKv3b9bZvzndu/PWa8DFaqr5hIlTpL36dYUNk4dalb6kMM
Av+Z6+hsTXBbKWWc3apdzK8BMewM69KN6Oqce+Zu9ydmDBpI125C4z/eIT574Q1w
+2OqqGwaVLRcJXrJosmLFqa7LH4XXgVNWG4SHQHuEhANxjJ/GP/89PrNbpHoNkm+
Gkhpi8KWTRoSsmkXwQqQ1vp5Iki/untp+HDH+no32NgN0nZPV/+Qt+OR0t3vwmC3
Zzrd/qqc8NSLf3Iizsafl7b4r4qgEKjZ+xjGtrVcUjyJthkqcwEKDwOzEmDyei+B
26Nu/yYwl/WL3YlXtq09s68rxbd2AvCl1iuahhQqcvbjM4xdCUsT37uMdBNSSwID
AQABo4ICUjCCAk4wDAYDVR0TBAUwAwEB/zALBgNVHQ8EBAMCAa4wHQYDVR0OBBYE
FE4L7xqkQFulF2mHMMo0aEPQQa7yMGQGA1UdHwRdMFswLKAqoCiGJmh0dHA6Ly9j
ZXJ0LnN0YXJ0Y29tLm9yZy9zZnNjYS1jcmwuY3JsMCugKaAnhiVodHRwOi8vY3Js
LnN0YXJ0Y29tLm9yZy9zZnNjYS1jcmwuY3JsMIIBXQYDVR0gBIIBVDCCAVAwggFM
BgsrBgEEAYG1NwEBATCCATswLwYIKwYBBQUHAgEWI2h0dHA6Ly9jZXJ0LnN0YXJ0
Y29tLm9yZy9wb2xpY3kucGRmMDUGCCsGAQUFBwIBFilodHRwOi8vY2VydC5zdGFy
dGNvbS5vcmcvaW50ZXJtZWRpYXRlLnBkZjCB0AYIKwYBBQUHAgIwgcMwJxYgU3Rh
cnQgQ29tbWVyY2lhbCAoU3RhcnRDb20pIEx0ZC4wAwIBARqBl0xpbWl0ZWQgTGlh
YmlsaXR5LCByZWFkIHRoZSBzZWN0aW9uICpMZWdhbCBMaW1pdGF0aW9ucyogb2Yg
dGhlIFN0YXJ0Q29tIENlcnRpZmljYXRpb24gQXV0aG9yaXR5IFBvbGljeSBhdmFp
bGFibGUgYXQgaHR0cDovL2NlcnQuc3RhcnRjb20ub3JnL3BvbGljeS5wZGYwEQYJ
YIZIAYb4QgEBBAQDAgAHMDgGCWCGSAGG+EIBDQQrFilTdGFydENvbSBGcmVlIFNT
TCBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTANBgkqhkiG9w0BAQUFAAOCAgEAFmyZ
9GYMNPXQhV59CuzaEE44HF7fpiUFS5Eyweg78T3dRAlbB0mKKctmArexmvclmAk8
jhvh3TaHK0u7aNM5Zj2gJsfyOZEdUauCe37Vzlrk4gNXcGmXCPleWKYK34wGmkUW
FjgKXlf2Ysd6AgXmvB618p70qSmD+LIU424oh0TDkBreOKk8rENNZEXO3SipXPJz
ewT4F+irsfMuXGRuczE6Eri8sxHkfY+BUZo7jYn0TZNmezwD7dOaHZrzZVD1oNB1
ny+v8OqCQ5j4aZyJecRDjkZy42Q2Eq/3JR44iZB3fsNrarnDy0RLrHiQi+fHLB5L
EUTINFInzQpdn4XBidUaePKVEFMy3YCEZnXZtWgo+2EuvoSoOMCZEoalHmdkrQYu
L6lwhceWD3yJZfWOQ1QOq92lgDmUYMA0yZZwLKMS9R9Ie70cfmu3nZD0Ijuu+Pwq
yvqCUqDvr0tVk+vBtfAii6w0TiYiBKGHLHVKt+V9E9e4DGTANtLJL4YSjCMJwRuC
O3NJo2pXh5Tl1njFmUNj403gdy3hZZlyaQQaRwnmDwFWJPsfvw55qVguucQJAX6V
um0ABj6y6koQOdjQK/W/7HW/lwLFCRsI3FU34oH7N4RDYiDK51ZLZer+bMEkkySh
NOsF/5oirpt9P/FlUQqmMGqz9IgcgA38corog14=
-----END CERTIFICATE-----';

    public static function ensureCertPath()
    {
        $caCertPath = __DIR__.'/startssl-ca.pem';

        if (!file_exists($caCertPath)) {
            file_put_contents($caCertPath, self::CA_CERT);
        }

        return $caCertPath;
    }

    public static function ensureBootstrapLoaded()
    {
        $curl = curl_init('https://'.self::API_HOST.'/alpha/bootstrap');
        curl_setopt_array($curl, array(
            CURLOPT_CAINFO => self::ensureCertPath(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json; charset=UTF-8'
            )
        ));

        if (self::$curlZLib) {
            curl_setopt($curl, CURLOPT_ENCODING, 'deflate');
        }

        $files = curl_exec($curl);
        curl_close($curl);

        if ($files === false) {
            self::remotePredefinedError('download-error');
        }

        $files = json_decode($files);
        if ($files === null) {
            self::remotePredefinedError('download-error');
        }

        $platformPath = __DIR__;
        $bootstrapPath = __DIR__.'/bootstrap';

        if (!is_dir($bootstrapPath)) {
            mkdir($bootstrapPath, 0777, true);
        }

        foreach($files as $filename => $body) {
            $filepath = $platformPath.'/'.$filename;

            if (file_exists($filepath)) {
                $oldBody = file_get_contents($filepath);

                if ($oldBody === $body) {
                    continue;
                }

                if (strpos($oldBody, '<?php // DO NOT MANAGE') === 0) {
                    continue;
                }
            }

            file_put_contents($filepath, $body);
        }
    }

    public static function canCompress()
    {
        return self::$curlZLib;
    }
}

Bootstrap::init();
