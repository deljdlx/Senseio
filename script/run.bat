php run.php --reset
start launch.bat
timeout 7 > NUL
FOR /L %%G IN (1,1,2) DO (
	start launch.bat
	//timeout 1 > NUL
)