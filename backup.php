<!DOCTYPE html>

<!--
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY 
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF 
CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.
//-->

<html>

	<head>
	
		 <meta http-equiv="Content-type" content="text/plain;charset=UTF-8">
	
		 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		
		 <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
		
		 <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans">
		 
		 <script language="JavaScript" type="text/javascript" src="jsbn.js"></script>
		 <script language="JavaScript" type="text/javascript" src="base64.js"></script>
		 <script language="JavaScript" type="text/javascript" src="base64x-1.1.js"></script>
		 <script language="JavaScript" type="text/javascript" src="Base64Decode.js"></script>
	
		<script type="text/javascript">
		
			 var CLIENT_ID = 'your-google-client-id';
			 var SCOPES = ['https://mail.google.com/','https://www.googleapis.com/auth/gmail.modify','https://www.googleapis.com/auth/gmail.readonly', 
						   'https://www.googleapis.com/auth/gmail.compose', 'https://www.googleapis.com/auth/gmail.labels'];
						   
			 var USER = 'me';
			 var glabel;
			 var labelnames = [];
			 var GAPI;
			 var accessToken;
			 var messages;
			 var messageindex;
			 var date;
			 var subj;
			 var convert  = b64toutf8;
			 var email;
			 var totalmsg;
			 var woattach;
			 var es = "";
			 var nextpage;
			 var messageid;
			 var qdate = ["after: ", "before: "];
			 var qtext = ["from: ", "to: "];
			 var dmsg;
			 var cpage = 1;
			 var charLimit = 19; // 0 to 19 -> 20 characters
			 var foldername;
			 
			  /**
			   * Called when the client library is loaded to start the auth flow.
			   */
			  function handleClientLoad() {
				window.setTimeout(checkAuth, 1);
			  }

			  /**
			   * Check if the current user has authorized the application.
			   */
			  function checkAuth() {
				GAPI = gapi.auth.authorize(
					{'client_id': CLIENT_ID, 'scope': SCOPES, 'immediate': true},
					handleAuthResult);
			  }
			  
			 function handleAuthResult(authResult) 
			 {
				var authButton = document.getElementById('authorizeButton');
				var outputNotice = document.getElementById('notice');
				authButton.style.display = 'none';
				outputNotice.style.display = 'none';
				if (authResult && !authResult.error) {
				  // Access token has been successfully retrieved, requests can be sent to the API.
				  gapi.client.load('gmail', 'v1', function() {
						document.getElementById("app").style.display = "block";
						accessToken = GAPI.rW.access_token;
						listlabels(USER);
				  });
				} else {
				  // No access token could be retrieved, show the button to start the authorization flow.
				  authButton.style.display = 'block';
				  outputNotice.style.display = 'none';
				  authButton.onclick = function() {
					  GAPI = gapi.auth.authorize(
						  {'client_id': CLIENT_ID, 'scope': SCOPES, 'immediate': false},
						  handleAuthResult);
				  };
				}
			 }
			 
			 function listlabels(userid)
			 {
				 var request = gapi.client.gmail.users.labels.list({
					  'userId': userid
					});
				 request.execute(function (resp){
					 var labels = resp.labels;
					 for (i=0; i < labels.length; i++)
					 {
						 var label = labels[i];
						 labelnames.push(label.name);
					 }
					 for (i=0; i < labelnames.length; i++)
					 {
						 var createSelectOption = document.createElement("option");
						 createSelectOption.setAttribute("value", i);
						 var textnode = document.createTextNode(labelnames.sort()[i]);
						 createSelectOption.appendChild(textnode);
						 document.getElementById('labelsel').appendChild(createSelectOption);
					 }
				 });
			 }
			 
			 function BackUp ()
			 {
				 console.clear();
				 var pgno = document.getElementById('continueid').value;
				 if (pgno)
				 {
					 if ($.isNumeric(Number(pgno)))
					 {
						 cpage = Number(pgno);
					 } else
					 {
						 alert("Please input an integer.");
						 $('#continueid').focus();
						 return
					 }
				 }
				 $("p").empty();
				 glabel = $('#labelsel option:selected').text();
				 if (glabel == "Select..")
				 {
					 var query = "";
				 } else 
				 {
					var query = "in:" + glabel + " "; 
				 }
				 var afterdate = document.getElementById("afterdate").value;
				 var beforedate = document.getElementById("beforedate").value;
				 for (i=0; i < $("input[type=date]").length; i++)
				 {
					 if ($.trim($("input[type=date]")[i].value).length)
					 {
						 //console.log(($("input[type=date]")[i].id));
						 query += qdate[i] + $("input[type=date]")[i].value + " ";
					 }
				 }		 
				 for (j=0; j < $("input[type=text]").length-3; j++)
				 {
					 if ($.trim($("input[type=text]")[j].value).length)
					 {
						 //console.log(($("input[type=text]")[j].id));
						 query += qtext[j] + $("input[type=text]")[j].value + " ";
					 }
				 }
				 var subject = document.getElementById('subjectid').value;
				 if (subject != "")
				 {
					 query += "subject: (" + subject + ")";
				 }
				 if (nextpage)
				 {
					 var request2 = gapi.client.gmail.users.messages.list({
					  'userId': USER,
					  'q': query,
					  'pageToken': nextpage
					 });
				 } else
				 {
					 var request2 = gapi.client.gmail.users.messages.list({
					  'userId': USER,
					  'q': query
					});
				 }
				 request2.execute(function (resp2){
					 nextpage = resp2.nextPageToken; // if not assigned, then undefined!
					 document.getElementById("pgmsg").innerHTML = "Saving page " + cpage + ".";
					 messages = resp2.messages;
					 if (messages)
					 {
						 document.getElementById("totalthreads").innerHTML = "Total messages: " + (messages.length) + "\n";
						 totalmsg = 1;
						 woattach = 0;
						 GetMessages(0, messages);
					 } else
					 {
						 document.getElementById("msg").innerHTML = "No messages found. Choose another label.";
					 }
				 });
			 }
			 		 
			 function GetMessages(messageindex, messages)
			 {
				 messageindex = messageindex || 0;
				 if (messageindex >= messages.length) 
				 {
					 document.getElementById("msg").innerHTML = "Saving complete.";
					 if (nextpage) 
					 {
						 cpage++;
						 $("#continueid").val('');
						 BackUp();
					 }
					 return
				 }
				 var message = messages[messageindex];
				 messageid = message.id;
				 var request3 = gapi.client.gmail.users.messages.get({
						  'userId': USER,
						  'id': messageid
				  });
				 request3.execute(function (msg1){
					  var headers = msg1.payload.headers;
					  var tdate = new Date(getHeader(headers, 'Date'));
					  subj = getHeader(headers, 'Subject').replace(/([\[\(] *)?(RE|FWD?) *([-:;)\]][ :;\])-]*|$)|\]+ *$/igm,"");
					  fileName = GetFileName(tdate, subj, messageindex);
					  console.log(messageindex + "-" + fileName);
					  $.ajax
						 ({
							 type: 'get',
							 timeout: 30000,
							 url: 'https://www.googleapis.com/gmail/v1/users/' + USER + '/messages/' + messageid + '?format=raw&access_token=' + accessToken,
							 success: function(result) 
								{
								   try
								   {
									   email = convert(result.raw);
								   }
								   catch (err)
								   {
									   email = Base64Decode(result.raw);
								   }
								   SaveGMail(email, messageindex);
								},
							 error: function (x, textstatus, m)
							 {
								 if (textstatus == "timeout")
								 {
									 GetWithOAttach(messageid, messageindex);
								 }
							 }
						 });
				 });
			 }
			 
			 function getHeader(headers, index) 
			  {
				var header = '';
				$.each(headers, function()
				{
				  if(this.name === index)
				  {
					header = this.value;
				  }
				});
				return header;
			  }
			 
			 function GetFileName(tdate, subj)
			 {
				 gM = tdate.getMonth() + 1;
				 if (gM < 10) {gM = "0" + gM;} else {gM = gM.toString();}
				 gD = tdate.getDate();
				 if (gD < 10) {gD = "0" + gD;} else {gD = gD.toString();}
				 gH = tdate.getHours();
				 if (gH < 10) {gH = "0" + gH;} else {gH = gH.toString();}
				 gMn = tdate.getMinutes();
				 if (gMn < 10) {gMn = "0" + gMn;} else {gMn = gMn.toString();}
				 gS = tdate.getSeconds();
				 if (gS < 10) {gS = "0" + gS;} else {gS = gS.toString();}
				 gY = tdate.getFullYear().toString();
				 date = gY + gM + gD + "_" + gH + gMn + gS;
				 var dplus = charLimit;
				 var modsubj = subj.replace(/[^a-zA-Z0-9]/gi, ' ').replace(/\s\s+/g, ' ');
				 var cA = modsubj.charAt(dplus);
				 while (cA != " ")
				 {
					 if (dplus == 50) {break}
					 dplus++;
					 cA = modsubj.charAt(dplus);
				 }
				 fiN = date + "_" + modsubj.substring(0, dplus).trim();
				 return fiN
			 }
			  
			  function SaveGMail (email, messageindex)
			  {
				  var dir = glabel.replace(/[^a-zA-Z0-9_\/s]/gi, ' ').trim();
				  foldername = document.getElementById("folderid").value;
				  if ($.trim(foldername).length)
				  {
					  dir += "/" + foldername.replace(/[^a-zA-Z0-9_\/s]/gi, ' ').trim();
				  }
				  $.ajax
				 ({
					 type: 'post',
					 url: 'save_gmail.php',
					 data: 
						{
							 glabelPHP    : dir,
							 fileNamePHP  : fileName,
							 emailPHP     : email,
							 totalmsgPHP  : totalmsg,
							 pagePHP      : cpage
						},
					 success: function(msg) 
						{
						  document.getElementById("tmsg").innerHTML = msg;
						  totalmsg++;
						  messageindex++;
						  GetMessages(messageindex, messages);
						}
				 });
			  }
			  
			  function GetWithOAttach(messageid, messageindex)
			  {
				  $.ajax
					 ({
						 type: 'get',
						 url: 'https://www.googleapis.com/gmail/v1/users/' + USER + '/messages/' + messageid + '?format=full&access_token=' + accessToken,
						 success: function(msg) 
							{
							   dmsg = msg;
							   var msgpayload = msg.payload;
							   var messagedetails = FormEmail(msgpayload);
							   SaveFullGMail(messagedetails, messageindex);
							}
					 });
			  }
			  
			  function SaveFullGMail(messagedetails, messageindex)
			  {
				  woattach++;
				  var dir = glabel.replace(/[^a-zA-Z0-9_\/s]/gi, ' ').trim();
				  foldername = document.getElementById("folderid").value;
				  if ($.trim(foldername).length)
				  {
					  dir += "/" + foldername.replace(/[^a-zA-Z0-9_\/s]/gi, ' ').trim();
				  }
				  $.ajax
				 ({
					 type: 'post',
					 url:  'savefull_gmail.php',
					 data: 
						{
							 glabelPHP         : dir,
							 fileNamePHP       : fileName,
							 messagedetailsPHP : JSON.stringify(messagedetails),
							 woattachPHP       : woattach,
							 pagePHP           : cpage
						},
					 success: function(msg) 
						{
						  document.getElementById("amsg").innerHTML = msg;
						  messageindex++;
						  GetMessages(messageindex, messages);
						}
				 });
			  }
			  
			  function FormEmail (msgpayload)
			  {
				  var messagedetails = [];
				  messagedetails = AppendHeaderText(msgpayload.headers, messagedetails);
				  messagedetails.push(es);
				  var mType = msgpayload.mimeType;
				  if (mType == "multipart/alternative")
				  {
					  var bdyalt = "--" + getHeader(msgpayload.headers,"Content-Type").split(';')[1].split('"')[1];
					  if (bdyalt == "--undefined")
					  {
						  bdyalt = "--" + getHeader(msgpayload.headers,"Content-Type").split(';')[1].split('=')[1];
					  }
					  var parts = msgpayload.parts;
					  messagedetails = AppendAltBody(bdyalt, parts, messagedetails);
				  }
				  if (mType == "multipart/mixed")
				  {
					  var hv = getHeader(msgpayload.headers,"Content-Type");
					  if (hv == "")
					  {
						  hv = getHeader(msgpayload.headers,"Content-type");
					  }
					  var bdymx = "--" + hv.split('"')[1]; //boundary
					  if (bdymx == "--undefined")
					  {
						  bdymx = "--" + hv.split('=')[1];
					  }
					  var parts = msgpayload.parts;
					  messagedetails = AppendMixBody(bdymx, parts, messagedetails);
				  }
				  if (mType == "multipart/related")
				  {
					  var hv = getHeader(msgpayload.headers,"Content-Type");
					  if (hv.indexOf("multipart/alternative") !== -1)
					  {
						  var bdyrel = "--" + hv.split('"')[1];
					  } else
					  {
						  var bdyrel = "--" + hv.split(';')[1].split('"')[1];

					  }
					  if (bdyrel == "--undefined")
					  {
							  bdyrel = "--" + hv.split('=')[1];
					  }
					  var parts = msgpayload.parts;
					  messagedetails = AppendRelBody(bdyrel, parts, messagedetails);
				  }
				  if ((mType == 'text/plain') || (mType == 'text/html'))
				  {
					  var convertTo = GetTransferEncoding(msgpayload.headers); // check if transfer-encoding is base64.
					  if (convertTo == "base64")
					  {
						  messagedetails.push((msgpayload.body.data.replace(/-/g, '+').replace(/_/g, '/')) + es);
					  } else 
					  { // transfer encoding is not base64.
				         try
						 {
							 messagedetails.push(convert(msgpayload.body.data) + es);
						 }
						 catch (err)
						 {
							 var tempemail = Base64Decode(msgpayload.body.data);
							 messagedetails.push(tempemail + es);
						 }
					  }
				  }
				  return messagedetails;
			  }
			  
			 function AppendHeaderText(headers, messagedetails)
			 {
				 if (headers)
				 {
					 for (k0=0; k0 < headers.length; k0++)
					 {
						 var header = headers[k0];
						 var headerName = header.name;
						 var headerValue = header.value;
						 var headertext = headerName + ": " + headerValue + es;
						 messagedetails.push(headertext);
					 }
				 }
				 return messagedetails;
			 }
			 
			 function GetTransferEncoding(headers)
			 {
				 var convertTo = "";
				 for (k2=0; k2 < headers.length; k2++)
				 {
					 var header = headers[k2];
					 if (header.value == "base64")
					 {
						 convertTo = "base64";
					 }
				 }
				 return convertTo;
			 }
			 
			 function AppendAltBody(bdyalt, parts, messagedetails)
			 {
				 for (k1=0; k1 < parts.length; k1++)
				 {
					 if ((parts[k1].mimeType == "text/plain") || (parts[k1].mimeType == "text/html"))
					 {
						 messagedetails.push(bdyalt + es);
						 var convertTo = GetTransferEncoding(parts[k1].headers);
						 messagedetails = AppendHeaderText(parts[k1].headers, messagedetails);
						 messagedetails.push(es);
						 if (convertTo == "base64")
						 {
							 messagedetails.push((parts[k1].body.data.replace(/-/g, '+').replace(/_/g, '/')) + es);
						 } else 
						 {
							 try
							 {
								 messagedetails.push(convert(parts[k1].body.data) + es);
							 }
							 catch (err)
							 {
								 var tempemail = Base64Decode(parts[k1].body.data);
								 messagedetails.push(tempemail + es);
							 }
						 }
						 messagedetails.push(es);
						 }
				 }
				 messagedetails.push(bdyalt + "--" + es);
				 messagedetails.push(es);
				 return messagedetails;
			 }
			 
			 function AppendMixBody(bdymx, parts, messagedetails)
			 {
				 for (z=0; z < parts.length; z++)
				 {
					 messagedetails.push(bdymx + es);
					 var part = parts[z];
					 if (part.filename == "")
					 {
						 var mType1 = part.mimeType;
						 if ((mType1 == "text/plain") || (mType1 == "text/html"))
						 {
							 messagedetails = AppendHeaderText(part.headers, messagedetails);
							 messagedetails.push(es);
							 var convertTo = GetTransferEncoding(part.headers);
							 if (convertTo == "base64")
							 {
								  messagedetails.push((part.body.data.replace(/-/g, '+').replace(/_/g, '/')) + es);
							 } else 
							 { // transfer encoding is not base64.
							     try
								 {
									 messagedetails.push(convert(part.body.data) + es);
								 }
								 catch (err)
								 {
									 var tempemail = Base64Decode(part.body.data);
									 messagedetails.push(tempemail + es);
								 }
							 }
							 messagedetails.push(es);
						 }
						 if (mType1 == "multipart/alternative")
						 {
							 var headertextalt = part.headers[0].name + ": " + part.headers[0].value + es;
							 messagedetails.push(headertextalt);
							 messagedetails.push(es);
							 var bdyalt = "--" + messagedetails[(messagedetails.length)-2].split(";")[1].split('"')[1];
							 if (bdyalt == "--undefined")
							 {
								 bdyalt = "--" + messagedetails[(messagedetails.length)-2].split(";")[1].split('=')[1];
							 }
							 var part1s = part.parts;
							 messagedetails = AppendAltBody(bdyalt, part1s, messagedetails);
						 }
						 if (mType1 == "multipart/related")
						 {
							 var headertextrel = part.headers[0].name + ": " + part.headers[0].value + es; 
							 messagedetails.push(headertextrel);
							 messagedetails.push(es);
							 var bdyrel = "--" + messagedetails[(messagedetails.length)-2].split(";")[1].split('"')[1];
							 if (bdyrel == "--undefined")
							 {
								 bdyrel = "--" + messagedetails[(messagedetails.length)-2].split(";")[1].split('=')[1];
							 }
							 var part1s = part.parts;
							 messagedetails = AppendRelBody(bdyrel, part1s, messagedetails);
						 }
					 }
				 }
				 messagedetails.push(bdymx + "--" + es);
				 messagedetails.push(es);
				 return messagedetails;
			 }
			 
			 function AppendRelBody(bdyrel, parts, messagedetails)
			 {
				 for (z1=0; z1 < parts.length; z1++)
				 {
					 messagedetails.push(bdyrel + es);
				     //messagedetails.push(es);
					 var part = parts[z1];
					 if (part.filename == "")
					 {
						 var mType2 = part.mimeType;
						 if ((mType2 == "text/plain") || (mType2 == "text/html"))
						 {
							 //console.log("line 536");
							 var convertTo = GetTransferEncoding(part.headers);
							 if (convertTo == "base64")
							 {
								  messagedetails.push((part.body.data.replace(/-/g, '+').replace(/_/g, '/')) + es);
							 } else 
							 { // transfer encoding is not base64.
							     try
								 {
									 messagedetails.push(convert(part.body.data) + es);
								 }
								 catch (err)
								 {
									 var tempemail = Base64Decode(part.body.data);
									 messagedetails.push(tempemail + es);
								 }
							 }
							 //messagedetails.push(convert(part.body.data.replace(/-/g, '+').replace(/_/g, '/')) + es);
							 messagedetails.push(es);
						 } else
						 {
							 var headertextalt = part.headers[0].name + ": " + part.headers[0].value + es;
							 messagedetails.push(headertextalt);
							 messagedetails.push(es);
							 var bdyalt = "--" + messagedetails[(messagedetails.length)-2].split(";")[1].split('"')[1];
							 if (bdyalt == "--undefined")
							 {
								 bdyalt = "--" + messagedetails[(messagedetails.length)-2].split(";")[1].split('=')[1];
							 }
							 var part2s = part.parts;
							 messagedetails = AppendAltBody(bdyalt, part2s, messagedetails)
						 }
					 }
				 }
				 messagedetails.push(bdyrel + '--' + es);
				 messagedetails.push(es);
				 return messagedetails;
			 }
		
		</script>
		
		<script type="text/javascript" src="https://apis.google.com/js/client.js?onload=handleClientLoad"></script>
	
	</head>
	
	<body>
	
		<input type="button" id="authorizeButton" style="display:none" value="Login with GMail"/>
		
		<p id="notice" style="display:none">check broswer console for output</p>
		
		<div id="app" style="display:none">
	
			<div id="main">
				<form>
					<label for="labelsel" id="labelselid"> Label:  <select id = "labelsel"><option value="Select">Select..</option></select></label><br><br/>
					<label for="afterdate" id="afterlabelid">  After:  <input  type="date" id = "afterdate"/></label><br><br/>
					<label for="beforedate" id="beforelabelid"> Before: <input  type="date" id = "beforedate"/></label><br><br/>
					<label for="from"> From: <input type="text" name="from" id = "fromid"/></label><br><br/>
					<label for="to"> To: <input type="text" name="to" id = "toid"/></label><br><br/>
					<label for="suject"> Subject: <input type="text" name="subject" id = "subjectid"/></label><br><br/>
					<label for="folder"> Folder name: <input type="text" name="folder" id = "folderid"/></label><br><br/>
					<label for="continue" id="continueidL"> Continue from page: <input type="text" name="continue" id = "continueid"/></label><br><br/>
					<button type="button" id="backupbut" onclick="BackUp()">Start Back Up</button><br><br/>
				</form>
			</div>
		
			<div id="sub">
				<form>	
					<p id="pgmsg"></p>
					<p id="totalthreads"></p>
					<p id="tmsg"></p>
					<p id="amsg"></p>
					<p id="msg"></p>
				</form>
			</div>
			
		</div>
		
	</body>
	
	
	<style type="text/css">
		body
		{
		  font-family: 'Droid Sans', serif;
		  font-size: 14px;
		  margin-top: 0.5cm;
		}
		div#main 
		{
		  width:300px;
		  float: left;
		}
		#sub
		{
		  margin-left: 350px;
		}
		label
		{
		  width: 250px;
		  font-family: 'Droid Sans', serif;
		  padding: 4px;
		  display: inline-block;
		  vertical-align: baseline;
		  margin-left: 5px;
		}
		select
		{
		  width: 250px;
		  font-family: 'Droid Sans', serif;
		  padding: 4px;
		  display: inline-block;
		  vertical-align: baseline;
		}
		input
		{
			font-family: 'Droid Sans', serif;
			width: 250px;
			padding: 2px;
			display: inline-block;
			vertical-align: baseline;
		}
		input#subjectid, input#fromid, input#toid, input#continueid, input#folderid
		{
			height: 20px;
		}
		label#continueidL, input#continueid
		{
			vertical-align: middle;
		}
		input#continueid
		{
			width: 50px;
		}
	    button#backupbut, input#authorizeButton
		{
		  display: inline-block;
		  background-color: #03a9f4;
		  color: #fff;
		  cursor: pointer;
		  font: 500 14px/20px Roboto,sans-serif;
		  height: 36px;
		  margin-left: 10px;
		  min-width: 36px;
		  outline: 0;
		  overflow: hidden;
		  padding: 8 px;
		  text-align: center;
		  text-decoration: none;
		  text-overflow: ellipsis;
		  text-transform: uppercase;
		  transition: background-color .2s,box-shadow .2s;
		  vertical-align: baseline;
		  white-space: nowrap;
		  border-radius: 0px;
		  align-items: flex-start;
		  letter-spacing: normal;
		  word-spacing: normal;
		  text-indent: 0px;
		  text-shadow: none;
		  border-spacing: 0;
		  border-collapse: collapse;
		}
		input#authorizeButton
		{
		  display: inline-block;
		  background-color: #03a9f4;
		  color: #fff;
		  cursor: pointer;
		  font: 500 14px/20px Roboto,sans-serif;
		  height: 36px;
		  margin: 0;
		  min-width: 36px;
		  outline: 0;
		  overflow: hidden;
		  padding: 8 px;
		  text-align: center;
		  text-decoration: none;
		  text-overflow: ellipsis;
		  text-transform: uppercase;
		  transition: background-color .2s,box-shadow .2s;
		  vertical-align: baseline;
		  white-space: nowrap;
		  border-radius: 0px;
		  align-items: flex-start;
		  letter-spacing: normal;
		  word-spacing: normal;
		  text-indent: 0px;
		  text-shadow: none;
		  border-spacing: 0;
		  border-collapse: collapse;
		  position: absolute;
		  top: 20%;
		  left: 43%
		}
	</style>


</html>
