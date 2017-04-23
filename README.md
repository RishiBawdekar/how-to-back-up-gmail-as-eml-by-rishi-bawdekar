# SaveGMailAsEML

Do you want to save your GMail messages to local computer without third-party softwares? If yes, then you have come to the right repository. Welcome!

The software programs in this repository talk ONLY to Google and your data is not shared with anyone. The source-code is open and can be easily modified. In order to use this application, there are few prerequisites:

# 1. Localhost

You need to have localhost server. Many localhost servers are available. I have used WAMP. The website is http://www.wampserver.com/en/.

Here is a link to Youtube video showing how to install - https://www.youtube.com/watch?v=RZTYqTGqtjI.

Before you download WAMP, ensure you have downloaded the prerequisite softwares.

Troubleshooting some common errors

1. Wamp Won't Turn Green & VCRUNTIME140.dll error -> http://stackoverflow.com/questions/34215395/wamp-wont-turn-green-vcruntime140-dll-error

2. Wamp Server not goes to green color -> http://stackoverflow.com/questions/17168624/wamp-server-not-goes-to-green-color

3. PHP Warning:  Unknown: POST Content-Length of X bytes exceeds the limit of 8388608 bytes in Unknown on line 0
                 
                 a. Open php.ini file.
                 b. Find post_max_size. Change limit from 8M to 2000M. If problem still occurs, change limit further.
                 
# 2. Google Client ID

1. Login to your GMail account.
2. Open link -> https://console.developers.google.com/flows/enableapi?apiid=gmail
3. Select "Create a Project" and continue.
4. Once GMail API is enabled, click on "Go to credential's".
5. Click on "client ID". Click "Configure consent screen".
6. In product name, type "SaveGMailAsEML" and save.
7. Select Web application.
8. In "Authorized JavaScript origins", type in "http://localhost" (without quotes).
9. Click "Create".
10. Copy your client ID.

# 3. Enable IMAP in GMail.

# 4. How it works?

1. Download the zip file. Extract contents to "www" directory of WAMP. Rename the folder to SaveGMailAsEML.
2. Go to SaveGMailAsEML folder and open "backup.php" file in Notepad. Find "CLIENT_ID" and replace "your-google-client-id" with your client id obtained in step 2. Save and close.
3. Open Chrome and type in -> http://localhost/SaveGMailAsEML/backup.php
4. Click on "Login With GMail". Type in your username and password. If you are already logged in, you will directed to "Request For Permission" page. Click "Allow". You will be directed to app homepage.
5. After initialization, you will find your GMail labels under Labels.
6. Choose a GMail label and click on Start Back UP.
7. A folder will be created in SaveGMailAsEML folder with name of your GMail label. Subfolders will be created within that folder for pages and emails without attachments.
8. Status message appears on webpage when download is complete.

The downloaded emails are in .eml format. They can be viewed using Windows Live Mail or Windows Mail 10 or any other .eml viewer.

The time limit for receiving data from Google is 30 seconds (which is adjustable). If time exceeds 30 seconds, then the email is saved without attachment.

It is recommended to run the program while keeping the console window open. 

The code has been tested on Chrome Version 54.0.2840.71.

Known errors and solutions:

1. The access token is valid for 1 hour. If the program runs for more than 1 hour, an error will be shown in console window. When this happens refresh and run again. Use the "Continue from page" option and structure your query using label, dates, to, from, subject fields to start where you had left off.

2. Sometimes you may get resource exceeded or quota error. When this happens, refresh and use the guide above to structure your query to start where you had left off.

3. Sometimes you mat get "Aw, Snap" error. Refresh and start again.
