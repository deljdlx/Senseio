function  Application(urlRoot)
{
		this.urlRoot=urlRoot;

		this.components=[];
}



Application.prototype.run=function() {


	console.debug('Application started');

	$('[data-className]').each(function(index, node) {

		var component=new window[node.getAttribute('data-className')](node);
		$(node).find('meta').each(function(index, node) {
			component[node.getAttribute('name')]=node.getAttribute('value');
		});

		console.debug(component);


		try {
			component.run();
		}
		catch(exeption) {
			console.log('Starting component failed : '+node.getAttribute('data-className'));
		}



	}.bind(this))




}