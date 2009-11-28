<?php
interface RequestWrapper {
    function request($request_method, $url, Array $headers, $post_params);
}
?>