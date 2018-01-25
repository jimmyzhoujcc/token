<?php
require_once 'DingAPI.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>钉钉测试</title>
	<script type="text/javascript" src="<?php echo $init['apiJs'];?>"></script>
	<script type="text/javascript">
		 DingTalkPC.config(<?php echo $init['apiConfig'];?>);
		 DingTalkPC.ready(function() {

			 DingTalkPC.runtime.permission.requestAuthCode({                         //获取code码值  
			        corpId :"<?php echo $init['corpid'];?>",  
			        onSuccess : function(info) {  

			            DingTalkPC.device.notification.alert({
			            message: info.code,
			            title: info.code,
			            buttonName: "收到",
			            onSuccess : function() {
			                /*回调*/
			            },
			            onFail : function(err) {}
			            }); 
			            
			       
			        },  
			        onFail : function(err) {  
			            alert(JSON.stringify(err));  
			        }  
			    }); 

			 });

	</script>
</head>
<body>

</body>
</html>