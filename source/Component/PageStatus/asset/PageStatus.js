function PageStatus(element)
{

	this.element=element;

	this.serviceURL='http://127.0.0.1/Senseio/public/component/pageStatus';

	this.options=option = {
		tooltip : {
			trigger: 'item',
			formatter: "{b}<br/>{c} ({d}%)"
		},
		legend: {
			orient : 'vertical',
			x : 'left',
			data:[]
		},
		calculable : true,
		series : [
			{
				type:'pie',
				radius : '55%',
				center: ['50%', '60%'],
				data:[]
			}
		]
	};
}

PageStatus.prototype.run=function() {


	this.element.chart = echarts.init(this.element);
	//this.element.chart.setOption(this.options, true);


	$.ajax({
		url:this.serviceURL,
		success: function(data) {

			for(var status in data) {
				var key=status;
				var value=data[status];

				this.options.legend.data.push(key);

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



