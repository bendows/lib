<?

class lib_page_captcha extends lib_page_page {

    var $captchadir = "html/captcha";

    function initialize() {
        $characters = "abcdefghijklmnopqrstuvwxyz";
        $str = '';
        for ($i = 0; $i < strlen($characters); $i++) {
            $str .= $characters[rand(0, strlen($characters) - 1)];
        }
        $_SESSION['captchac'] = substr($str, 0, 6);
        $tmpimg = $this->captchadir . "/{$_SESSION['captchac']}.gif";
        if (!file_exists($tmpimg))
            passthru(
"convert -size 140x30 xc:lightblue -font Bookman-DemiItalic -pointsize 32 -fill blue -draw \"text 10,20 '{$_SESSION['captchac']}'\" -fill blue -draw \"path 'M 5,5 L 135,5 M 5,10 L 135,10 M 5,15 L 135,15'\" -trim {$tmpimg}");
        header("Content-type: image/png");
        passthru("convert -wave 4x70 -swirl 30 {$tmpimg} -");
    }

}
?>
