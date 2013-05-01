<?php

  // This page is the "super page" that loads the IFRAME that contains the actual
  // screen information.

  $callurl = base_url() . 'index.php/screen/inner/'   . $id;  // The url to call for prediction updates
  $pollurl = base_url() . 'index.php/update/version/' . $id;  // The url to call to check whether the screen needs
                                                              // needs to be refreshed.
?><html>
  <head>
    <title>Transit Screen</title>
    <meta name="robots" content="none">
    <link rel="shortcut icon" href="<?php print base_url(); ?>/public/images/favicon.ico" />
    <link rel="apple-touch-icon" href="<?php print base_url(); ?>/public/images/CPlogo.png" />    
    <script type="text/javascript" src="<?php print base_url(); ?>/public/scripts/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="<?php print base_url(); ?>/public/scripts/jquery.timers-1.2.js"></script>
    
    <script type="text/javascript">
      var now = Math.round(new Date().getTime() / 1000);  
      var latestv = ''; 
      var frameclass = '';
      
      $(document).ready(function(){
        //Call the update function
        get_update();
      });
      
      // Poll the server to find the latest version number
      $(document).everyTime(60000, function(){
        get_update();      
      });
      
      function get_update() {
        // Poll the server for the latest version number
        
        $.getJSON('<?php print $pollurl; ?>',function(versionval){
          // If that version number differs from the current version number,
          // create a new hidden iframe and append it to the body.  ID = version num
          if(versionval != latestv){
            
            //If the element already exists, remove it and replace it with a new version
            if($('#frame-' + versionval).length > 0){
              $('#frame-' + versionval).remove();  
            }
            
            $('<iframe />', {
              id:     'frame-' + versionval,

              src:    '<?php print $callurl; ?>?' + now
            }).appendTo('body');

            if(frameclass.length > 0)
            {
                $('#frame-' + versionval).show(); 
            }
            if (frameclass== 'hidden')
            {
                $('#frame-' + versionval).hide(); 
            }

            frameclass = 'hidden';    
          
            // Wait 20 seconds and call another function to check the status of the new iframe
            setTimeout('switch_frames("' + versionval + '");',20000);
          }         
          
        })
        .error(function() {
            
        }); 
      }
      
      function switch_frames(ver) {        
        var newname = '#frame-' + ver;
        
        //console.log('blocks in ' + newname + ': ' + $(newname).contents().find('.block').length);
        
        // If the new iframe has populated with .blocks, remove the old iframe
        // and show the new one                
        if($(newname).contents().find('.block').length > 0) {
          // For each iframe, if the id doesn't equal newname, remove it
          $.each($('iframe'), function(i, frame) {
            //console.log('frame.id = ' + frame.id + '; compare to: frame' + ver);
            if(frame.id != 'frame-' + ver){
              $('#' + frame.id).remove();
            }
            
            // And show the new iframe by removing the .hidden class
            $(newname).attr('class', '');            
            // Set the latest version variable to the new version
            latestv = ver;
            
            //console.log(frame.id);
          });
          
        }      
        // Else, remove the new, hidden iframe
        else {
          $(newname).remove();
          //console.log('Removed ' + newname);
        }
      }
      
      
      
    </script>
    
    <style type="text/css">
      body {
        margin: 0;
        background-color: #000;
      }
      iframe {
        border: 0;
        width: 100%;
        height: 100%;
      }
      .hidden {
        display: none;
      }
    </style>
  
  </head>
  
  <body>
    <noscript>
    <div id="noscript-padding"></div>
    <div id="noscript-warning" style="color:red">Transit Screen requires a JavaScript-enabled browser. <a href="https://www.google.com/support/adsense/bin/answer.py?answer=12654" target="_blank">Not sure how to enable it?</a></div>
    </noscript>
    <script type="text/javascript">
      if( navigator.userAgent.match(/Mobile/i) &&
          navigator.userAgent.match(/Safari/i)
        ) {
             document.title = "Transit Screen";
          }
    </script>    
  </body>
</html>