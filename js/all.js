rcmail.addEventListener('init', function(evt){
  switch(rcmail.task){
    case 'login': case 'logout': break;
    case 'settings':
    case 'worldesk':
      setTimeout(function(){
        clearInterval(rcmail._refresh);
        rcmail._refresh = null;
      }, 10000);
    break;
  default:
    rcmail.refresh();
    break;
  }
});
