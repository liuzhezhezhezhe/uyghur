;(function () {
	// 创建SearchResult类，用于存储翻译结果
	function SearchResult(title,content) {
		this.title = title;
		this.content = content;
	}
	var vue = new Vue({
		el: '#uyghur',
		data: {
			searchContent: "",
			chineseResult: [new SearchResult("","")],
			uyghurResult: [new SearchResult("","")]
		},
		// 建立观察者，当searchContent内容发生变化时，进行查询（导致输入一个字母也会发送查询请求）
		watch: {
			searchContent: function () {
				// 内容为空时不进行查询（空内容也是内容改变，会触发ajax）
				if ($.trim(this.searchContent) != "") {
					var json;
					$.ajax({
						url: './action/search.php?content='+encodeURI(this.searchContent),
						dataType: 'json',
						// 设置为同步，将结果写入页面
						async: false,
						success: function (data) {
							json = data;
							length = json.length;
						}
					});

					for (var i = 0; i < length; i++) {
						this.chineseResult[i] = new SearchResult(json[i].title,json[i].content);
					}

					$.ajax({
						url: './action/translate.php?content='+encodeURI(this.searchContent),
						type: 'POST',
						dataType: 'json',
						data: {result: json},
						// 设置为同步，将结果写入页面
						async: false,
						success: function (data) {
							json = data;
							length = json.length;
						}
					});

					for (var i = 0; i < length; i++) {
						this.uyghurResult[i] = new SearchResult(json[i].title,json[i].content);
					}
				}
			}
		}
	});

})();