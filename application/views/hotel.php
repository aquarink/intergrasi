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
	<title>Abu Nawas - Hotel</title>

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
					<li><a href="<?php echo base_url() ?>keretaapi">Kereta APi</a></li>
					<li class="active"><a href="<?php echo base_url() ?>hotel">Hotel</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade active in">
						<h4>Cari Hotel</h4>
						
						<form class="form-group">
							<div class="col-md-6">							
								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
									<input type="text" class="form-control" id="SearchHotel" name="SearchHotel" placeholder="Search Hotel">
									<input type="" id="SearchHotelBusinessUri" name="SearchHotelBusinessUri">
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
									<input type="text" class="form-control" id="checkinDate" name="checkinDate" placeholder="Checkin Date">
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
									<input type="number" class="form-control" id="night" name="night" placeholder="Night (in number)">
								</div>

								<br>

								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
									<select class="form-control" id="room" name="room">
										<option value="0">Room</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
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
											<a id="SearchHotels" class="btn btn-success">SearchH otel</a>
										</div>
									</div>
								</div>

							</div>

							<div class="col-md-6">
								<h3>Hotel List</h3>
								<table id="hotelData" class="table table-bordered table-hover table-striped">
									<thead>
										<tr>
											<th>Pilih</th>
											<th>Hotel ID</th>
											<th>Hotel Name</th>
											<th>Price</th>
											<th>Room Image</th>
											<th>Room Facility</th>
										</tr>
									</thead>

									<tbody>

									</tbody>
								</table>

								<br><br>

								<a id="ChooseHotel" class="btn btn-success">Choose Hotel</a>
							</div>

						</form>

						<div class="col-md-12">
							<h3>Hotel Detail</h3>
							<table id="hotelDetail" class="table table-bordered table-hover table-striped">
								<thead>
									<tr>
										<th>Hapus Order</th>
										<th>Order Detail Id</th>
										<th>Hotel Name</th>
										<th>Hotel Name Detail</th>
										<th>Room ID</th>
										<th>Room Amount</th>
										<th>Adult</th>
										<th>Child</th>
										<th>Start Date</th>
										<th>End Date</th>
										<th>Night Amount</th>
										<th>Price</th>
										<th>Room Image</th>
									</tr>
								</thead>

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

			$("#checkinDate").datepicker({ dateFormat: 'yy-mm-dd' });

			$('#SearchHotel').autocomplete({
				dataType: "json",
				autoFocus: true,
				minLength: 3,
				select: function (event, ui) {
					$(this).val(ui.item.label);
					$("#SearchHotelBusinessUri").val(ui.item.uri);
					
				},
				source: function (request, response) {
					$.getJSON("<?php echo base_url(); ?>SearchAutocomplete", { search: $('#SearchHotel').val() },function (data) {
						response($.map(data, function (value, key) {
							return {
								label: key.name,
								value: value.name,
								uri: value.business_uri
							};
						}));
					});
				}
			});

			$("#SearchHotels").click(function() {

				if($('#SearchHotelBusinessUri').val() !== '' && $('#checkinDate').val() !== '' && 
					$('#night').val() !== '' && $('#room').val() !== '' && $('#adultPass').val() !== '0' || 
					$('#childPass').val() !== '0' || $('#invantPass').val() !== '0') {

					$('#popup').modal({backdrop: 'static', keyboard: false});				

					$.getJSON($("#SearchHotelBusinessUri").val(), { 
						checkin: $('#checkinDate').val(),
						night: $('#night').val(),
						room: $('#room').val(),
						adult: $('#adultPass').val(),
						child: $('#childPass').val(),
						invant: $('#invantPass').val()
					}, function (data) {
						var listDep = [];
						var listArr = [];

						$.each(data, function(key, val) {
							listDep.push("<tr>");
							listDep.push("<td><input type=radio value="+ val.room_id +"_"+ $('#checkinDate').val() +"_"+ $('#night').val() +"_"+ $('#adultPass').val() +"_"+ $('#childPass').val() +"_"+ $('#invantPass').val() +"_"+ $('#room').val() +" name=pilHotel id=pilHotel></td>");
							listDep.push("<td>"+val.room_id+"</td>");
							listDep.push("<td>"+val.business_name+"</td>");
							listDep.push("<td>"+val.price+"</td>");

							$.each(val.room_image, function(k, v) {
								listDep.push("<td><img src='"+v+"' ></td>");
							});

							$.each(val.room_facility, function(k, v) {
								listDep.push("<td>"+v+"</td>");
							});

							listDep.push("</tr>");
						});

						$("<tbody/>", {html: listDep.join("")}).appendTo("#hotelData");

						$('#popup').modal('hide');

					});
				} else {
					alert('Isi form terlebih dahulu');
				}
			});

			$("#ChooseHotel").click(function() {
				// if($('input[name=pilHotel]:checked').val() === '' || $('input[name=pilHotel]:checked').val() === undefined) {
				// 	alert('Pilih Kamar');
				// } else {
					$('#popup').modal({backdrop: 'static', keyboard: false});

					var hotelSelect = $('input[name=pilHotel]:checked').val();

					$.getJSON("<?php echo base_url(); ?>HotelAddOrder",  { 
						// hotel: hotelSelect
						hotel: "772101_2018-05-30_1_1_0_0_1"
					}, function (data) {
						// console.log(data);
						// if(data.error === 211) {
						// 	$('#popup').modal('hide');
						// 	alert(data.msg);
						// } else if(data.error === 200) {
						// 	$('#popup').modal('hide');
						// 	alert('Success');
						// }

						var listDep = [];
						var listArr = [];

						$.each(data.data, function(k, v) {
							$.each(v.list, function(ky, vl) {
								listDep.push("<tr>");
								// listDep.push("<td><a href='<?php echo base_url(); ?>HotelDeleteOrder?delete="+vl.deleteOrder+"'>Delete</a></td>");
								listDep.push("<td><a onClick=deleteOrder('"+vl.deleteOrder+"')>Delete</a></td>");
								listDep.push("<td>"+vl.orderDetailId+"</td>");
								listDep.push("<td>"+vl.orderName+"</td>");
								listDep.push("<td>"+vl.orderNameDetail+"</td>");
								listDep.push("<td>"+vl.roomId+"</td>");
								listDep.push("<td>"+vl.rooms+"</td>");
								listDep.push("<td>"+vl.adult+"</td>");
								listDep.push("<td>"+vl.child+"</td>");
								listDep.push("<td>"+vl.startdate+"</td>");
								listDep.push("<td>"+vl.enddate+"</td>");
								listDep.push("<td>"+vl.nights+"</td>");
								listDep.push("<td>"+vl.price+"</td>");
								listDep.push("<td><img src='"+vl.orderPhoto+"' ></td>");
								listDep.push("</tr>");
							});

							listDep.push("<tr>");
							listDep.push("<td colspan=3>"+v.total+"</td>");
							listDep.push("<td colspan=10><input type='text' id='coUrl' value='"+v.checkoutUrl+"'></td>");
							listDep.push("</tr>");
						});

						$("<tbody/>", {html: listDep.join("")}).appendTo("#hotelDetail");

						$('#popup').modal('hide');

					});
				// }
			});

		});

		function deleteOrder(url) {
			$('#popup').modal({backdrop: 'static', keyboard: false});

			$.getJSON("<?php echo base_url(); ?>HotelDeleteOrder",  { 
					// hotel: hotelSelect
					delete: url
				}, function (response) {
				if(response.error === 200) {

					$('#hotelDetail').empty();
					
					var hotelSelect = $('input[name=pilHotel]:checked').val();

					$.getJSON("<?php echo base_url(); ?>HotelAddOrder",  { 
						// hotel: hotelSelect
						hotel: "772101_2018-05-30_1_1_0_0_1"
					}, function (data) {
						// console.log(data);
						// if(data.error === 211) {
						// 	$('#popup').modal('hide');
						// 	alert(data.msg);
						// } else if(data.error === 200) {
						// 	$('#popup').modal('hide');
						// 	alert('Success');
						// }

						var listDep = [];
						var listArr = [];

						$.each(data.data, function(k, v) {
							$.each(v.list, function(ky, vl) {
								listDep.push("<tr>");
								// listDep.push("<td><a href='<?php echo base_url(); ?>HotelDeleteOrder?delete="+vl.deleteOrder+"'>Delete</a></td>");
								listDep.push("<td><a onClick=deleteOrder('"+vl.deleteOrder+"')>Delete</a></td>");
								listDep.push("<td>"+vl.orderDetailId+"</td>");
								listDep.push("<td>"+vl.orderName+"</td>");
								listDep.push("<td>"+vl.orderNameDetail+"</td>");
								listDep.push("<td>"+vl.roomId+"</td>");
								listDep.push("<td>"+vl.rooms+"</td>");
								listDep.push("<td>"+vl.adult+"</td>");
								listDep.push("<td>"+vl.child+"</td>");
								listDep.push("<td>"+vl.startdate+"</td>");
								listDep.push("<td>"+vl.enddate+"</td>");
								listDep.push("<td>"+vl.nights+"</td>");
								listDep.push("<td>"+vl.price+"</td>");
								listDep.push("<td><img src='"+vl.orderPhoto+"' ></td>");
								listDep.push("</tr>");
							});

							listDep.push("<tr>");
							listDep.push("<td colspan=3>"+v.total+"</td>");
							listDep.push("<td colspan=10><input type='text' id='coUrl' value='"+v.checkoutUrl+"'></td>");
							listDep.push("</tr>");
						});

						$("<tbody/>", {html: listDep.join("")}).appendTo("#hotelDetail");

						$('#popup').modal('hide');

					});
				}								
			});
		}


	</script>
</body>
</html>