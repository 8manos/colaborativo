
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('http')
  , io = require('socket.io')
  , path = require('path');

var app = express();

var Mongoose = require('mongoose');
// var db = Mongoose.createConnection('localhost', 'mytestapp');

// all environments
app.set('port', process.env.PORT || 3000);
app.set('views', __dirname + '/views');
app.set('view engine', 'jade');
app.use(express.favicon());
app.use(express.logger('dev'));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

// development only
if ('development' == app.get('env')) {
  app.use(express.errorHandler());
}

	var Twit = require('twit');

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

app.get('/', routes.index);
app.get('/partials/:name', routes.partials);
app.get('/users', user.list);

var server = http.createServer(app);
var io = require('socket.io').listen(server);

io.sockets.on('connection', function (socket) {
    console.log('A socket connected!');

    stream.on('tweet', function (tweet) {
        socket.volatile.emit('tweet', tweet);
    });
});

server.listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});