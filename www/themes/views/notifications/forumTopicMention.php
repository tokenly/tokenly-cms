<?php

echo <<<EOT
{$data['username']} has mentioned you in a 
<a href="{$data['site']['url']}/{$data['app']['url']}/post/{$data['url']}">forum thread.</a>
EOT;
