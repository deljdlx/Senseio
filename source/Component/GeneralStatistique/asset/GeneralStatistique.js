function GeneralStatistique(element)
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

GeneralStatistique.prototype.run=function() {

	/*

	this.element.chart = echarts.init(this.element);
	//this.element.chart.setOption(this.options, true);
	*/



	$.ajax({
		url:this.serviceURL,
		success: function(data) {
			$(this.element).find('.pageCount').html(data.general.total);
			$(this.element).find('.averageBufferSize').html(data.general.averageSize);

			if(data.general.serverAverageSize) {
				$(this.element).find('.serverAverageSize').html(data.general.serverAverageSize);
			}

			if(data.general.linkCount) {
				$(this.element).find('.linkCount').html(data.general.linkCount);
			}

			if(data.general.averageLoadingTime) {
				$(this.element).find('.averageLoadingTime').html(data.general.averageLoadingTime);
			}







		}.bind(this)
	})



}



