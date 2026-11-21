import os
import subprocess
cmd = ['pgrep -f .*python.*download-video.py']
cmd2 = ['pgrep -f .*python.*video-frame.py']
cmd3 = ['pgrep -f .*python.*youtube_transcript.py']
cmd4 = ['pgrep -f .*python.*search_keyword_audio.py']
cmd5 = ['pgrep -f .*python.*search_logo.py']
cmd6 = ['pgrep -f .*python.*detect-text.py']
cmd7 = ['pgrep -f .*python.*search_keyword_frame.py']
cmd8 = ['pgrep -f .*python.*sentiment.py']
cmd9 = ['pgrep -f .*python.*video-frame-pre.py']

process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid, err = process.communicate()

process2 = subprocess.Popen(cmd2, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid2, err2 = process2.communicate()

process3 = subprocess.Popen(cmd3, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid3, err3 = process3.communicate()

process4 = subprocess.Popen(cmd4, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid4, err4 = process4.communicate()

process5 = subprocess.Popen(cmd5, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid5, err5 = process5.communicate()

process6 = subprocess.Popen(cmd6, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid6, err6 = process6.communicate()

process7 = subprocess.Popen(cmd7, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid7, err7 = process7.communicate()

process8 = subprocess.Popen(cmd8, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid8, err8 = process8.communicate()

process9 = subprocess.Popen(cmd9, shell=True, stdout=subprocess.PIPE,stderr=subprocess.PIPE)
my_pid9, err9 = process9.communicate()

if((len(my_pid.splitlines())>0) or (len(my_pid2.splitlines())>0) or (len(my_pid3.splitlines())>0) or (len(my_pid4.splitlines())>0) or (len(my_pid5.splitlines())>0) or (len(my_pid6.splitlines())>0) or (len(my_pid7.splitlines())>0) or (len(my_pid8.splitlines())>0) or (len(my_pid9.splitlines())>0)):
	print("Running")
else:
	#subprocess.call(['python','/var/www/html/competiscan.com/video-tool/download-video.py'])
	subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/download-video.py'])
		
	

