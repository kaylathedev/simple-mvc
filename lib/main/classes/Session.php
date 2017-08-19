<?php

class Session
{
    private static $started;
    private static $messageLayoutFilename;
    private static $messageLayout;

    public static function has($key)
    {
        Session::initalize();

        return isset($_SESSION[$key]);
    }

    public static function setMessageLayoutFilename($filename)
    {
        Session::$messageLayoutFilename = $filename;
    }

    public static function remove($key)
    {
        Session::initalize();
        unset($_SESSION[$key]);
    }

    public static function set($key, $value)
    {
        Session::initalize();
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        Session::initalize();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;
    }

    public static function initalize()
    {
        if (!Session::$started) {
            Session::$started = true;
            session_start();
        }
    }

    public static function setFlash($message, $key = 'info', $timeout = 0)
    {
        $key = 'message.' . $key;
        Session::set($key, array('content' => $message, 'timeout' => $timeout));
    }

    public static function flash($type = 'info')
    {
        $key = 'message.' . $type;
        $data = Session::get($key);
        Session::remove($key);
        if ($data !== null) {
            $content = $data['content'];
            $timeout = $data['timeout'];
            if (Session::$messageLayoutFilename !== null) {
                ob_start();
                include Session::$messageLayoutFilename;
                $rawOutput = ob_get_contents();
                ob_end_clean();

                return $rawOutput;
            }
            if (Session::$messageLayout !== null) {
                return sprintf(Session::$messageLayout, $type, $timeout, $content);
            }

            return Session::getDefaultFlashLayout($type, $timeout, $content);
        }

        return null;
    }

    public static function isLoggedIn()
    {
        Session::initalize();

        return isset($_SESSION['_user']);
    }

    public static function getCurrentUser($key = null)
    {
        Session::initalize();
        if (isset($_SESSION['_user'])) {
            $data = $_SESSION['_user'];
            if ($key === null) {
                return $_SESSION['_user'];
            } else {
                return $_SESSION['_user'][$key];
            }
        }

        return null;
    }

    public static function setCurrentUser($data)
    {
        Session::initalize();
        $_SESSION['_user'] = $data;
    }

    public static function logout()
    {
        Session::initalize();
        $wasSet = isset($_SESSION['_user']);
        unset($_SESSION['_user']);

        return $wasSet;
    }

