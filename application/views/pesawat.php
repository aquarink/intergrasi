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
	<title>Abu Nawas - Pesawat</title>

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
					<li class="active"><a href="<?php echo base_url() ?>pesawat">Pesawat</a></li>
					<li><a href="<?php echo base_url() ?>keretaapi">Kereta APi</a></li>
					<li><a href="<?php echo base_url() ?>hotel">Hotel</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade active in">
						<h4>Cari Penerbangan</h4>
						
						<div class="col-md-6">
							<form class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
									<input type="text" class="form-control" id="departAirport" name="departAirport" placeholder="From : Airport Name or Alias">
									<input type="hidden" name="departAirportCode" id="departAirportCode">
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
									<input type="text" class="form-control" id="arivedAirport" name="arivedAirport" placeholder="To : Airport Name or Alias">
									<input type="hidden" name="arivedAirportCode" id="arivedAirportCode">
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
											<select class="form-control" name="returnFlight" id="returnFlight">
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
											<a id="SearchFlight" class="btn btn-success">Search Flight</a>
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
										<th>Flight ID</th>
										<th>Airlinest</th>
										<th>Price</th>
										<th>No.Flight</th>
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
										<th>Flight ID</th>
										<th>Airlinest</th>
										<th>Price</th>
										<th>No.Flight</th>
									</tr>
								</thead>

								<tbody></tbody>
							</table>

							<br>

							<!-- <a id="ChooseFlight" class="btn btn-success">Choose Flight</a> -->
						</div>

						<div class="col-md-12">
							<div class="col-md-6">
								<h3>Simulasi Input Data Penumpang</h3>
								<textarea class="form-control" id="dataPenumpang">&conSalutation=Mrs&conFirstName=budianto&conLastName=wijaya&conPhone=%2B6287880182218&conEmailAddress=you_julin@yahoo.com&conOtherPhone=%2B628521342534&firstnamea1=susi&lastnamea1=wijaya&birthdatea1=1990-02-09&ida1=&titlea1=Mr&firstnamec1=carreen&lastnamec1=athalia&birthdatec1=2005-02-02&idc1&titlei1=Mr&firstnamei1=wendy&lastnamei1=suprato&birthdatei1=2011-06-29&idi1&parenti1=&passportnoa1&passportExpiryDatea1=2020-09-02&passportissueddatea1=2015-0902&passportissuinga1&passportnationalitya1=id&passportnoc1&passportExpiryDatec1&passportissueddatec1&birthdatec1&passportissuingc1&passportnationalityc1&passportnoe1&passportExpiryDatee1&passportissueddatee1&birthdatee1&passportissuinge1&passportnationalitye1&dcheckinbaggagea11&dcheckinbaggagec11&dcheckinbaggagee11&rcheckinbaggagea11&rcheckinbaggagec11&rcheckinbaggagee11</textarea>
								<a id="OrderFlight" class="btn btn-warning">Order Flight</a>
							</div>
						</div>

						<div class="col-md-12">
							<h3>Hotel Detail</h3>
							<table id="orderDetail" class="table table-bordered table-hover table-striped">
								<thead>
									<tr>
										<th>Hapus Order</th>
										<th>orderDetailId</th>
										<th>orderExpireDatetime</th>
										<th>orderName</th>
										<th>orderNameDetail</th>
										<th>airlinesName</th>
										<th>flightNumber</th>
										<th>priceAdult</th>
										<th>priceChild Date</th>
										<th>priceInfant</th>
										<th>departureAirport</th>
										<th>passengers</th>
										<th>Total Price</th>
										<th>breakdownPrice</th>
									</tr>
								</thead>

								<tbody>

								</tbody>
							</table>

							<br>

							<h3>Checkout</h3>
							<table id="hotelCheckout" class="table table-bordered table-hover table-striped">
								<tbody>

								</tbody>
							</table>
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

			$('#departAirport').autocomplete({
				dataType: "json",
				autoFocus: true,
				minLength: 3,
				select: function (event, ui) {
					$(this).val(ui.item.label);
					$("#departAirportCode").val(ui.item.code);
				},
				source: function (request, response) {
					$.getJSON("<?php echo base_url(); ?>SearchAirport", { search: $('#departAirport').val() },
					function (data) {
						response($.map(data, function (value, key) {
							return {
								label: value.airport_name,
								value: value.airport_name,
								code: value.airport_code
							};
						}));
					});
				}
			});

			$('#arivedAirport').autocomplete({
				dataType: "json",
				autoFocus: true,
				minLength: 3,
				select: function (event, ui) {
					$(this).val(ui.item.label);
					$("#arivedAirportCode").val(ui.item.code);
				},
				source: function (request, response) {
					$.getJSON("<?php echo base_url(); ?>SearchAirport", { search: $('#arivedAirport').val() },
						function (data) {
							response($.map(data, function (value, key) {
								return {
									label: value.airport_name,
									value: value.airport_name,
									code: value.airport_code
								};
							}));
						});
				}
			});

			$( "#departDate" ).datepicker({ dateFormat: 'yy-mm-dd' });

			$( "#arivedDate" ).datepicker({ dateFormat: 'yy-mm-dd' });

			$("#SearchFlight").click(function(){

				var retType = $('#returnFlight').val();
				if(retType === 1 || retType === '1') {
		    		// return
		    		var returnDate = $('#arivedDate').val();
		    	} else {
		    		// one way
		    		var returnDate = '';
		    	}

		    	if($('#departAirportCode').val() !== '' && $('#arivedAirportCode').val() !== '' && 
		    		$('#departDate').val() !== '' && $('#adultPass').val() !== '0' || 
		    		$('#childPass').val() !== '0' || $('#invantPass').val() !== '0') {

		    		$('#popup').modal({backdrop: 'static', keyboard: false});

		    		// AJAX
			    	$.getJSON("<?php echo base_url(); ?>SearchFlight", 
			    	{
			    		depature: $('#departAirportCode').val(),
			    		arrival: $('#arivedAirportCode').val(),
			    		depatureDate: $('#departDate').val(),
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
				    			listDep.push("<td><input type=radio value="+val.flight_id+" name=pilDep id=pilDep></td>");
				    			listDep.push("<td>"+val.flight_id+"</td>");
				    			listDep.push("<td>"+val.airlines_name+"</td>");
				    			listDep.push("<td>"+val.price.price_value+"</td>");
				    			listDep.push("<td>"+val.flight_number+"</td>");
				    			listDep.push("</tr>");
				    		});

				    		$.each(data.datas.returns, function(key, val) {
				    			listArr.push("<tr>");
				    			listArr.push("<td><input type=radio value="+val.flight_id+" name=pilArr id=pilArr></td>");
				    			listArr.push("<td>"+val.flight_id+"</td>");
				    			listArr.push("<td>"+val.airlines_name+"</td>");
				    			listArr.push("<td>"+val.price.price_value+"</td>");
				    			listArr.push("<td>"+val.flight_number+"</td>");
				    			listArr.push("</tr>");
				    		});
			    		} else {
			    			alert('Data Tidak Ditemukan');
			    			window.location.reload(true);
			    		}

			    		

			    		$("<tbody/>", {html: listDep.join("")}).appendTo("#depatureData");
			    		$("<tbody/>", {html: listArr.join("")}).appendTo("#arrivalData");

			    		$('#popup').modal('hide');

			    	});
			    } else {
			    	alert('Isi form terlebih dahulu');
			    }
			});

			$("#OrderFlight").click(function() {

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

					// AJAX
			    	$.getJSON("<?php echo base_url(); ?>GetFlightData", 
			    	{
			    		depatureId: dep,
			    		depatureDate: $('#departDate').val(),
			    		returnId: arr,
			    		returnDate: returnDate
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

					    		$('#popup').modal('hide');
					    	});
			    		}
			    	});
			    } else {
			    	alert('PILIH PENERBANGAN');
			    }
			});

			$("#OrderFlightsss").click(function() {

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

				var order = $('#dataPenumpang').val();

				if(order !== '') {

					$('#popup').modal({backdrop: 'static', keyboard: false});

					// AJAX
			    	$.getJSON("<?php echo base_url(); ?>GetFlightData", 
			    	{
			    		depatureId: dep,
			    		depatureDate: $('#departDate').val(),
			    		returnId: arr,
			    		returnDate: returnDate
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

					    		var listDep = [];

								$.each(res.datas, function(k, v) {
									$.each(v.list, function(ky, vl) {
										listDep.push("<tr>");
										// listDep.push("<td><a href='<?php echo base_url(); ?>HotelDeleteOrder?delete="+vl.deleteOrder+"'>Delete</a></td>");
										listDep.push("<td><a onClick=deleteOrder('"+vl.deleteOrder+"')>Delete</a></td>");
										listDep.push("<td>"+vl.orderDetailId+"</td>");
										listDep.push("<td>"+vl.orderExpireDatetime+"</td>");
										listDep.push("<td>"+vl.orderName+"</td>");
										listDep.push("<td>"+vl.orderNameDetail+"</td>");
										listDep.push("<td>"+vl.airlinesName+"</td>");
										listDep.push("<td>"+vl.flightNumber+"</td>");
										listDep.push("<td>"+vl.priceAdult+"</td>");
										listDep.push("<td>"+vl.priceChild+"</td>");
										listDep.push("<td>"+vl.priceInfant+"</td>");
										listDep.push("<td>"+vl.departureAirport+"</td>");
										listDep.push("<td>"+vl.arrivalAirport+"</td>");

										$.each(vl.passengers, function(key, val) {
											listDep.push("<td>"+val+"</td>");
										});

										listDep.push("<td>"+vl.totalPrice+"</td>");

										$.each(vl.breakdownPrice, function(key, val) {
											listDep.push("<td>"+val+"</td>");
										});

										listDep.push("</tr>");
									});
								});

								$("<tbody/>", {html: listDep.join("")}).appendTo("#orderDetail");

					    		$('#popup').modal('hide');
					    	});
			    		}
			    	});
			    }
			});

		});

		
	</script>
</body>
</html>