<?php

namespace Translation;

class Token
{

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $comment;

    /**
     * @param string $token
     * @param string $comment
     */
    public function __construct(string $token, string $comment = '')
    {
        $this->token   = $token;
        $this->comment = $comment;
    }
}
