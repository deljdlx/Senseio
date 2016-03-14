php5 run.php --reset
start launch.bat
timeout 7 > NUL
FOR /L %%G IN (1,1,20) DO (
	start launch.bat
	#timeout 1 > NUL
)