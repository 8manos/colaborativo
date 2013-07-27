
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('http')
  , io = require('socket.io')
  , path = require('path')
  , lessMiddleware = require('less-middleware')
  , os = require('os')
  , Twit = require('twit');

var app = express();

var Mongoose = require('mongoose')
    , Schema = Mongoose.Schema;

var db = Mongoose.connect( process.env.MONGOLAB_URI );
var tmpDir = os.tmpDir();

// all environments
app.set('port', process.env.PORT || 3000);
app.set('views', __dirname + '/views');
app.set('view engine', 'jade');
app.use(express.favicon());
app.use(express.logger('dev'));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(app.router);
app.use(lessMiddleware({
  src: path.join(__dirname, 'public'),
  dest: tmpDir,
  compress: true
}));
app.use(express.static(tmpDir));
app.use(express.static(path.join(__dirname, 'public')));

// development only
if ('development' == app.get('env')) {
  app.use(express.errorHandler());
}

app.get('/', routes.index);
app.get('/partials/:name', routes.partials);
app.get('/users', user.list);

var server = http.createServer(app);
var io = require('socket.io').listen(server);

var tweetSchema = new Schema({ 
	  id: Number,
	  screen_name: String,
      name: String,
      text: String,
      profile_img: String
    }, { capped: 12000 })

var Tweet = Mongoose.model('Tweet', tweetSchema);

io.sockets.on('connection', function (socket) {
    console.log('A socket connected!');
    var skip = Tweet.count({}, function(err, c){
    	skip_count = c-1;

    	last = Tweet.where().skip(skip_count).limit(1).exec(function(err, doc){ 
    		     
    		     var last_id = doc[0].id;

    		     	console.log("Last "+last_id);

				    var streamdb = Tweet.find().where('id').gte(last_id).limit(1).tailable().stream();

				    streamdb.on('data', function (doc) {
				      socket.volatile.emit('tweet', doc);
				    }).on('error', function (err) {
				      console.log(err);
				    }).on('close', function () {
				      console.log("closed db stream");
				    });
    		   });
    });
});

server.listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});