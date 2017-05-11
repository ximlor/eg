function route(handle, pathname, response, request) {
  console.log('Route for ' + pathname +'.');
  if (typeof handle[pathname] === 'function') {
    handle[pathname](response, request);
  }
  else{
    console.log('No such as page for ' + pathname +'.');
    response.writeHead(404, {
      'Content-Type': 'text/plain'
    });
    response.write('404. Page is not found.');
  }
}

exports.route = route;
