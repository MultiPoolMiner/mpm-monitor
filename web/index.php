<!DOCTYPE html>
<html lang="en">
<head>

  <?php if(file_exists("../thingsinhead.php")) {
    include("../thingsinhead.php");
  } ?>

  <title>MultiPoolMiner - Worker Monitor</title>
  <link rel="stylesheet" type="text/css" href="style.css"/>
  <?php if(file_exists("custom.css")) { ?><link rel="stylesheet" type="text/css" href="custom.css"/><?php } ?>
  <?php if (!empty($_GET["address"])) { ?><meta http-equiv="refresh" content="120"/><?php
    $cookie = $_GET["address"];
    setcookie("btcaddress", $cookie, strtotime('+30 days')); 
  }
  if (!empty($_GET["showdetail"])) {
	  $cookie2 = $_GET["showdetail"];
	  setcookie("showdetails", $cookie2, strtotime('+30 days'));
  } ?>
  
  <script src="https://code.jquery.com/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.1.3/mustache.js"></script>
  
  <?php if (!empty($_GET["address"])) { ?>
  <script type="text/javascript">
	function setTimer(num) {
	    var counter = setInterval(function () {
	        document.getElementById('timer').innerHTML = num;
	        num-- || clearInterval(counter);
	    }, 1000);
	}
	setTimer(120);  
  </script><?php } ?>

  <script id="newkey_template" type="text/template">
    <div class="generatestatuskey">
      Your new status key is: {{.}}<br/>
      <a href="index.php?address={{.}}">Your status page</a>
    </div>
  </script>

  <script id="total_profit_template" type="text/template">
    Total BTC/Day:<br/>
    <span class="profittext">{{.}}</span>
  </script>

  <script id="worker_template_basic" type="text/template">
    <div class="workers basic">
      {{#.}}
        <div class="worker workerbasic worker_{{workerstatus}}">
          <h3>{{workername}}</h3>
          <div class="statuscontainerbasic">
            <div class="workerstatus"><span class="label">Status:</span><span>{{workerstatus}}</div>
            <div class="lastseen"><span class="label">Last Seen:</span><span>{{timesincelastseen}}</div>
            <div class="btcday"><span class="label">BTC/Day:</span><span>{{profit}}</div>
          </div>
        </div>
      {{/.}}
    </div>
  </script>
            

  <script id="worker_template_detailed" type="text/template">
    <div class="workers detailed">
      {{#.}}
        <div class="worker workerdetailed worker_{{workerstatus}}">
          <h3>{{workername}}</h3>
          <div class="statuscontainer">
            <div class="workerstatus"><span class="label">Status:</span><span>{{workerstatus}}</div>
            <div class="lastseen"><span class="label">Last Seen:</span><span>{{timesincelastseen}}</div>
            <div class="btcday"><span class="label">BTC/Day:</span><span>{{profit}}</div>
          </div>
          <div class="activeminers">
            <table>
			  <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Pool</th>
                <th>Path</th>
                <th>Active</th>
                <th>Algorithm</th>
                <th>Current Speed</th>
                <th>Benchmark Speed</th>
                <th>PID</th>
                <th>BTC/day</th>
              </tr>
			  </thead>
			  <tbody>
              {{#miners}}
                <tr>
                  <td data-label="Name">{{Name}}</td>
                  <td data-label="Type">{{Type}}</td>
                  <td data-label="Pool">{{Pool}}</td>
                  <td data-label="Path">{{Path}}</td>
                  <td data-label="Active">{{Active}}</td>
                  <td data-label="Algorithm">{{Algorithm}}</td>
                  <td data-label="Current Speed">{{CurrentSpeed}}</td>
                  <td data-label="Benchmark Speed">{{EstimatedSpeed}}</td>
                  <td data-label="PID">{{PID}}</td>
                  <td data-label="BTC/day"><strong>{{BTC/day}}</strong></td>
                </tr>
				</tbody>
              {{/miners}}
            </table>
          </div>
        </div>
      {{/.}}
    </div>
  </script>


  <script type="text/javascript">
    function getUrlParameter(sParam) {
      var sPageURL = decodeURIComponent(window.location.search.substring(1)),sURLVariables = sPageURL.split('&'),sParameterName,i;
  
      for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
          return sParameterName[1] === undefined ? true : sParameterName[1];
        }
      }
    }

    function timeSince(date) {
      var seconds = Math.floor((new Date() - date) / 1000);
      var interval = Math.floor(seconds / 31536000);

      if (interval > 1) {
        return interval + " years ago";
      }
      interval = Math.floor(seconds / 2592000);
      if (interval > 1) {
        return interval + " months ago";
      }
      interval = Math.floor(seconds / 86400);
      if (interval > 1) {
        return interval + " days ago";
      }
      interval = Math.floor(seconds / 3600);
      if (interval > 1) {
        return interval + " hours ago";
      }
      interval = Math.floor(seconds / 60);
      if (interval > 1) {
        return interval + " minutes ago";
      }
      return Math.floor(seconds) + " seconds ago";
    }

    function convertHashrate(hashes) {
      hashes = parseFloat(hashes);
      if(hashes == 0) return '0 H/s';
      var sizes = ['H/s','KH/s','MH/s','GH/s','TH/s','PH/s','EH/s','ZH/s'];
      var i = Math.floor(Math.log(hashes)/Math.log(1000));
      return parseFloat((hashes/Math.pow(1000,i)).toFixed(2)) + ' ' + sizes[i];
    }

    function formatWorkers(json) {
    }

    function generateKey() {
      $.ajax({url: "generatekey.php", success: function (result) {
        if(result.hasOwnProperty('error')) {
          $("#workers_placeholder").empty();
          $("#workers_placeholder").append(result.error);
          return;
        }

        $("#workers_placeholder").empty();
        var template = $("#newkey_template").html();
        var html = Mustache.render(template, result);
        $("#workers_placeholder").append(html);
      }})
    }


    function updateStatus(address) {
      $.ajax({url: "stats.php?address=" + address, success: function(result) {

        // Check if error is set.  If it is, just display the error.
        if(result.hasOwnProperty('error')) {
          $("#workers_placeholder").empty();
          $("#workers_placeholder").append(result.error);
          return;
        }

        // Fix and add some extra information to the json
        var totalprofit = 0.0;
        var now = Math.round((new Date()).getTime() / 1000);
        for (i in result) {
          // Some versions of PHP/MySQL/PDO return strings instead of numbers
          result[i].lastseen = parseInt(result[i].lastseen);
          result[i].profit = parseFloat(result[i].profit);


          // Set the worker status
          if(result[i].lastseen > (now - 5*60)) {
            result[i].workerstatus = "Running";
            totalprofit += result[i].profit;
          } else {
            result[i].workerstatus = "Stopped";
          }
          // Set worker timesincelastseen
          result[i].timesincelastseen = timeSince(result[i].lastseen * 1000);
  
          // Round profit to 8 decimals
          result[i].profit = result[i].profit.toFixed(8);
  
          for (j in result[i].miners) {
            // Comma separate the strings
            result[i].miners[j].Algoritm = result[i].miners[j].Algoritm 
            // Round profit
            result[i].miners[j]["BTC/day"] = parseFloat(result[i].miners[j]["BTC/day"]).toFixed(8);
            // Convert hashrates to readable format, then comma separate
            for (k in result[i].miners[j].CurrentSpeed) {
              result[i].miners[j].CurrentSpeed[k] = convertHashrate(result[i].miners[j].CurrentSpeed[k]);
            }
            result[i].miners[j].CurrentSpeed = result[i].miners[j].CurrentSpeed.toString()
            for (k in result[i].miners[j].EstimatedSpeed) {
              result[i].miners[j].EstimatedSpeed[k] = convertHashrate(result[i].miners[j].EstimatedSpeed[k]);
            }
            result[i].miners[j].EstimatedSpeed = result[i].miners[j].EstimatedSpeed.toString()
          }
        }

        // Round total profit
        totalprofit = totalprofit.toFixed(8);
        // Clear #workers_placeholder, format according to the template and append
        $("#workers_placeholder").empty();

        if(showdetail) {
          var template = $("#worker_template_detailed").html();
        } else {
          var template = $("#worker_template_basic").html();
        }

        var html = Mustache.render(template, result);
        $("#workers_placeholder").append(html);

        // Update total profit
        $("#total_profit").empty();
        template = $("#total_profit_template").html();
        html = Mustache.render(template, totalprofit);
        $("#total_profit").append(html);
      }});
    }

    $(document).ready(function() {
      var address = getUrlParameter('address');

      var detail = getUrlParameter('showdetail');
      if(detail == "yes") { 
        showdetail = true 
      } else { 
        showdetail = false 
      }
      $("#showdetail").prop('checked', showdetail);

      if(address) {
        $("#address").val(address);
        updateStatus(address);
        window.setInterval(function() {updateStatus(address)}, 60000);
      }
    });

  </script>

</head>
<body>
<?php if(file_exists("../menu.php")) {
  include ("../menu.php");
} ?>
  <header class="masthead">
    <div class="overlay short">
      <div class="container">
        <h2 class="display-4 text-white"><img src="mpm_logo_c.png" width="80" /><br />MultiPoolMiner</h2>
        <h2 class="display-5 text-white">monitor your workers</h2>
      </div>
    </div>
  </header>
  <section>
	  <div class="container"><br />	  
		  <form id="addressform" method="GET">
			  <label>Status Key: 
        <?php $statusurl = ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            if(substr($statusurl, -1) == '/') {
              $statusurl .= "miner.php";
            } else {
              $statusurl .= "/miner.php";
            }
        ?>
			  <sup><img src="data:image/png;base64,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" width="16" data-toggle="tooltip" title="Please add the following command to your start.bat file for each mining rig you want to monitor: -minerstatusurl <?php echo $statusurl?>" /></sup> 
    		<input id="address" type="text" name="address" value="<?php echo $_COOKIE["btcaddress"]; ?>"/></label>
      	<label>Show details 
    		<input id="showdetail" type="checkbox" name="showdetail" value="yes" <?php if ($_COOKIE["showdetails"] == "yes") { echo 'checked'; } ?>/></label>
  		  <input type="submit" class="submit-button" value="Submit"/> <?php if (!empty($_GET["address"])) { ?><br /><center><span style="font-size:smaller;color:#FFF;">Refreshing in </span><span style="font-weight:bold;font-size:smaller;color:#FFF;" id="timer"></span><span style="font-size:smaller;color:#FFF;"> seconds </span></center><?php } ?>
  		</form> 
      <div class="total_profit" id="total_profit"></div>
	  	<div class="workers_placeholder" id="workers_placeholder">
  	  	<div class="generatestatuskey">Don't have a status key?  <a href="#" onclick="generateKey()">Generate a new one</a></div>
      </div>
	  </div>
  </section>
<?php if(file_exists("../footer.php")) {
  include("../footer.php"); 
} ?>
</body>
</html>
