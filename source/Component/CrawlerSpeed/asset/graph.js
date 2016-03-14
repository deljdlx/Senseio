function CrawlerSpeed(element)
{

	this.element=element;

	this.serviceURL='http://127.0.0.1/Senseio/public/component/crawlerSpeed';

	this.options={

		tooltip : {
			formatter: "{a} <br/>{b} : {c}%"
		},
		series : [
			{
				name:"",
				type:'gauge',
				axisLine: {
					lineStyle: {
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
}

CrawlerSpeed.prototype.run=function() {


	this.element.chart = echarts.init(this.element);
	this.element.chart.setOption(this.options, true);


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
				url:this.serviceURL,
				success: function(data) {

					var currentTime=new Date();

					axisData = (new Date()).toLocaleTimeString().replace(/^\D*/,'');





					if(lastInsertedPages!==null) {


						var deltaPage=data.pages-lastInsertedPages;
						var deltaCrawled=data.crawledPages-lastCrawledPages;

						var delta=Math.floor((currentTime.getTime()-startTime.getTime()));
						insertedPageAverage=deltaPage/(delta/1000);
						crawledPageAverage=deltaCrawled/(delta/1000);
					}


					lastInsertedPages=data.pages;
					lastCrawledPages=data.crawledPages;

					startTime=currentTime;


					this.options.series[0].data[0].value = Math.floor(insertedPageAverage*100)/100;
					this.element.chart.setOption(this.options, true);


				}.bind(this)
			})




		}.bind(this), 1000);




}



