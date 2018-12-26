//DOT模板
function useDot(tid, sid, obj) { //tid是模板id，sid是页面容器的id，obj是传入的对象
	var evalText = doT.template($(tid).text());
	$(sid).html(evalText(obj));
}

function appDot(tid, sid, obj) {
	var evalText = doT.template($(tid).text());
	$(sid).append(evalText(obj));
}