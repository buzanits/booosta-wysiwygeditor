<?php
namespace booosta\wysiwygeditor;

\booosta\Framework::add_module_trait('webapp', 'wysiwygeditor\webapp');

trait webapp
{
  protected function preparse_wysiwygeditor()
  {
    if($version = $this->moduleinfo['wysiwygeditor']):
      $path = 'vendor/npm-asset/ckeditor--ckeditor5-build-classic';
      if($version === 'inline') $path = 'vendor/npm-asset/ckeditor--ckeditor5-build-inline';

      $this->add_includes("<script type='text/javascript' src='{$this->base_dir}{$path}/ckeditor.js'></script>");

      if($this->language && $this->language != 'en') $this->add_javascriptfile("$path/lang/$this->language.js");;
    endif;
  }
}
