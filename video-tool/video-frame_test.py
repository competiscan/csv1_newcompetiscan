import boto3
from PIL import Image
import io
import json
#from dbconfig import dbconn,mydb
from dbconfig import MySQLHost
import sys
import json
import subprocess
import os

connectionObj = MySQLHost()
comprehend = boto3.client(service_name='comprehend')
                
#text = "It is raining today in Seattle"
#text="so what's in your wallet well I'm gonna talk about a card today that you are gonna be very surprised how much money it can save you so make sure you stay throughout the whole show you're gonna be really glad you did yep it's the AARP card not only does this paid membership save you a ton of money they have thousands of free resources that are gonna make your quality of life better I'm Evan from the forward through 50 show and I started this show because I wanted to have answers I wanted to help those of us over 50 to approach our senior years with confidence and to keep our bodies and our brains healthy if you're new here or you've been here for a while and you'd like to approach your senior years with confidence I'd encourage you to subscribe and hit that subscribe button down below each week on the forward through 50 show we have three segments to the show the first one is answering a question from one of you the second one is an action item fun and functional and the third one is a quote to take us through the week and a feel-good story as I mentioned please hang in there it's gonna be about a five-minute video and this information is really going to be beneficial you'll be surprised at the amount of educational a super helpful information that the AARP website has this is not sponsored by AARP I just happen to be a big fan and really wanted to share with you guys the things that I have found beneficial so what is AARP well in the u.s. when you start getting mail from them it's the omen that you're officially getting old kind of like the Grim Reaper but with some benefits Mike M wants to know is the a 8rp membership worth the price well it's 16 dollars a month like a year and so for me the answer is yes but even if you don't purchase the membership there is so much free educational useful information I'm going to share five things I really like about AARP some are on the paid membership and some are on the free website number one is the discounts and that is on the paid membership the paid discounts include restaurants entertainment travel auto service technology they have a 20 page booklet that you can download that has all the information on all the discounts number two for me are the magazines that they send out this one they send out every two months got information on travel health holidays many reports and one article how to make your kids not hate you sounds pretty practical this other one comes out every single month and it also has a lot of practical information he talks about fraud alerts things to be aware of how not to get tricked out of our money and even some of the political things that are going on in this case it has to do with all the drug policies a lot of this information I've discovered is on the website where it's free so to get some of this or probably most of this information you don't even have to have the membership number three is they have a lot of insurances that are available you do have to go through insurance agents to do it but I have for instance a long-term care policy that is an AARP policy and it is I haven't had to use it yet but as I've talked with my agent who is a broker and he handles a lot of different companies he personally has this one as well then there's also the Supplemental Medicare policy and my dad has that one he has been through a lot of medical things in the last few years and it covers what Medicare doesn't so saves him thousands and thousands and thousands of dollars so number four is what goes on in the background of AARP they stand for a lot of things that get involved politically for things like that drug article that was in there they also work at ending senior poverty they worked on social justice issues and they also are working on behalf of those of us as we're getting older with age discrimination in the workplace the number five for me is their massive massive website and again it that's all for free it has information detailed information articles resources for being a caregiver for figuring out if you're ready for retirement financially for budgeting fitness on being social making friends and it even has a section for games where you can just go online and play games those ones that are good for your brain all right so this week's action item go to the website check it out for yourself see what great resources are there you will find things absolutely I have no doubt that are going to apply to your life now and they'll apply to your life later all right in the comments go ahead and let us know what you found what surprised you and I'll do the same if you're enjoying this video it'd be awesome and really helpful to me if you'd hit the like button alright this week's quote is from anonymous old age the one thing duct tape can't fix the feel-good story today is from the website inspire more and the author is beverly L Jenkins as usual I'm going to read it verbatim says some people were just born to lead others when Darius Brown of Newark New Jersey was 8 years old he was plagued with speech fine motor skills and comprehension delays he began working to overcome these issues by honing his fine motor skills in a variety of ways including helping his big sister cut out fabric for bows a few years later Darius had overcome his delays was now making bows bowties and other fabric designs just for fun he'd loved to dress up the family dog with a dashing bow tie so when he heard a news story about cats and dogs who'd been displaced by hurricanes Harvey and her aunt Irma in 2017 his mind began working at the problem to see if there was any way that he at 11 could help he thought of how cute his dog looked in his bowties and how much making the ties had helped him develop and suddenly the idea came to him put shelter animals and cute ties to increase their shots at getting adopted Darius created his first business venture and made himself the CEO he called it Bo and paws and he began cranking it cranking out as many toys as he could donating them to shelter residents by the thousands soon other companies caught wind of his efforts and began sending him donations some monetary and some comprised of big boxes of fabric various audience began to grow and now over 43,000 people follow the now 12 year old on Instagram his efforts have helped countless get noticed on Petfinder and other adoption sites sending more animals to their forever homes and saving them from being euthanized Darius's efforts have been recognized internationally he has reciprocated by setting off still more ties overseas to the UK so that their adoptable pets can look their best too he's been featured on TV shows and has met dozens of public figures from Kim Kardashian to Michael Strahan still more impressive darius received a personal letter from former President Barack Obama praising him for his work at such a young age I love that that's great well anyhow I hope you have a good week thanks so much for being here and don't forget to put a comment below for me for number two I like this number four"

