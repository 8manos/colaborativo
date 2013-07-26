
/*
 * GET home page.
 */

exports.index = function(req, res){
  res.render('index', { title: 'Colaborativo' });
};

/** serve jade enabled partials */
exports.partials = function(req, res) {
    res.render('partials/' + req.params.name);
};