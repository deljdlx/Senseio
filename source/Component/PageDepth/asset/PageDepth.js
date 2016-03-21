function PageDepth(element)
{

	this.element=element;

	this.serviceURL='http://127.0.0.1/Senseio/public/component/pageStatus';



	this.options = {

		tooltip : {
			trigger: 'axis',
			formatter: function(params) {
				return params[0].name+' '+params[0].seriesName + ' : ' + params[0].value + '%'
			}
		},
		legend: {
			data:[]
		},
		calculable : true,
		xAxis : [
			{
				type : 'category',
				data : []
			}
		],
		yAxis : [
			{
				type : 'value',
				axisLabel : {
					formatter: '{value} %'
				}
			}
		],
		series : [
			{
				type:'bar',
				data:[],
			}
		]
	}
	;



}

PageDepth.prototype.run=function() {


	this.element.chart = echarts.init(this.element);
	//this.element.chart.setOption(this.options, true);


	$.ajax({
		url:this.serviceURL,
		success: function(data) {

			console.log(data);
			for(var status in data) {
				var key=status;
				var value=data[status];

				//this.options.legend.data.push(key);


				console.debug(value);
				console.debug(key);

				console.debug(this.options)

				this.options.xAxis[0].data.push(
					key
				);


				this.options.series[0].data.push({
						value: value,
						name: key
				});
			}


			//this.options.series[0].data[0].value = Math.floor(insertedPageAverage*100)/100;
			this.element.chart.setOption(this.options, true);


		}.bind(this)
	})




}



