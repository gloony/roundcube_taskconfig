<?php
class taskconfig extends rcube_plugin
{
  public $task = '.*';

  private $rcmail;

  public function init()
  {
    $this->rcmail = rcube::get_instance();
    $this->load_config();

    $this->add_hook('render_page', array($this, 'render_page'));

    if(file_exists(__DIR__.'/css/'.$this->rcmail->task.'.css')){
      $this->include_stylesheet('css/'.$this->rcmail->task.'.css');
    }
    if(file_exists(__DIR__.'/css/all.css')){
      $this->include_stylesheet('css/all.css');
    }
    if(file_exists(__DIR__.'/js/'.$this->rcmail->task.'.js')){
      $this->include_script('js/'.$this->rcmail->task.'.js');
    }
    if(file_exists(__DIR__.'/js/all.js')){
      $this->include_script('js/all.js');
    }
  }

  public function render_page($args)
  {
    if(file_exists(__DIR__.'/favicon/'.$this->rcmail->task.'.ico')){
      $hrefIcns = 'plugins/taskconfig/favicon/'.$this->rcmail->task.'.ico';
      $favicon = !empty($hrefIcns) ? $favicon = html::tag('link', array('rel' => 'shortcut icon', 'href' => $hrefIcns)) : '';
      $args['content'] = preg_replace('!<link\s[^>]*rel="shortcut icon"[^>]*>!', $favicon, $args['content'], -1, $count);
      if (!$count && $favicon) $args['content'] = preg_replace('!(</head>)!', $favicon . "\n\\1", $args['content']);
    }
    return $args;
  }
}
