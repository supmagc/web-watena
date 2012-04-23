<script type="text/javascript" src="http://webplayer.unity3d.com/download_webplayer-3.x/3.0/uo/UnityObject.js"></script>
<script type="text/javascript"><!--
window.tvHash = '{[getHash()]}';
function GetUnity() {
	if (typeof unityObject != "undefined") {
		return unityObject.getObjectById("unityPlayer");
	}
	return null;
}
if(typeof unityObject != "undefined") {
	var params = {
			wmode: "opaque",
			disableContextMenu: true,
			backgroundcolor: "FFFFFF",
			bordercolor: "FFFFFF",
			textcolor: "000000",
			logoimage: "{[getUrl()]}/files/toevla/unity/loadunity.png"
	};
	unityObject.embedUnity("unityPlayer", "{[getUrl()]}/files/toevla/unity/{[getConfig('unity', 'WebPlayer.unity3d')]}", 728, 450, params);
}

function Hide() {
	video.location='{[getUrl()]}/files/toevla/video/blank.html';
	document.getElementById("youtubePlayer").style.display = 'none';
}
function Video(URL){
	document.getElementById("youtubePlayer").style.display = 'block';
	video.location=URL;
}

function requestHash(sRequestHashType) {
	if(sRequestHashType == "HIDDEN" || (sRequestHashType == "AUTOMATIC" && window.tvHash.length > 0))
		GetUnity().SendMessage('Persistent', 'InjectHash', window.tvHash);		
	else
		document.getElementById("loginLayer").style.display = 'block';
	window.tvHash = '';
}

function connectCancel() {
	hashCallback();
}
function connectPopup(sUrl) {
	if(window.tvLoginPopup) {
		window.tvLoginPopup.close();
	}
	var nLeft = (screen.width - 950) / 2;
	var nTop = (screen.height - 600) / 2;
	window.tvLoginPopup = window.open(sUrl, 'Login', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=950,height=600,left='+nLeft+',top='+nTop);
	window.tvLoginPopup.focus();
}
function connectIntoFrame() {
	if(window.tvLoginPopup) {
		document.getElementById("loginLayer").style.display = 'block';
		window.frames['loginFrame'].location = window.tvLoginPopup.location;
		window.tvLoginPopup.close();
		window.tvLoginPopup = undefined;
	}
}
function connectCallback(sHash) {
	if(sHash && sHash.length > 0) window.tvHash = sHash;
	document.getElementById("loginLayer").style.display = 'none';
	GetUnity().SendMessage('Persistent', 'InjectHash', window.tvHash);
	window.frames['loginFrame'].location = '{[getUrl()]}/login';	
	if(window.tvLoginPopup) {
		window.tvLoginPopup.close();
		window.tvLoginPopup = undefined;
	}	
}
function deezerChange(mId) {
	if(mId && (mId.length > 0 || mId > 0)) {
		document.getElementById("dezelicious").style.display = 'block';
		window.frames['dezelicious'].location = 'http://www.deezer.com/nl/plugins/player?autoplay=true&playlist=false&width=300&height=69&cover=false&type=playlist&id=' + mId;
	}
	else {
		document.getElementById("dezelicious").style.display = 'none';
		window.frames['dezelicious'].location = '{[getUrl()]}/files/toevla/video/blank.html';
	}
}
--></script>
<div id="loginLayer" style="display: none;">
	<iframe name="loginFrame" src="{[getUrl()]}/login" frameborder="0" scrolling="0"></iframe>
</div>

<div id="youtubePlayer" style="display: none;">
	<iframe width="150" height="120" name="video" src="/files/toevla/video/blank.html" frameborder="0"></iframe>
</div>

<div id="unityPlayer"></div>

