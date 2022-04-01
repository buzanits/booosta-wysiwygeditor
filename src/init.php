<?php
namespace booosta\wysiwygeditor;

\booosta\Framework::add_module_trait('webapp', 'wysiwygeditor\webapp');

trait webapp
{
  protected function preparse_wysiwygeditor()
  {
    if($type = $this->moduleinfo['wysiwygeditor']['type']):
      $path = 'vendor/npm-asset/ckeditor--ckeditor5-build-classic/build';
      if($type === 'inline') $path = 'vendor/npm-asset/ckeditor--ckeditor5-build-inline/build';

      $this->add_includes("<script type='text/javascript' src='{$this->base_dir}{$path}/ckeditor.js'></script>");

      if($this->moduleinfo['wysiwygeditor']['lang'] && $this->moduleinfo['wysiwygeditor']['lang'] != 'en'):
        $lang = $this->moduleinfo['wysiwygeditor']['lang'];
        $this->add_javascriptfile("$path/translations/$lang.js");
      elseif($this->language && $this->language != 'en'):
        $this->add_javascriptfile("$path/translations/$this->language.js");
      endif;
    endif;
  }
}
