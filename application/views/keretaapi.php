<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Abu Nawas - Kereta Api</title> 

	<!-- Bootstrap -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>                                                                     
  .box{
  	width: 500px;
  	margin: auto;
  	margin-top: 50px;
  }
  .ui-autocomplete {
  	position: absolute;
  	z-index: 1000;
  	cursor: default;
  	padding: 0;
  	margin-top: 2px;
  	list-style: none;
  	background-color: #ffffff;
  	border: 1px solid #ccc;
  	-webkit-border-radius: 5px;
  	-moz-border-radius: 5px;
  	border-radius: 5px;
  	-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  	-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  }
  .ui-autocomplete > li {
  	padding: 3px 10px;
  }
  .ui-autocomplete > li.ui-state-focus {
  	background-color: #3399FF;
  	color:#ffffff;
  }
  .ui-helper-hidden-accessible {
  	display: none;
  }
</style>
</head>
<body>
	

	<div class="container">
		<div class="row">
			<div>

				<h1>Abu Nawas</h1>

				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li><a href="<?php echo base_url() ?>pesawat">Pesawat</a></li>
					<li class="active"><a href="<?php echo base_url() ?>keretaapi">Kereta APi</a></li>
					<li><a href="<?php echo base_url() ?>hotel">Hotel</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade active in">
						<h4>Cari Kereta</h4>
						
						<div class="col-md-6">
							<form class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
									<input type="text" class="form-control" id="departTrain" name="departTrain" placeholder="From City">
									<input type="hidden" name="departTrainCode" id="departTrainCode">
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
									<input type="text" class="form-control" id="arivedTrain" name="arivedTrain" placeholder="To City">
									<input type="hidden" name="arivedTrainCode" id="arivedTrainCode">
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
									<input type="text" class="form-control" id="departDate" name="departDate" placeholder="From Date">
								</div>

								<br>

								<div class="row">
									<div class="col-md-4">
										<div class="input-group">
											<span class="input-group-addon"><i class="glyphicon glyphicon-plane"></i></span>
											<select class="form-control" name="returnTrain" id="returnTrain">
												<option value="1">Return</option>
												<option value="2">One Way</option>
											</select>
										</div>
									</div>
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
									<input type="text" class="form-control" id="arivedDate" name="arivedDate" placeholder="To Date">
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
									<select class="form-control" id="class" name="class">
										<option value="0">Class</option>
										<option value="all">All</option>
										<option value="bis">Bisnis</option>
										<option value="eks">Eksekutif</option>
										<option value="eco">Ekonomi</option>
									</select>
								</div>

								<br>

								<div class="row">
									<div class="col-md-4">
										<div class="input-group">
											<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
											<select class="form-control" id="childPass" name="childPass">
												<option value="0">Child</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
											</select>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="input-group">
											<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
											<select class="form-control" id="adultPass" name="adultPass">
												<option value="0">Adult</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
											</select>
										</div>
									</div>

									<div class="col-md-4">
										<div class="input-group">
											<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
											<select class="form-control" id="invantPass" name="invantPass">
												<option value="0">Invant</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
											</select>
										</div>
									</div>
								</div>

								<br>

								<div class="row">
									<div class="col-md-4">
										<div class="input-group">
											<a id="SearchTrain" class="btn btn-success">Search Train</a>
										</div>
									</div>
								</div>
							</form>
						</div>
						
						<div class="col-md-6">
							<h3>Depature</h3>
							<table id="depatureData" class="table table-bordered table-hover table-striped">
								<thead>
									<tr>
										<th>Pilih</th>
										<th>Train ID</th>
										<th>Airlinest</th>
										<th>Price</th>
										<th>No.Train</th>
									</tr>
								</thead>

								<tbody>
									
								</tbody>
							</table>

							<br><br>

							<h3>Arrival</h3>
							<table id="arrivalData" class="table table-bordered table-hover table-striped">
								<thead>
									<tr>
										<th>Pilih</th>
										<th>Train ID</th>
										<th>Airlinest</th>
										<th>Price</th>
										<th>No.Train</th>
									</tr>
								</thead>

								<tbody></tbody>
							</table>

							<br>

							<a id="ChooseTrain" class="btn btn-success">Choose Train</a>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- Small modal -->
	<div class="modal fade bs-example-modal-sm" id="popup" style="display: none;" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif">
			</div>
		</div>
	</div>
	

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="assets/js/bootstrap.min.js"></script>

	<script>
		$(function () {

			$('#departTrain').autocomplete({
				dataType: "json",
				autoFocus: true,
				minLength: 1,
				select: function (event, ui) {
					$(this).val(ui.item.label);
					$("#departTrainCode").val(ui.item.code);
				},
				source: function (request, response) {
					$.getJSON("<?php echo base_url(); ?>SearchStation", { search: $('#departTrain').val() },
					function (data) {
						response($.map(data, function (value, key) {
							return {
								label: value.station_name,
								value: value.station_name,
								code: value.station_code
							};
						}));
					});
				}
			});

			$('#arivedTrain').autocomplete({
				dataType: "json",
				autoFocus: true,
				minLength: 1,
				select: function (event, ui) {
					$(this).val(ui.item.label);
					$("#arivedTrainCode").val(ui.item.code);
				},
				source: function (request, response) {
					$.getJSON("<?php echo base_url(); ?>SearchStation", { search: $('#arivedTrain').val() },
						function (data) {
							response($.map(data, function (value, key) {
								return {
									label: value.station_name,
								value: value.station_name,
								code: value.station_code
								};
							}));
						});
				}
			});

			$( "#departDate" ).datepicker({ dateFormat: 'yy-mm-dd' });

			$( "#arivedDate" ).datepicker({ dateFormat: 'yy-mm-dd' });

			$("#SearchTrain").click(function(){

				var retType = $('#returnTrain').val();
				if(retType === 1 || retType === '1') {
		    		// return
		    		var returnDate = $('#arivedDate').val();
		    	} else {
		    		// one way
		    		var returnDate = '';
		    	}

		    	if($('#departTrainCode').val() !== '' && $('#arivedTrainCode').val() !== '' && $('#class').val() !== '' && $('#departDate').val() !== '' && $('#adultPass').val() !== '0' || 
		    		$('#childPass').val() !== '0' || $('#invantPass').val() !== '0') {

		    		$('#popup').modal({backdrop: 'static', keyboard: false});

		    		// AJAX
			    	$.getJSON("<?php echo base_url(); ?>SearchTrain", 
			    	{
			    		departure: $('#departTrainCode').val(),
			    		arrival: $('#arivedTrainCode').val(),
			    		departureDate: $('#departDate').val(),
			    		class: $('#class').val(),
			    		returnDate: returnDate,
			    		adult: $('#adultPass').val(),
			    		child: $('#childPass').val(),
			    		invant: $('#invantPass').val()
			    	}, function (data) {

			    		var listDep = [];
			    		var listArr = [];
			    		if(data.error === 0) {
				    		$.each(data.datas.departures, function(key, val) {
				    			listDep.push("<tr>");
				    			listDep.push("<td><input type=radio value="+key+"_"+val.subclass+" name=pilDep id=pilDep></td>");
				    			listDep.push("<td>"+key+"</td>");
				    			listDep.push("<td>"+val.trainName+"</td>");
				    			listDep.push("<td>"+val.priceTotal+"</td>");
				    			listDep.push("</tr>");
				    		});

				    		$.each(data.datas.returns, function(key, val) {
				    			listArr.push("<tr>");
				    			listArr.push("<td><input type=radio value="+key+"_"+val.subclass+" name=pilArr id=pilArr></td>");
				    			listArr.push("<td>"+key+"</td>");
				    			listArr.push("<td>"+val.trainName+"</td>");
				    			listArr.push("<td>"+val.priceTotal+"</td>");
				    			listArr.push("</tr>");
				    		});

				    		$("<tbody/>", {html: listDep.join("")}).appendTo("#depatureData");
				    		$("<tbody/>", {html: listArr.join("")}).appendTo("#arrivalData");

				    		$('#popup').modal('hide');
				    	} else {
			    			alert('Data Tidak Ditemukan');
			    			window.location.reload(true);
			    		}

			    	});
			    } else {
			    	alert('Isi form terlebih dahulu');
			    }
			});

			$("#ChooseTrain").click(function() {

				var retType = $('#returnFlight').val();
				if(retType === 1 || retType === '1') {
		    		// return
		    		var returnDate = $('#arivedDate').val();
		    	} else {
		    		// one way
		    		var returnDate = '';
		    	}

				var dep = $('input[name=pilDep]:checked').val();
				var arr = $('input[name=pilArr]:checked').val();

				if(dep !== '' && arr !== '') {

					$('#popup').modal({backdrop: 'static', keyboard: false});

					var explDep = dep.split("_");
					var explArr = arr.split("_");

					// AJAX
			    	$.getJSON("<?php echo base_url(); ?>TrainAddOrder", 
			    	{
			    		departureCode: $('#departTrainCode').val(),
			    		arrivalCode: $('#arivedTrainCode').val(),
			    		departureDate: $('#departDate').val(),
			    		returnDate: returnDate,
			    		adult: $('#adultPass').val(),
			    		child: $('#childPass').val(),
			    		invant: $('#invantPass').val(),
			    		depTrainId: explDep[0],
			    		depSubclass: explDep[1],
			    		retTrainId: explArr[0],
			    		retSubclass: explArr[1]
			    	}, function (data) {

			    		if(data.error == 0) {
			    			// AJAX
					    	$.getJSON("<?php echo base_url(); ?>FlightAddOrder", 
					    	{
					    		depatureId: dep,
					    		returnId: arr,
					    		adult: $('#adultPass').val(),
					    		child: $('#childPass').val(),
					    		invant: $('#invantPass').val()
					    	}, function (res) {
					    		$('#dataPenumpang').val(res);
					    	});
			    		}

			    		$('#popup').modal('hide');

			    	});
			    } else {
			    	alert('PILIH PENERBANGAN');
			    }
			});

		});

		
	</script>
</body>
</html>