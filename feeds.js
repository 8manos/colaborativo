console.log("Twit worker starting...");

var Twit = require('twit');

var Mongoose = require('mongoose')
    , Schema = Mongoose.Schema;

var db = Mongoose.connect( process.env.MONGOLAB_URI );

var T = new Twit({
    consumer_key: process.env.T_CONSUMER_KEY
  , consumer_secret: process.env.T_CONSUMER_SECRET
  , access_token: process.env.T_ACCESS_TOKEN
  , access_token_secret: process.env.T_ACCESS_SECRET
});

//
//  filter the twitter public stream by the word 'mango'. 
//
var stream = T.stream('statuses/filter', { track: '#WifeHerIf' });

var tweetSchema = new Schema({ 
	  id: Number,
	  screen_name: String,
      name: String,
      text: String,
      profile_img: String
    }, { capped: 12000 })

var Tweet = Mongoose.model('Tweet', tweetSchema);

stream.on('tweet', function (tweet) {

	var twitty = new Tweet({ 
		           id: tweet.id_str,
		           name: tweet.user.name,
		           text: tweet.text,
		           screen_name: tweet.user.screen_name,
		           profile_img: tweet.user.profile_image_url
		         });

	twitty.save(function (err) {
	  if (err)
	   // ...
	  console.log('pio');
	});

});