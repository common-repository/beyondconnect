<?php

use inc\Beyond;

function bc_getValue(string $path, string $returnField)
{
    return Beyond::getValues($path, $returnField);
}

function bc_getUrls(string $path)
{
    return Beyond::bc_getUrls($path);
}

function bc_getUrl(string $path)
{
    return Beyond::getUrl($path);
}

function bc_getLanguage()
{
    return Beyond::getLanguage();
}
