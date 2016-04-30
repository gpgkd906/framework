$(function() {
    var $tableCollapse = $('[data-toggle=table-collapse]');
    $tableCollapse.click(function() {
	var $this = $(this);
	var $target = $this.data('target');
	$('[data-reference=' + $target + ']').toggle();
    });
    //init
    $tableCollapse.hide(0);
    $('[data-toggle=table-collapse][data-depth=1]').show(0);
})
