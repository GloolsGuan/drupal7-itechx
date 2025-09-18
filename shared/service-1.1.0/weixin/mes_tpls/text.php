<xml>
<ToUserName><![CDATA[<?=$receiver['FromUserName'];?>]]></ToUserName>
<FromUserName><![CDATA[<?=$receiver['ToUserName'];?>]]></FromUserName>
<CreateTime><?php print time();?></CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[<?=$content;?>]]></Content>
</xml>