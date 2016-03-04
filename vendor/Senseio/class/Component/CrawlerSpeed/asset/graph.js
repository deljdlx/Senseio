$(function() {


option = {

	tooltip : {
		formatter: "{a} <br/>{b} : {c}%"
	},
	series : [
		{
			name:"",
			type:'gauge',
			axisLine: {            // 坐标轴线
				lineStyle: {       // 属性lineStyle控制线条样式
					color: [[0.2, '#228b22'],[0.8, '#48b'],[1, '#ff4500']],
					width: 2
				}
			},
			pointer : {
				width : 2
			},
			min:0,
			max:40,
			detail : {formatter:'{value}'},
			data:[{value: 0, name: ""}]
		}
	]
};






$('.senseio.crawlerSpeed').each(function() {
	
}

)

var myChart = echarts.init(document.querySelector('.senseio.crawlerSpeed'));
myChart.setOption(option, true);


	var timeTicket;

	var axisData;
	clearInterval(timeTicket);

	var startTime=new Date();

	console.debug(startTime.getTime());


	var lastInsertedPages=null;
	var lastCrawledPages=null;


	var insertedPageAverage=0;
	var crawledPageAverage=0;


	timeTicket = setInterval(function (){




		$.ajax({
			url:'http://127.0.0.1/Senseio/public/component/crawlerSpeed',
			success: function(data) {

				var currentTime=new Date();

				axisData = (new Date()).toLocaleTimeString().replace(/^\D*/,'');

				if(lastInsertedPages!==null) {


					var deltaPage=data.pages-lastInsertedPages;
					var deltaCrawled=data.crawledPages-lastCrawledPages;

					var delta=Math.floor((currentTime.getTime()-startTime.getTime()));




					insertedPageAverage=deltaPage/(delta/1000);

					console.debug(delta);
					console.debug(data.crawledPages);

					crawledPageAverage=deltaCrawled/(delta/1000);

					//console.debug(crawledPageAverage);
				}


				lastInsertedPages=data.pages;
				lastCrawledPages=data.crawledPages;

				startTime=currentTime;


				option.series[0].data[0].value = Math.floor(insertedPageAverage*100)/100;
				myChart.setOption(option, true);


			}
		})




	}, 1000);



});
