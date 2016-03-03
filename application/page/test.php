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
			res.push(0);
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
			data:['Inserted pages', 'Insert speed', 'Crawled']
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
				scale: true,
				name : 'Inserted pages'
			},
			{
				type : 'value',
				scale: true,
				name : 'Insert speed',
                min:0,
                max: 20
                //boundaryGap: [0.2, 0.2]
			}
		],
		series : [
			{
				name:'Inserted pages',
				type:'line',
				smooth:true,
				itemStyle: {normal: {areaStyle: {type: 'default'}}},

				data:generateData()
			},
            {
                //xAxisIndex: 1,
                yAxisIndex: 1,
                name:'Insert speed',
                type:'bar',
                smooth:true,
                //itemStyle: {normal: {areaStyle: {type: 'default'}}},
                data: generateData()
            },
            {
                name:'Crawled',
                type:'line',
                smooth:true,
                itemStyle: {normal: {areaStyle: {type: 'default'}}},
                data: generateData()
            },


		]
	};



	var myChart = echarts.init(document.getElementById('test'));
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



        //console.debug(Math.floor(currentTime.getTime()/1000));



        $.ajax({
            url:'http://127.0.0.1/Senseio/public/component/crawlerSpeed',
            success: function(data) {

                var currentTime=new Date();

                axisData = (new Date()).toLocaleTimeString().replace(/^\D*/,'');

                if(lastInsertedPages!==null) {
                    var deltaPage=data.pages-lastInsertedPages;
                    var deltaCrawled=data.crawledPages-lastCrawledPages;

                    var delta=Math.floor((currentTime.getTime()-startTime.getTime())/1000);


                    console.debug(delta);

                    insertedPageAverage=data.pages/delta;
                    crawledPageAverage=data.crawledPages/delta;

                    //console.debug(crawledPageAverage);
                }


                lastInsertedPages=data.pages;
                lastCrawledPages=data.crawledPages;

                //console.debug(lastInsertedPages);
                //console.debug(lastCrawledPages);


                console.debug(insertedPageAverage);


                myChart.addData([
                    [
                        2,
                        data.crawledPages,
                        false,
                        false,
                        axisData
                    ],
                    [
                        0,
                        data.pages,
                        false,
                        false,
                        axisData
                    ],
                    [
                        1,
                        insertedPageAverage,
                        false,
                        false,
                        axisData
                    ],
                ]);


            }
        })




	}, 1000)






</script>


</html>