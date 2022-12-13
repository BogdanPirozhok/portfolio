<?php

function mb_title_format($string): string
{
    return mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1);
}
