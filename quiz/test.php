<?php
require_once("./header.php");
print_r($_SESSION['user']);

?>
<script>
    function createWindow(src, width, height) {
        var win = window.open(src, "_new", "width=" + width + ",height=" + height);
        win.addEventListener("resize", function() {
            console.log("Resized");
            win.resizeTo(width, height);
        });
    }

    createWindow("about:blank", 500, 300);
</script>