    private static function getDefaultFlashLayout($type, $timeout, $content)
    {
        return '<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script><style type="text/css">
.icon{display:inline-block;float:left;height:16px;margin:0.15em 0px;width:16px}
.msg{border:1px solid;border-radius:3px;color:#222;padding:9px 12px}
.msg-close{color:#333 !important;display:inline-block;float:right;font-family:sans-serif;font-size:1.1em;font-weight:700;text-decoration:none}
.msg-content{display:inline-block;float:left;padding:0 9px}
.msg-error{background:#fff0ef;border-color:#c42608;color:#c00}
.msg-error .icon{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAhFBMVEUAAAD/dHf9b3LiHR/yS031VFfnLC7rNjj4XWD7Z2riHB7uQUTwR0rzUVT3W13/cXPwRUf7a27/eXz/d3n5YGP2VVj4XmDtPT/6Y2b3W13qLjD/foH0TU/2WFvvQEL+dHbqMzX/ZGX/e37/hYj/bW//WVz/VVj2SkzkIiT0SErmKiz5aGrQirOsAAAAD3RSTlMAHytBQCorCCsIQQVvqm8MTAOtAAAAk0lEQVQY023MSRKDIBRFUWNUTIt0AoJgn27/+4sfSSWDMPl1bhUv+X2HeHfpdstrtpmxNJi/LnmwMTgFk4Gf4SOmIyVlWRNaV6cwRfzoeTCK44qYgTINjkXTefbRYV+zZcFV8bXCeJqEjCVvFW6dtL3tio+FQ6ir+kcDJRNcyHUPPd2t2SdQLHgtd3AoYCjH5M97AzuMChdFwVz6AAAAAElFTkSuQmCC")}
.msg-info{background:#dfebfb;border-color:#82aee7;color:#064393}
.msg-info .icon{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAABLFBMVEUAAAA7bahtlcZqksO10PG91vWyze4+b6o6bKijweVGda5fib14nsyEp9Ojweazzu+OsNmRs9tNe7JXg7g6bKd4nsyDp9JRfrVOfLPE2/msyOu91vXA2Pe50/PC2vg3aaU/cKplkL/a5fP2+PxrmcZql8Xj7PVolMK7zeJfibd/p9NslcHG1+t7pdHW4/Cau97d6Pa/0+Ziirm4zONxns2/0eTT4e92oNCfveFkirZijLvJ2OnP3e/p7/aGp8qivNjL2+q61POUsM+ErNfR4Ow7bKiVtdny9vpxmcePsNnU4/SqwNpAcKthh7Jbh7umvdnD1erC1OqRtNbu8/hhjL9xm8Xs8ffE1ee6zuactdOAocXf6ve0y+SJrtjW5PXc6fmnweOuyOS+1/ZnjripOlM9AAAAIXRSTlMAfSLyIlN9IlNT8lN+5+fyIvJ+5/TnflP0I/T05+d95+e1NKfaAAAAyUlEQVQY023KRZYCQRREUeimod1xzaSy3JDC3d3dZf974B9kxpvFPaG7m93jOuTdRsNtPzm3KsbqxvFy3fllarcuKiKOncXwpYpFhab30kz++QYwNlJ81A/xUq9vAfjEsWiYXgX8ATKImAEUzIcpEhqms8xYJgB1LFGEVGkBIUEeAXxEKhTDIJQIBjO5P4D3kFhDaJLmuDjb/QV4eEyyQqJQ4NpN7dWkg946Uy1zjC9YrfUME7LasrlSKTn/h33JpPeVy149/O90AoRiHyEkOYBcAAAAAElFTkSuQmCC")}
.msg-success{background:#e0fbcc;border-color:#6dc70c;color:#2b6301}
.msg-success .icon{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAY1BMVEUAAABap1E/hDhXok5Nl0Vpul5VoUw2ejBOl0ZTnkpqul9mtlxmtVtnt1xRnEmEynlaolJss2KKzn46gDRgpldpr1+O0IKBynY4fDI+gzeHzXx3v2xaplFlrVtHjkBjsFlRmkmacPQFAAAAD3RSTlMAb3uRfESRBwmRPPTxZYsbekVaAAAAY0lEQVQY05XJVw6AIBBFUXvXEaRIU/e/SslAQohfvq85d4p/q5vcHeFNbgGsSh65oEz1/mrXYBo9zaZEa3bgfwFhy81p2NHFcBsBhIBEY7GEahkcy+O8T3Qs1x6ciopOJfmzF9bRBe4aYgy4AAAAAElFTkSuQmCC")}
.msg-warning{background:#fffcd3;border-color:#ebcd41}
.msg-warning .icon{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAA8FBMVEUAAADouDnotjjZmSTmsjTotTfbnCbgpi3otjflsDPmtDbsvz3eoyvntTfotzjrvDzrvDzjrDHpuTrmszbUjh3mszbNgBPJeQ7lsDTDbQbjrTHiqi/BaAO/ZADWoj3+/O/+/PP02lzz11f13F356JT++uf12lj+++vRiBj110///vnx04T++u3puTn888X89uHx16D89tb147/y1lT12VP23WPeoyrIdAvCagTbnSbYliHlu2jtyHH36sjdsUf679XhqS/y2JT036nw04/6657nwH3w0ozry1f45Yvlv4j677Xov2Lgpy3Sk0TMfhHrzquByH+SAAAAHnRSTlMAA1rx/s5aDBIw+wPKad6V3pa4rIckJLhLS/Cj46PpUlYpAAAAsElEQVQY03XP1Q7CQBBA0cXd3bbbrcMidcHd4f//hgIBSgjzMMk9yTwM+DOJbMj3BZlOJ+XtaJeaBqMeyM8Rt8x9OtZtsysyi70hvSAQyoPkq/2jNgMhTYbhZ0fiY46GEPeUQOQBhY3A0hOImf66eO9SEPUYWt+qGtsXyy6EBtyOd09UTZL3FQCqR3TgeYwxL0m6cK6BuoIoQijKXURA1yZonAzDth1HFC+maVmtn79vaXwWBrYtRuMAAAAASUVORK5CYII=")}
</style>
<script type="text/javascript">var t=' . $timeout . ';if (!h) {var h=function (e) {var s=e.style;if (window.jQuery) {s.display="block";$(e).slideUp();} else {s.display="none";}}};if (t>0) {setTimeout(function () {h(document.querySelector(".msg:last-child"));},t)}</script>
<div class="msg msg-' . $type . '">
<span class="icon"></span><span class="msg-content">' . $content . '</span><a href="#" onclick="h(this.parentNode);return false;" class="msg-close">x</a><div style="clear:both"></div></div>';
    }

}
