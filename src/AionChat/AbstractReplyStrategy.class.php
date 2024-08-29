<?php

namespace AionChat;

abstract class AbstractReplyStrategy{

    /**
     * Handle the cUrl response based on a strategy
     *
     * @param string $cUrl_response the cUrl response in string format
     * @return string status
     */
    public abstract function do_handle_cUrl_response(string $cUrl_response);

}