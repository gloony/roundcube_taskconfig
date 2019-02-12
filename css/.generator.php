<?php
  if(isset($_GET['task'])){
    $task = $_GET['task'];
    $hue = -1; $saturation = -1; $light = -1;
    if(isset($_GET['hue'])) $hue = $_GET['hue'];
    if(isset($_GET['saturation'])) $saturation = $_GET['saturation'];
    if(isset($_GET['light'])) $light = $_GET['light'];
    GenerateFromTemplates($task, $hue, $saturation, $light);
    echo 'Generation done';
  }else{
    if(file_exists(__DIR__.'/.generator.ini')){
      $tasks = file(__DIR__.'/.generator.ini', FILE_IGNORE_NEW_LINES);
      foreach($tasks as $line){
        $hue = -1; $saturation = -1; $light = -1;
        $t = $line;
        $task = substr($t, 0, strpos($t, ' = '));
        $t = substr($t, strlen($task) + 3);
        $commaP = strpos($t, ', ');
        if($commaP!==false){
          $hue = substr($t, 0, strpos($t, ', '));
          $t = substr($t, strlen($hue) + 2);
          $commaP = strpos($t, ', ');
          if($commaP!==false){
            $saturation = substr($t, 0, strpos($t, ', '));
            $t = substr($t, strlen($saturation) + 2);
            $light = $t;
          }else $saturation = $t;
        }else $hue = $t;
        GenerateFromTemplates($task, $hue, $saturation, $light);
      }
      echo 'Generations done';
    }else echo 'init file doesn\'t exist';
  }

  function GenerateFromTemplates($task, $hue, $saturation, $light){
    $lines = file(__DIR__.'/.template.css', FILE_IGNORE_NEW_LINES);
    $output = eachLineConvert($lines, $hue, $saturation, $light);
    if(file_exists(__DIR__.'/.tmp.'.$task.'.css')){
      $output .= "\n\n";
      $lines = file(__DIR__.'/.tmp.'.$task.'.css', FILE_IGNORE_NEW_LINES);
      $output .= eachLineConvert($lines, $hue, $saturation, $light);
    }
    file_put_contents(__DIR__.'/'.$task.'.css', $output);
  }
  function eachLineConvert($lines, $hue, $saturation, $light){
    $output = '';
    foreach($lines as $line){
      $hslaP = strpos($line, 'hsla(');
      if($hslaP!==false){
        $t = $line;
        $h = substr($t, $hslaP);
        $h = substr($h, 5, strpos($h, ', ') - 5);
        $t = substr($t, $hslaP + 5 + strlen($h) + 2);
        $s = substr($t, 0, strpos($t, '%, '));
        $t = substr($t, strlen($s) + 3);
        $l = substr($t, 0, strpos($t, '%, '));
        $t = substr($t, strlen($l) + 3);
        $a = substr($t, 0, strpos($t, ')'));
        if($hue!=-1) $hv = $hue;
        else $hv = $s;
        if($saturation!=-1) $sv = $saturation;
        else $sv = $s;
        if($light!=-1) $lv = $light;
        else $lv = $l;
        if($a==1) $line = str_replace('hsla('.$h.', '.$s.'%, '.$l.'%, '.$a.')', '#'.hslToHex(array($hv / 360, $sv / 100, $lv / 100)), $line);
        else{
          $rgb = hslToRGB(array($hv / 360, $sv / 100, $lv / 100));
          $line = str_replace('hsla('.$h.', '.$s.'%, '.$l.'%, '.$a.')', 'rgba('.$rgb['r'].', '.$rgb['g'].', '.$rgb['b'].', '.$a.')', $line);
        }
      }
      if($output!='') $output .= "\n";
      $output .= $line;
    }
    return $output;
  }

  function hslToHex($hsl){
    list($h, $s, $l) = $hsl;
    // var_dump($hsl);
    if ($s == 0) {
      $r = $g = $b = 1;
    } else {
      $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
      $p = 2 * $l - $q;
      $r = hue2rgb($p, $q, $h + 1/3);
      $g = hue2rgb($p, $q, $h);
      $b = hue2rgb($p, $q, $h - 1/3);
    }
    return rgb2hex($r) . rgb2hex($g) . rgb2hex($b);
  }

  function hslToRGB($hsl){
    list($h, $s, $l) = $hsl;
    if ($s == 0) {
      $r = $g = $b = 1;
    } else {
      $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
      $p = 2 * $l - $q;
      $r = hue2rgb($p, $q, $h + 1/3);
      $g = hue2rgb($p, $q, $h);
      $b = hue2rgb($p, $q, $h - 1/3);
    }
    return array('r' => intval($r * 256), 'g' => intval($g * 256), 'b' => intval($b * 256));
  }
  function hue2rgb($p, $q, $t) {
    // echo $t."\n";
    if ($t < 0) $t += 1;
    if ($t > 1) $t -= 1;
    if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
    if ($t < 1/2) return $q;
    if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
    return $p;
  }
  function rgb2hex($rgb) {
    return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
  }