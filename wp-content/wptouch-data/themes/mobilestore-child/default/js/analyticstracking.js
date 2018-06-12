  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  var user_id = atts.user_id, ga_id = atts.ga_id;
      
  console.log(user_id)
  console.log(ga_id)
  
  ga('create', ga_id, 'auto', {
      userId: user_id
  });
//  ga('set', 'userId', user.id); // Legen Sie die User ID mithilfe des Parameters "user_id" des angemeldeten Nutzers fest.
  ga('send', 'pageview');