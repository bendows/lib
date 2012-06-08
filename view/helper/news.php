<?

class lib_helper_news extends object {

    private $theview = false;

    function __construct(&$aview) {
        $this->theview = $aview;
    }

    function display_grid($colcount, $articles) {
        if (!is_array($articles))
            return;
        if (empty($articles))
            return;
        $i = 0;
        echo "<table border=0>";
        foreach ($articles as $key => $article) {
            if ($i == 0) {
                echo "<tr>";
            } else {
                if ($i % $colcount == 0)
                    echo "</tr><tr><td style='border-bottom:2px solid lightgray;' colspan=$colcount>&nbsp;</td></tr>";
            }
            echo "<td valign=top>";
            echo "<table cellpadding=0 border=0 cellspacing=0>";
            echo "<tr><td>";
            ?>
            <a href='http://<?= $_SERVER['HTTP_HOST'] . $site->dirs->path($did); ?>'><?=
            "<img onclick=\"alert (getimgsize('img" . $article['imageid'] . "'));\" id='img" . $article['imageid'] . "' src='/" . IPATH . "/" . $images[$article['imageid']]['path'] . "/$version/" . $images[$article['imageid']]['filename'] . "'></a></td></tr>";
            echo "<tr><td><div style='width:160px; margin:0; padding:0;'><b><a href=# style='margin:0; padding:0;'>" . $article['name'] . "</a><br>R&nbsp;" . $article['price'] . "</b></div></td></tr>";
            echo "</table></td>";
            //echo "<label for='checker$checkboxname$key'><img src='".$page['path']."/".$page['filename']."'></label></td>";
            $i++;
        }
        $emptytds = $colcount - (int) ($i % $colcount);
        if ((int) ($i % $colcount) > 0) {
            echo "<td colspan=$emptytds>&nbsp;&nbsp;</td>";
        }
        echo "</tr>";
        echo "</table>";
    }

}
?>
