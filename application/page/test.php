<!doctype html>
<html>
<head>
<script src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
<script src="vendor/echart/build/dist/echarts-all.js"></script>

</head>
<body>


<div id="test" style="width: 1200px; height: 400px; border: solid 1px #000"></div>

</body>


<script>



	function generateData() {
		var res = [];
		var len = 30;
		while (len--) {
			res.push((Math.random()*10 + 5));
		}
		return res;
	}



	option = {
		title : {
			//text: 'titre',
			//subtext: 'sous titre'
		},
		tooltip : {
			trigger: 'axis'
		},
		legend: {
			data:['s1', 's2']
		},
		xAxis : [
			{
				type : 'category',
				data : (function (){
					var now = new Date();
					var res = [];
					var len = 30;
					while (len--) {
						res.unshift(now.toLocaleTimeString().replace(/^\D*/,''));
						now = new Date(now - 2000);
					}
					return res;
				})()
			},
			{
				type : 'category',
				show:false,
				data : (function (){
					var now = new Date();
					var res = [];
					var len = 30;
					while (len--) {
						res.unshift(now.toLocaleTimeString().replace(/^\D*/,''));
						now = new Date(now - 2000);
					}
					return res;
				})()
			}
		],
		yAxis : [
			{
				type : 'value',
				scale: false,
				name : 's1',
			},
			{
				type : 'value',
				scale: false,
				name : 's2',
			}
		],
		series : [
			{
				name:'s1',
				type:'line',
				smooth:true,
				//itemStyle: {normal: {areaStyle: {type: 'default'}}},

				data:generateData()
			},
			{
				name:'s2',
				type:'line',
				smooth:true,
				//itemStyle: {normal: {areaStyle: {type: 'default'}}},
				data: generateData()
			}
		]
	};



	var myChart = echarts.init(document.getElementById('test'));
	myChart.setOption(option, true);


	var timeTicket;


	var axisData;
	clearInterval(timeTicket);
	timeTicket = setInterval(function (){

		axisData = (new Date()).toLocaleTimeString().replace(/^\D*/,'');

		// 动态数据接口 addData
		myChart.addData([
			[
				0,        // 系列索引
				Math.round(5+Math.random() * 10),
				false,
				false,
				axisData
			],
			[
				1,        // 系列索引
				Math.round(5+Math.random() * 10),
				false,
				false,
				axisData  // 坐标轴标签
			]
		]);
	}, 1000)






</script>


</html>