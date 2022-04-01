<?php
namespace booosta\wysiwygeditor;

use \booosta\Framework as b;
b::init_module('wysiwygeditor');

class Wysiwygeditor extends \booosta\ui\UI
{
  use moduletrait_wysiwygeditor;

  protected $type = 'classic', $content, $ajaxurl, $language = 'en';

  public function after_instanciation()
  {
    parent::after_instanciation();

    if(is_object($this->topobj) && is_a($this->topobj, "\\booosta\\webapp\\Webapp")):
      $this->topobj->moduleinfo['wysiwygeditor']['type'] = $this->type;
      $this->topobj->moduleinfo['wysiwygeditor']['lang'] = $this->language;
      if($this->topobj->moduleinfo['jquery']['use'] == '') $this->topobj->moduleinfo['jquery']['use'] = true;;
    endif;

    $this->language = $this->config('lang') ?? $this->config('language') ?? 'en';
  }

  public function set_type($type) 
  { 
    $this->type = $type;
    $this->topobj->moduleinfo['wysiwygeditor']['type'] = $type;
  }

  public function set_content($content) { $this->content = $content; }
  public function set_ajaxurl($ajaxurl) { $this->ajaxurl = $ajaxurl; }

  public function set_language($language) { 
    $this->language = $language; 
    $this->topobj->moduleinfo['wysiwygeditor']['lang'] = $language;
  }

  public function get_htmlonly()
  {
    if($this->language != 'en' && is_object($this->topobj) && is_a($this->topobj, "\\booosta\\webapp\\webapp"))
      $this->topobj->add_javascriptfile("vendor/npm-assets/ckeditor--ckeditor5-build-classic/lang/$this->language.js");
      
    if(strtolower($this->type) == 'inline') return "<div id='wysiwygeditor_$this->id'>$this->content</div>";
    return "<textarea name='$this->id' id='wysiwygeditor_$this->id'>$this->content</textarea>";
  }

  public function get_js()
  {
    if($this->ajaxurl):
      $savefunc = "\$.ajax({ url: '$this->ajaxurl', method: 'POST', data: { content: wysiwygeditor_$this->id.getData() }});";
    else:
      $savefunc = "console.log('save data: there is no save URL specified!');";
    endif;

    if(strtolower($this->type) == 'inline'):
      $class = 'InlineEditor';
      $init = ".then(wysiwygeditor_$this->id => { wysiwygeditor_$this->id.model.document.on( 'change:data', () => { wysiwygeditor_{$this->id}_change = true; });
                wysiwygeditor_$this->id.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => 
                  { if(!isFocused && wysiwygeditor_{$this->id}_change) { $savefunc wysiwygeditor_{$this->id}_change = false; }});})";
    else:
      $class = 'ClassicEditor';
    endif;

    $toolbar = $this->config('wysiwyg_toolbar') ?? "'heading', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'fontFamily', 'insertTable', 'blockquote', 'undo', 'redo'";

    $code = "var wysiwygeditor_$this->id; var wysiwygeditor_{$this->id}_change = false; $class
             .create(document.querySelector('#wysiwygeditor_$this->id'), { language: '$this->language', toolbar: [ $toolbar ] }) $init
             .catch(error => { console.error(error); });
            ";

    if(is_object($this->topobj) && is_a($this->topobj, "\\booosta\\webapp\\webapp")):
      $this->topobj->add_jquery_ready($code);
      return '';
    else:
      return "\$(document).ready(function(){ $code });";
    endif;
  }
}
