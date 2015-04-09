<?php

echo <<<EOT
{$data['username']} has mentioned you in a 
<a href="{$data['site']['url']}/{$data['app']['url']}/{$data['module']['url']}/{$data['topic']['url']}{$data['page']}#post-{$data['postId']}">forum post.</a>
EOT;
