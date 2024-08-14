<?php

require_once './vendor/autoload.php';

use LightnCandy\LightnCandy;
use LightnCandy\Flags;

$partials = file_get_contents("web/partials/partials.hbs");
$template = file_get_contents("web/examples/index.hbs");
$c = glob("web/**/*.hbs");


$total = $partials."\n".$template;

$render = createRenderer($total);
file_put_contents("dist/examples/index.html", $render([]));

function createRenderer($template) {
  $phpStr = LightnCandy::compile($template, [
    'flags' => Flags::FLAG_ERROR_LOG|Flags::FLAG_RUNTIMEPARTIAL,
  ]);
  
  if(!is_dir("tmp/"))
    mkdir("tmp/");
  file_put_contents('tmp/render.php', "<?php $phpStr");
  
  return include('tmp/render.php');
}


