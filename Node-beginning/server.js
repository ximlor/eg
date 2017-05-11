/*
** 一个简单的 HTTP 服务器
*/

var http = require('http');

// 引入 url 模块解析请求
var url = require('url');

function start(route, handle){
  // http 模块的 createServer() 方法需要一个函数作为参数，并返回一个对象。该对象有一个 listen() 方法，参数为监听的端口号
  http.createServer(
    function(request, response) {
      var pathname = url.parse(request.url).pathname;
      console.log('Request for ' + pathname + ' received.');
      route(handle, pathname, response, request);
    }
  ).listen(8888);
}

// Node.js 基于事件驱动的回调，createServer() 在监听到请求时才会回调传入的处理函数，下面的代码不会被阻塞
console.log('Server start.');
exports.start = start;
