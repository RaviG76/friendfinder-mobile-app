<!DOCTYPE html>
    <head>
        <title>
            ajax form data submiting
        </title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
			$('#send').click(function(e){
				var name = $('#uname').val();
				var password = $('#password').val();
				var phone = $("#phone").val(); 
				var location = $('#loca').val();
				var email = $('#email').val();
				var values = {"uname":name,"password":password,"phone":phone,"location":location,"email":email} ;
				$.ajax({
					url:'/mobileApi/send',
					method:'POST',
					data:values,
					success:function(response){
						$('#uname').val('');
						$('#password').val('');
						$("#phone").val(''); 
						$('#loca').val('');
						$('#email').val('');
						$("#result").html(response); 
												
					}
				      });
				e.preventDefault();
			});

			$('#send1').click(function(e){
				var name = $('#uname1').val();
				var password = $('#password1').val();
				var values = {"uname":name,"password":password} ;
				$.ajax({
					url:'/bojio/login',
					method:'POST',
					data:values,
					success:function(response){
						$('#uname').val('');
						$('#password').val('');
						$("#result1").html(response); 
					}
				      });
				e.preventDefault();
			});
		});
        </script>  
      
    </head>
    
    <body>
        <center>
        <form id="myFrm" action="" method="post" >
            userName:<input type="text"  name='uname' id='uname'><br/>
            userEmail:<input type="text"  name='uname' id='email'><br/> 
	  PhoneNumber:<input type="text" name="phone" id="phone" /><br/>
	  Password:<input type="password"  name='password' id='password'></br>
	  Location:<input type="text" name="location" id="loca" />
        </form>
	<button id="send" class="1save" >Ajax save</button>            
        <div id="result"></div>
        </center>
	<hr>
	        <center> Login
        <form id="myFrm" action="ajax.php" method="post" >
            userName:<input type="text"  name='uname' id='uname1'><br/>
	  Password:<input type="password"  name='password' id='password1'></br>
        </form>
	<button id="send1" class="1save" >Ajax save</button>            
        <div id="result1"></div>
        </center>
        
        
    </body>
    </html>