#text=text[:5000]

check_query='SELECT id FROM cscan_youtube_video where audio_text_status=1'
records = connectionObj.select(check_query,())
if records is not None:
	for row in records:
		vid=row['id']
		check_sentiment_query='SELECT id,video_id FROM cscan_youtube_sentiment where video_id=%s'
		whereval=(vid,)		
		sentiment_records = connectionObj.select(check_sentiment_query,whereval,())
		if sentiment_records is not None:			
			continue
		audio_query='SELECT id,audio_text FROM `cscan_youtube_audio_text` WHERE video_id=%s'
		where_audio_val=(vid,)		
		audio_records = connectionObj.select(audio_query,where_audio_val,())			
		if audio_records is not None:
			audioArr=[]
			for audioval in audio_records:
				aid=audioval['id']
				audio_text=audioval['audio_text']				
				audioArr=[]
				if(audio_text!=''):
					audioArr.append(audio_text)
				
			if(len(audioArr)>0):
				audiotext=' '.join(audioArr)	
			if(len(audiotext)>0):
				try:
					text=audiotext[:5000]
					print('Calling DetectSentiment')
					sentiment_result=json.loads(json.dumps(comprehend.detect_sentiment(Text=text, LanguageCode='en'), sort_keys=True, indent=4))
					#print(sentiment_result)
					print('End of DetectSentiment\n')
					#print(sentiment_result['Sentiment'])
					if(int(sentiment_result['ResponseMetadata']['HTTPStatusCode'])==200):
						sentiment=sentiment_result['Sentiment']
						mixed=sentiment_result['SentimentScore']['Mixed']
						negative=sentiment_result['SentimentScore']['Negative']
						neutral=sentiment_result['SentimentScore']['Neutral']
						positive=sentiment_result['SentimentScore']['Positive']
						insert_query_s="Insert into cscan_youtube_sentiment (video_id,sentiment,mixed,negative,neutral,positive) VALUES (%s, %s, %s, %s, %s, %s) "
						data_s = (vid,sentiment,mixed,negative,neutral,positive,)								
						
						connectionObj.execute(insert_query_s,data_s)
						
				except:					
					print('There are some issue with sentiment score api')					
				
				#print(detectedtext)			
		else:	
			print('There are no audio text available to detect sentiment score for video id: '+str(vid))		
			
	print('Done!')		
else:
	print('There are no video exist to fetch sentiment score!')
	
cmd = ['pgrep -f .*python.*search_keyword_frame.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
#if len(my_pid.splitlines()) >0:
   #print("Running")  
   #os.system("pkill -f search_keyword_frame.py")
   	
#subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/download-video.py'])



