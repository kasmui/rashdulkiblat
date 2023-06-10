<?php
//
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Rashdul Kiblat</title>
    <meta name="description" content="Penentuan Arah Kiblat" />
    <link rel="shortcut icon" href="https://blogchem.com/moonphase/moon.png">
    <link rel="stylesheet" type="text/css" href="style.css" media="screen" />
  <link rel="shortcut icon" href="kalender.png">
  <meta charset="utf-8">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
    /* Set height of the grid so .sidenav can be 100% (adjust if needed) */
    .row.content {height: auto}
    
    /* Set gray background color and 100% height */
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
    }
    
    /* Set black background color, white text and some padding */
    footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height: auto;} 
    }
        table {
          border: 1px solid #5f8287;
          border-collapse: collapse;
        }
        input, select {
              margin: 5px;
              height: 30px;
              color: black;
            }        
        td {
          background-color: #eeeeee;
          padding: 3px 3px;
          color: blue;
        }

        fieldset {
          background-color: #f0f7f7;//#f6f2f7
          padding: 0px 0px;
          text-align: center;
        }
        
        legend {
          background-color:#daeff0 ; //#edf7f6
          color: darkgreen;
          text-align: center;
        }
        
        hr {
            border-color: orange;
            color: black;
        }    
  </style>
  <style>
#myBtn {
  display: none;
  position: fixed;
  bottom: 20px;
  right: 30px;
  z-index: 99;
  font-size: 18px;
  border: none;
  outline: none;
  background-color: red;
  color: white;
  cursor: pointer;
  padding: 15px;
  border-radius: 4px;
}

#myBtn:hover {
  background-color: #555;
}    
</style>

<style> 
#radius1 {
    border: 1px solid grey;
    padding: 3px;
    border-radius: 8px;
}

#radius2 {
    border: 2px solid red;
    padding: 10px;
    border-radius: 50px 20px;
}
</style>
</head>

<body>
<div align="center" id='wrap'>
<div align="center" id='content'>

<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyAATj04FZVxMmpwYZm05sLxKzUd7YVb8Jw&sensor=false"></script>
<script type="text/javascript">
var _map, _marker1, _lineDirKib, _lineDirSol, _qiblaDir, _solarDir, _solarEl, _solNoonLocal
var _solMarker, _geocoder;
var _eqTime, _theta, _solarLng
var _qiblaLat = 21.42252; var _qiblaLng = 39.82621
var _qiblaT1, _qiblaT2, _qiblaLoc1, _qiblaLoc2, _winref
var _docday, _docmonth, _docyear, _dochr, _docmn, _docsc, _tzone
var _lat, _lng
var _tanggal = new Date()
var _but1, _but2

function calcTimeJulianCent(jd)
{
  var T = (jd - 2451545.0)/36525.0
  return T
}

function calcJDFromJulianCent(t)
{
  var JD = t * 36525.0 + 2451545.0
  return JD
}

function isLeapYear(yr) 
{
  return ((yr % 4 == 0 && yr % 100 != 0) || yr % 400 == 0);
}

function radToDeg(angleRad) 
{
  return (180.0 * angleRad / Math.PI);
}

function degToRad(angleDeg) 
{
  return (Math.PI * angleDeg / 180.0);
}

function calcGeomMeanLongSun(t)
{
  var L0 = 280.46646 + t * (36000.76983 + t*(0.0003032))
  while(L0 > 360.0)
  {
    L0 -= 360.0
  }
  while(L0 < 0.0)
  {
    L0 += 360.0
  }
  return L0		// in degrees
}

function calcGeomMeanAnomalySun(t)
{
  var M = 357.52911 + t * (35999.05029 - 0.0001537 * t);
  return M;		// in degrees
}

function calcEccentricityEarthOrbit(t)
{
  var e = 0.016708634 - t * (0.000042037 + 0.0000001267 * t);
  return e;		// unitless
}

function calcSunEqOfCenter(t)
{
  var m = calcGeomMeanAnomalySun(t);
  var mrad = degToRad(m);
  var sinm = Math.sin(mrad);
  var sin2m = Math.sin(mrad+mrad);
  var sin3m = Math.sin(mrad+mrad+mrad);
  var C = sinm * (1.914602 - t * (0.004817 + 0.000014 * t)) + sin2m * (0.019993 - 0.000101 * t) + sin3m * 0.000289;
  return C;		// in degrees
}

function calcSunTrueLong(t)
{
  var l0 = calcGeomMeanLongSun(t);
  var c = calcSunEqOfCenter(t);
  var O = l0 + c;
  return O;		// in degrees
}

function calcSunTrueAnomaly(t)
{
  var m = calcGeomMeanAnomalySun(t);
  var c = calcSunEqOfCenter(t);
  var v = m + c;
  return v;		// in degrees
}

function calcSunRadVector(t)
{
  var v = calcSunTrueAnomaly(t);
  var e = calcEccentricityEarthOrbit(t);
  var R = (1.000001018 * (1 - e * e)) / (1 + e * Math.cos(degToRad(v)));
  return R;		// in AUs
}

function calcSunApparentLong(t)
{
  var o = calcSunTrueLong(t);
  var omega = 125.04 - 1934.136 * t;
  var lambda = o - 0.00569 - 0.00478 * Math.sin(degToRad(omega));
  return lambda;		// in degrees
}

function calcMeanObliquityOfEcliptic(t)
{
  var seconds = 21.448 - t*(46.8150 + t*(0.00059 - t*(0.001813)));
  var e0 = 23.0 + (26.0 + (seconds/60.0))/60.0;
  return e0;		// in degrees
}

function calcObliquityCorrection(t)
{
  var e0 = calcMeanObliquityOfEcliptic(t);
  var omega = 125.04 - 1934.136 * t;
  var e = e0 + 0.00256 * Math.cos(degToRad(omega));
  return e;		// in degrees
}

function calcSunRtAscension(t)
{
  var e = calcObliquityCorrection(t);
  var lambda = calcSunApparentLong(t);
  var tananum = (Math.cos(degToRad(e)) * Math.sin(degToRad(lambda)));
  var tanadenom = (Math.cos(degToRad(lambda)));
  var alpha = radToDeg(Math.atan2(tananum, tanadenom));
  return alpha;		// in degrees
}

function calcSunDeclination(t)
{
  var e = calcObliquityCorrection(t);
  var lambda = calcSunApparentLong(t);

  var sint = Math.sin(degToRad(e)) * Math.sin(degToRad(lambda));
  var theta = radToDeg(Math.asin(sint));
  return theta;		// in degrees
}

function calcEquationOfTime(t)
{
  var epsilon = calcObliquityCorrection(t);
  var l0 = calcGeomMeanLongSun(t);
  var e = calcEccentricityEarthOrbit(t);
  var m = calcGeomMeanAnomalySun(t);

  var y = Math.tan(degToRad(epsilon)/2.0);
  y *= y;

  var sin2l0 = Math.sin(2.0 * degToRad(l0));
  var sinm   = Math.sin(degToRad(m));
  var cos2l0 = Math.cos(2.0 * degToRad(l0));
  var sin4l0 = Math.sin(4.0 * degToRad(l0));
  var sin2m  = Math.sin(2.0 * degToRad(m));

  var Etime = y * sin2l0 - 2.0 * e * sinm + 4.0 * e * y * sinm * cos2l0 - 0.5 * y * y * sin4l0 - 1.25 * e * e * sin2m;
  return radToDeg(Etime)*4.0;	// in minutes of time
}

function zeroPad(n, digits) {
  n = n.toString();
  while (n.length < digits) {
    n = '0' + n;
  }
  return n;
}

function readTextBox(inputId, numchars, intgr, pad, min, max, def)
{
  var number = document.getElementById(inputId).value.substring(0,numchars)
  if (intgr) {
    number = Math.floor(parseFloat(number))
  } else {  // float
    number = parseFloat(number)
  }
  if (number < min) {
    number = min
  } else if (number > max) {
    number = max
  } else if (number.toString() == "NaN") {
    number = def
  }
  if ((pad) && (intgr)) {
    document.getElementById(inputId).value = zeroPad(number,2)
  } else {
    document.getElementById(inputId).value = number
  }
  return number
}

function month(name, numdays, abbr) 
{
  this.name = name;
  this.numdays = numdays;
  this.abbr = abbr;
}
var monthList = new Array();	
var i = 0;
monthList[i++] = new month("Januari", 31, "Jan");
monthList[i++] = new month("Februari", 28, "Feb");
monthList[i++] = new month("Maret", 31, "Mar");
monthList[i++] = new month("April", 30, "Apr");
monthList[i++] = new month("Mei", 31, "Mei");
monthList[i++] = new month("Juni", 30, "Jun");
monthList[i++] = new month("Juli", 31, "Jul");
monthList[i++] = new month("Agustus", 31, "Agu");
monthList[i++] = new month("September", 30, "Sep");
monthList[i++] = new month("Oktober", 31, "Okt");
monthList[i++] = new month("November", 30, "Nov");
monthList[i++] = new month("Desember", 31, "Des");


function JulianDay(day, month, year)
{
   if (month <= 2) {
    year -= 1
    month += 12
  }
  
  var A = Math.floor(year/100)
  var B = 2 - A + Math.floor(A/4)
  var Julian = Math.floor(365.25*(year + 4716)) + Math.floor(30.6001*(month+1)) + day + B - 1524.5
  return Julian
}

function getTimeLocal()
{
  var mins = _dochr * 60 + _docmn + _docsc/60.0
  return mins
}

function calcAzEl(T, localtime, latitude, longitude, zone)
{
  _eqTime = calcEquationOfTime(T)
  _theta  = calcSunDeclination(T)
		
  var solarTimeFix = _eqTime + 4.0 * longitude - 60.0 * zone
  var earthRadVec = calcSunRadVector(T)
  var trueSolarTime = localtime + solarTimeFix
  
  _solarLng = 180 - trueSolarTime/4.0 + longitude
  while (_solarLng < -180){_solarLng += 360}
  while (_solarLng > 180){_solarLng -= 360}
  
  while (trueSolarTime > 1440)
  {
    trueSolarTime -= 1440
  }
  var hourAngle = trueSolarTime / 4.0 - 180.0;
  if (hourAngle < -180) 
  {
    hourAngle += 360.0
  }
  var haRad = degToRad(hourAngle)
  var csz = Math.sin(degToRad(latitude)) * Math.sin(degToRad(_theta)) + Math.cos(degToRad(latitude)) * Math.cos(degToRad(_theta)) * Math.cos(haRad)
  if (csz > 1.0) 
  {
    csz = 1.0
  } else if (csz < -1.0) 
  { 
    csz = -1.0
  }
  var zenith = radToDeg(Math.acos(csz))
  var azDenom = ( Math.cos(degToRad(latitude)) * Math.sin(degToRad(zenith)) )
  if (Math.abs(azDenom) > 0.001) {
    azRad = (( Math.sin(degToRad(latitude)) * Math.cos(degToRad(zenith)) ) - Math.sin(degToRad(_theta))) / azDenom
    if (Math.abs(azRad) > 1.0) {
      if (azRad < 0) {
	azRad = -1.0
      } else {
	azRad = 1.0
      }
    }
    var azimuth = 180.0 - radToDeg(Math.acos(azRad))
    if (hourAngle > 0.0) {
      azimuth = -azimuth
    }
  } else {
    if (latitude > 0.0) {
      azimuth = 180.0
    } else { 
      azimuth = 0.0
    }
  }
  if (azimuth < 0.0) {
    azimuth += 360.0
  }
  var exoatmElevation = 90.0 - zenith

// Atmospheric Refraction correction

  if (exoatmElevation > 85.0) {
    var refractionCorrection = 0.0;
  } else {
    var te = Math.tan (degToRad(exoatmElevation));
    if (exoatmElevation > 5.0) {
      var refractionCorrection = 58.1 / te - 0.07 / (te*te*te) + 0.000086 / (te*te*te*te*te);
    } else if (exoatmElevation > -0.575) {
      var refractionCorrection = 1735.0 + exoatmElevation * (-518.2 + exoatmElevation * (103.4 + exoatmElevation * (-12.79 + exoatmElevation * 0.711) ) );
    } else {
      var refractionCorrection = -20.774 / te;
    }
    refractionCorrection = refractionCorrection / 3600.0;
  }

  var solarZen = zenith - refractionCorrection;

  if (solarZen > 108.0) {
	_solarDir = "gelap"
	_solarEl = "gelap"
  } else {
    _solarDir = azimuth
	_solarEl = 90.0-solarZen
  }
}

function calcSolNoon(jd, longitude, timezone)
{
  var tnoon = calcTimeJulianCent(jd - longitude/360.0)
  var eqTime = calcEquationOfTime(tnoon)
  var solNoonOffset = 720.0 - (longitude * 4) - eqTime // in minutes
  var newt = calcTimeJulianCent(jd + solNoonOffset/1440.0)
  eqTime = calcEquationOfTime(newt)
  _solNoonLocal = 720 - (longitude * 4) - eqTime + (timezone*60.0)// in minutes
}

function minute_kiblat(latRad, dekRad)
{
   _qiblaT1 = "nihil"; _qiblaT2 = "nihil"
   var azimRad = degToRad(_qiblaDir)
   var a = Math.cos(dekRad)*Math.cos(azimRad)
   var b = Math.sin(dekRad)*Math.cos(latRad)*Math.sin(azimRad)
   var c = Math.cos(dekRad)*Math.sin(latRad)*Math.sin(azimRad)
   var r = Math.sqrt(a*a + c*c)
   var alpa = Math.atan2(a,c)
   var ratio = b/r
   
       
   if (Math.abs(ratio) < 1)
   {
     var beta =  Math.acos(ratio)
	 
	 var hourRad = beta - alpa
     var z = Math.sin(dekRad)*Math.sin(latRad) + Math.cos(dekRad)*Math.cos(latRad)*Math.cos(hourRad)
	 var x = Math.cos(dekRad)*Math.sin(hourRad)
	 var y = Math.sin(dekRad)*Math.cos(latRad) - Math.cos(dekRad)*Math.sin(latRad)*Math.cos(hourRad)
	 var azimShadow = - Math.atan2(x,y)
	 while (azimShadow < 0) {azimShadow += 2*Math.PI}
	 var delta = Math.abs(azimRad - azimShadow)
	 while (hourRad > Math.PI) {hourRad -= 2*Math.PI}
	 while (hourRad < -Math.PI) {hourRad += 2*Math.PI}
	 
	 if ( (z < 0.97) && (z > 0))
	   {if (delta < 0.01) {_qiblaT1 = hourRad*720/Math.PI}
	    if (Math.abs(delta - Math.PI) < 0.01) {_qiblaT2 = hourRad*720/Math.PI}}
	     
	 hourRad = -beta - alpa
	 z = Math.sin(dekRad)*Math.sin(latRad) + Math.cos(dekRad)*Math.cos(latRad)*Math.cos(hourRad)
	 x = Math.cos(dekRad)*Math.sin(hourRad)
	 y = Math.sin(dekRad)*Math.cos(latRad) - Math.cos(dekRad)*Math.sin(latRad)*Math.cos(hourRad)
	 azimShadow = - Math.atan2(x,y)
	 while (azimShadow < 0) {azimShadow += 2*Math.PI}
	 delta = Math.abs(azimRad - azimShadow)
	 while (hourRad > Math.PI) {hourRad -= 2*Math.PI}
	 while (hourRad < -Math.PI) {hourRad += 2*Math.PI}
	 
	  if ( (z < 0.97) && (z > 0))
	   {if (delta < 0.01) {_qiblaT1 = hourRad*720/Math.PI}
	    if (Math.abs(delta - Math.PI) < 0.01) {_qiblaT2 = hourRad*720/Math.PI}}
   }
}


function waktuKiblat(jd, lintang, bujur, timezone)
{
   var tKiblat = calcTimeJulianCent(jd - bujur/360.0)
   var dekRad = degToRad(calcSunDeclination(tKiblat))
   var eqTime = calcEquationOfTime(tKiblat)
   var latRad = degToRad(lintang)
   
   minute_kiblat(latRad, dekRad)
   if (_qiblaT1 != "nihil")
   {
     var kiblatOffset = 720 + _qiblaT1 - (bujur * 4) - eqTime // in minutes
     var newt = calcTimeJulianCent(jd + kiblatOffset/1440.0)
     eqTime = calcEquationOfTime(newt)
	 dekRad = degToRad(calcSunDeclination(newt))
	 minute_kiblat(latRad, dekRad)
	 
     if (_qiblaT1 != "nihil")
	  {_qiblaLoc1 = 720 + _qiblaT1 - (bujur * 4) - eqTime + (timezone*60.0); _qiblaLoc1 = timeString(_qiblaLoc1, 3)}
	} 
	else {_qiblaLoc1 = "tak terjadi"}
	
   if (_qiblaT2 != "nihil")
   {
     kiblatOffset = 720 + _qiblaT2 - (bujur * 4) - eqTime // in minutes
     newt = calcTimeJulianCent(jd + kiblatOffset/1440.0)
     eqTime = calcEquationOfTime(newt)
	 dekRad = degToRad(calcSunDeclination(newt))
	 minute_kiblat(latRad, dekRad)
	 
     if (_qiblaT2 != "nihil")
	  {_qiblaLoc2 = 720 + _qiblaT2 - (bujur * 4) - eqTime + (timezone*60.0); _qiblaLoc2 = timeString(_qiblaLoc2, 3)}
	}
	else {_qiblaLoc2 = "tak terjadi"}
}

function setOutput()
{
   var posMentari =  new google.maps.LatLng(_theta, _solarLng)
   document.getElementById("eqtbox").value = _eqTime.toFixed(2)
   document.getElementById("sdbox").value = _theta.toFixed(2)
   document.getElementById("kiblatbox").value = _qiblaLoc1
   document.getElementById("kiblatbox2").value = _qiblaLoc2	
   
   if (_solarDir != "gelap")
     {document.getElementById("azbox").value = _solarDir.toFixed(2)
	  document.getElementById("elbox").value = _solarEl.toFixed(2)}
   else
     {document.getElementById("azbox").value = "gelap"
	  document.getElementById("elbox").value = "gelap"}
   
   if (_solarEl > 0)
     {arah(_lineDirSol, _theta, _solarLng, true); gambar(true,true);}
   else
     {arah(_lineDirSol, _theta, _solarLng, false); gambar(true,false)}
	 
    _solMarker.setPosition(posMentari)	 
	 
   document.getElementById("azkiblatbox").value = _qiblaDir.toFixed(2)
   arah(_lineDirKib, _qiblaLat, _qiblaLng, true)
   document.getElementById("noonbox").value = timeString(_solNoonLocal, 3)
   _but1.disabled = false;
   _but2.disabled = false;
}

function getData()
{
  _docmonth = document.getElementById("mosbox").selectedIndex + 1
  _docday =   document.getElementById("daybox").selectedIndex + 1
  _docyear =  readTextBox("yearbox", 5, 1, 0, -2000, 3000, 2009)
  
  if ( (isLeapYear(_docyear)) && (_docmonth == 2) ) {
    if (_docday > 29) {
      _docday = 29
      document.getElementById("daybox").selectedIndex = _docday - 1
    } 
  } else {
    if (_docday > monthList[_docmonth-1].numdays) {
      _docday = monthList[_docmonth-1].numdays
      document.getElementById("daybox").selectedIndex = _docday - 1
    }
  }
 
 _dochr = readTextBox("hrbox", 2, 1, 1, 0, 23, 12)
 _docmn = readTextBox("mnbox", 2, 1, 1, 0, 59, 0)
 _docsc = readTextBox("scbox", 2, 1, 1, 0, 59, 0)
 
  _lat = parseFloat(document.getElementById("latbox").value.substring(0,9))
  _lng = parseFloat(document.getElementById("lngbox").value.substring(0,10))
  
  _tzone = readTextBox("zonebox", 5, 0, 0, -14, 13, 0)
  
}

function timeString(minutes, flag)
// timeString returns a zero-padded string (HH:MM:SS) given time in minutes
// flag=2 for HH:MM, 3 for HH:MM:SS
{
  if ( (minutes >= 0) && (minutes < 1440) ) {
  var floatHour = minutes / 60.0;
  var hour = Math.floor(floatHour);
  var floatMinute = 60.0 * (floatHour - Math.floor(floatHour));
  var minute = Math.floor(floatMinute);
  var floatSec = 60.0 * (floatMinute - Math.floor(floatMinute));
  var second = Math.floor(floatSec + 0.5);
  if (second > 59) {
    second = 0
    minute += 1
  }
  if ((flag == 2) && (second >= 30)) minute++;
  if (minute > 59) {
    minute = 0
    hour += 1
  }
  var output = zeroPad(hour,2) + ":" + zeroPad(minute,2);
  if (flag > 2) output = output + ":" + zeroPad(second,2);
  } else { 
    var output = "salah"
  }
  return output;
}

function clearOutputs()
{
  document.getElementById("eqtbox").value = ""
  document.getElementById("sdbox").value = ""
  document.getElementById("noonbox").value = ""
  document.getElementById("azbox").value = ""
  document.getElementById("elbox").value = ""
  document.getElementById("azkiblatbox").value = ""
  document.getElementById("kiblatbox").value = ""
  document.getElementById("kiblatbox2").value = ""
  
  arah(_lineDirKib,_qiblaLat,_qiblaLng,false)
  arah(_lineDirSol, _theta, _solarLng, false)
  gambar(false,false)
  _but1.disabled = true;
  _but2.disabled = true;
}

function calculate() {
  getData()
  var jday = JulianDay(_docday, _docmonth, _docyear)
  var tl = getTimeLocal()
  var total = jday + tl/1440.0 - _tzone/24.0
  var T = calcTimeJulianCent(total)
   
  calcAzEl(T, tl, _lat, _lng, _tzone)
  calcSolNoon(jday, _lng, _tzone)
  kiblat(_lat,_lng)
  waktuKiblat(jday, _lat, _lng, _tzone)
  setOutput()
}

function calculate1()
{
  var lat = parseFloat(document.getElementById("latbox").value.substring(0,9))
  var lng = parseFloat(document.getElementById("lngbox").value.substring(0,10))
  var center = new google.maps.LatLng(lat, lng);
  
  _marker1.setPosition(center); _map.setCenter(center); calculate()
}

function buatTabel()
{
    var kota = prompt("buat tabel untuk kota?")
	if (!kota){return}
    if (_winref && !_winref.closed) {_winref.close()}
    _winref = window.open("",'mywin',
     'left=20,top=20,width=800,height=500,resizable=1,menubar=1,scrollbars=1');
	 
    getData()
	var jday, kiblat1, kiblat2
	kiblat(_lat, _lng)
	
	_winref.document.write("<html><body><h3>Tabel Waktu Kiblat</h3>")
	_winref.document.write("kota : " + kota + "<br>lintang = " + _lat.toFixed(4) + "&deg; dan bujur = " + _lng.toFixed(4) + "&deg;<br>zona waktu = " + _tzone + "<br>")
	_winref.document.write("Azimuth Kiblat = " + _qiblaDir.toFixed(2)+ "&deg;<br>")
	_winref.document.write("bulan : " + monthList[_docmonth-1].name + " Tahun : " + _docyear + "<br><br>")
	
	_winref.document.write("<table border='1'><tr><th>Hari</th><th>Dzuhur</th><th>Waktu<br>Kiblat T-M<br>(berlawanan)</th><th>Waktu<br>Kiblat M-T<br>(searah)</th></tr>") 
	for (i=1; i <= monthList[_docmonth-1].numdays; i++)
	{
	  jday = JulianDay(i, _docmonth, _docyear)
	  calcSolNoon(jday, _lng, _tzone)
	  waktuKiblat(jday, _lat, _lng, _tzone)
	  
	  _winref.document.write("<tr><td>" + i + "</td><td>" +  timeString(_solNoonLocal,3) + "</td><td>" + _qiblaLoc1 + "</td><td>" + _qiblaLoc2 + "</td></tr>")
	}
	
	_winref.document.write("</table></body></html>")
} 


function getDirection(lat1, lng1, lat2, lng2) 
{
	var dLng = lng1- lng2;
	return radToDeg(getDirectionRad(degToRad(lat1), degToRad(lat2), degToRad(dLng)));
}

function getDirectionRad(lat1, lat2, dLng) 
{
	return Math.atan2(Math.sin(dLng), Math.cos(lat1)* Math.tan(lat2)- Math.sin(lat1)* Math.cos(dLng));
}

function kiblat(lat, lng)
{
   _qiblaDir = -getDirection(lat, lng, _qiblaLat, _qiblaLng);
   if (_qiblaDir < 0) {_qiblaDir = _qiblaDir + 360}
}

function arah(poly, lat, lng, plot)
{
    var path = poly.getPath()
	if (path.length == 2){path.pop(); path.pop()}

if (plot){	
	var b = _marker1.getPosition()
	var c = new google.maps.LatLng(lat, lng)
	path.push(b); path.push(c)}
}
 

function tulisLetak(loc)
{
  document.getElementById("latbox").value = loc.lat()
  document.getElementById("lngbox").value = loc.lng()
  calculate()
}


function tinjau(lat, lng, zlevel)
{
   var loc = new google.maps.LatLng(lat, lng)
   
   _map.setCenter(loc); _map.setZoom(zlevel); _map.setMapTypeId(google.maps.MapTypeId.SATELLITE); _marker1.setPosition(loc)
   tulisLetak(loc)
}

function sekarang(){
   var tanggal = new Date()
   document.getElementById("hrbox").value = tanggal.getHours();
   document.getElementById("mnbox").value = tanggal.getMinutes();
   document.getElementById("scbox").value = tanggal.getSeconds();
   calculate();
}

function ubahWaktu(x){
   var y = x.split(":");
   document.getElementById("hrbox").value = y[0];
   document.getElementById("mnbox").value = y[1];
   document.getElementById("scbox").value = y[2];
   calculate();
}   

function tDzuhur(){
   var waktu = document.getElementById("noonbox").value;
   ubahWaktu(waktu);
}

function tKiblat(){
   var waktu = document.getElementById("kiblatbox").value
   if (waktu == "tak terjadi"){
      waktu = document.getElementById("kiblatbox2").value;}
   if (waktu == "tak terjadi"){
      alert("tak terjadi waktu kiblat");}
   else 
     { ubahWaktu(waktu);}	  
}

function gambar(plot1,plot2)
{ var drawingCanvas = document.getElementById('myDrawing');
  var azQiblaRad = degToRad(_qiblaDir)
  
  var radius = 80
  var panahI = 6, panahJ = 12
  var xorg = 125, yorg = 125
  var ctiga = 0.6; csun = 1.3
  
  var rxqib = Math.sin(azQiblaRad)*radius
  var ryqib = - Math.cos(azQiblaRad)*radius
  var xqibla = xorg + rxqib
  var yqibla = yorg + ryqib
  var qx = xorg + rxqib*ctiga
  var qy = yorg + ryqib*ctiga
  
  if(plot2)
  {
    var delta = _solarDir - _qiblaDir
    var sunDir = _solarDir
  
    if (delta < 0.0)
      {if( (delta<-270) || (delta>-90) )
	    {if (delta<-270) delta += 360.0}
	   else
	    {delta += 180.0; sunDir = _solarDir + 180.0 }}
    else
      {if( (delta > 270) || (delta < 90) )
	    {if (delta > 270) delta -= 360.0}
	  else
	   {delta -= 180.0; sunDir = _solarDir - 180}}
  
    var searah = (Math.abs(sunDir - _solarDir) < 0.01)
	  
    var sunDirRad = degToRad(sunDir)
    var rxsun =  Math.sin(sunDirRad)*radius
    var rysun =  - Math.cos(sunDirRad)*radius
    var xsun = xorg + rxsun*csun
    var ysun = yorg + rysun*csun
    var  sx = xorg + rxsun*ctiga
    var  sy = yorg + rysun*ctiga
    var alphax = xorg - 0.1*rxsun
    var alphay = yorg - 0.1*rysun
    var betax = xorg + 1.4*rxsun
    var betay = yorg + 1.4*rysun
  }
  
  var ix = panahI*Math.cos(azQiblaRad)
  var iy = panahI*Math.sin(azQiblaRad)
  var jx = - panahJ*Math.sin(azQiblaRad)
  var jy = panahJ*Math.cos(azQiblaRad)
  
  var px1 = xqibla + ix + jx
  var py1 = yqibla + iy + jy
  var px2 = xqibla  - ix + jx 
  var py2 = yqibla - iy + jy
  
  
  if(drawingCanvas && drawingCanvas.getContext)
  {var ctx = drawingCanvas.getContext('2d'); 
   ctx.clearRect(0,0,250,250)
   
   if (plot2)   
   {
     ctx.fillStyle = "green"
     ctx.beginPath()
     ctx.moveTo(xorg, yorg)
     ctx.lineTo(qx, qy)
     ctx.lineTo(sx, sy)
     ctx.closePath()
     ctx.fill()
   
     ctx.strokeStyle="blue"
     ctx.beginPath()
     ctx.lineWidth = 2
     ctx.moveTo(xorg, yorg)
     ctx.lineTo(xsun,ysun)
     ctx.stroke()
   }
   
   if(plot1)
   {ctx.strokeStyle="red"
   ctx.beginPath()
   ctx.lineWidth = 2
   ctx.moveTo(xorg, yorg)
   ctx.lineTo(xqibla,yqibla)
   ctx.stroke()
  
   
   ctx.fillStyle = "red"
   ctx.beginPath()
   ctx.moveTo(xqibla, yqibla)
   ctx.lineTo(px1, py1)
   ctx.lineTo(px2, py2)
   ctx.fill()}
   
   if (plot2)
   {
     ctx.font="15px Times New Roman"
     ctx.fillStyle = "black"
      if (searah)
        {ctx.fillText("T",alphax,alphay); ctx.fillText("M",betax,betay)}
      else	 
        {ctx.fillText("M",alphax,alphay); ctx.fillText("T",betax,betay)}
   }
  }
  
  if (plot2)
  {
    if (searah)
       {document.getElementById("pusat").innerHTML = "T"}
    else
       {document.getElementById("pusat").innerHTML = "M"}
	 	 	
    var sudut = Math.abs(delta)
  
    if (delta < 0)
      document.getElementById("shadow").innerHTML = sudut.toFixed(2) + "&deg; searah jarum jam"
    else	
	  document.getElementById("shadow").innerHTML = sudut.toFixed(2) + "&deg; melawan jarum jam"

    if ( sudut < 45)
    {var cord1 = 100.0*Math.tan(degToRad(sudut)); var cord2 = cord1*0.5; var cord3 = cord1*0.25
     var hasil = "a = 25cm; t = " + cord3.toFixed(2) + "cm<br> a = 50cm; t = " +  cord2.toFixed(2) + "cm<br> a = 100cm; t = " + cord1.toFixed(2) + "cm"
     document.getElementById("tri1").innerHTML = hasil}
     else document.getElementById("tri1").innerHTML = "Tidak dianjurkan"
	
    var cord1 = 200.0*Math.sin(degToRad(sudut/2.0)); var cord2 = cord1*0.5; var cord3 = cord1*0.25
    var hasil = "a = 25cm; t = " + cord3.toFixed(2) + "cm<br> a = 50cm; t = " +  cord2.toFixed(2) + "cm<br> a = 100cm; t = " + cord1.toFixed(2) + "cm"
    document.getElementById("tri2").innerHTML = hasil
 }
 else
 {
  document.getElementById("pusat").innerHTML ="&nbsp;"
  document.getElementById("shadow").innerHTML ="&nbsp;"
  document.getElementById("tri1").innerHTML ="&nbsp;"
  document.getElementById("tri2").innerHTML ="&nbsp;"
 }
}

function init() {
    _geocoder = new google.maps.Geocoder();
	var salmanLat = -7.067405341822339, salmanLng = 110.40250387982576
    var myLatlng = new google.maps.LatLng(salmanLat, salmanLng);
    var myOptions = {
      zoom: 7,
      center: myLatlng,
      streetViewControl: true,
      mapTypeId: google.maps.MapTypeId.SATELLITE,
	  heading: 90,
      tilt: 45      
    }
    _map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    ketinggian = new google.maps.ElevationService();
    
     _marker1 = new google.maps.Marker({
		draggable: true,
        position: myLatlng, 
        map: _map
    });
    

	
	var mentari = new google.maps.MarkerImage('circle.gif',
    new google.maps.Size(20, 20),
    new google.maps.Point(0,0),
    new google.maps.Point(10, 10));
	
	_solMarker = new google.maps.Marker({
        icon: mentari,
        map: _map,
		zIndex: 3
    });
	
	_lineDirKib = new google.maps.Polyline({
      strokeColor: "#FF0000",
      strokeOpacity: 1,
      strokeWeight: 2,
	  geodesic: true,
	  zIndex : 1
    });
 
	_lineDirKib.setMap(_map);
	
	_lineDirSol = new google.maps.Polyline({
      strokeColor: "#0000FF",
      strokeOpacity: 1,
      strokeWeight: 2,
	  geodesic: true,
	  zIndex : 2
    });
 
	_lineDirSol.setMap(_map);
	
	document.getElementById("latbox").value = salmanLat
    document.getElementById("lngbox").value = salmanLng
	
	google.maps.event.addListener(_map, 'click', function(event) {
    _marker1.setPosition(event.latLng); _map.setCenter(event.latLng); tulisLetak(event.latLng)
  });
	
	google.maps.event.addListener(_marker1, 'dragend', function() {
    _map.setCenter(_marker1.getPosition()); tulisLetak(_marker1.getPosition());
  }); 
  _but1 = document.getElementById("butDzu");
  _but2 = document.getElementById("butQib");
  calculate() 
}

function cariAlamat() {
    var alamat = document.getElementById("kota").value;
    geocoder.geocode( { 'address': alamat}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK)
	  {
         map.setCenter(results[0].geometry.location);
         marker1.setPosition(results[0].geometry.location);
	     TulisLetak(results[0].geometry.location);
	  }
	  else
	  {
        alert("pencarian gagal, sebab: " + status);
      }
	  });
   }
   
function cariTinggi(x) {

  var locations = [];
  
  locations.push(x);
  var positionalRequest = {
    'locations': locations
  }

  ketinggian.getElevationForLocations(positionalRequest, function(results, status) {
    if (status == google.maps.ElevationStatus.OK) {

      if (results[0]) {
        //tampilkan hasil 
       document.getElementById("h").value = Math.round(results[0].elevation)
      } else {
        alert("Ketinggian tidak didapat"); document.getElementById("h").value = "0.0"
      }
    } else {
      alert("Gagal mencari ketinggian karena : " + status); document.getElementById("h").value = "0.0"
    }
  });
}   
   
 window.onload = function(){init();}   
</SCRIPT>

<div  align="center">
	<h3  align="center">Menentukan Kiblat Dengan Matahari (Rashdul Kiblat) </h3>
<a href="carashdul.php" target="_top"><button><font color="red">Cara Menentukan Kiblat</font></button></a>&nbsp;&nbsp;<a href="http://blogchem.com/kiblat/index.php" target="_blank"><button><font color="red">Peta Kiblat</font></button></a>
</div>
<br>
</p>
<center>
<div  align="center" id="map_canvas" style="width: 600px; height: 400px"></div> 
</center>
<div  align="center">
<br/>
<p  align="center"><span style="color:#FF0000">----- Garis merah arah Kiblat</span>&nbsp;&nbsp;<span style="color:#0000FF"> ------ Garis biru arah Matahari</span>
<br/>

</div> 
<br/>
  <TABLE  align="center" border="1" cellspacing="0" width="600">
  <caption align="top"> <strong>Form Data</strong></caption>
  <TR>
  <TD>Lintang<INPUT TYPE="text" NAME="lat" id="latbox" SIZE="8"  onChange="clearOutputs()"></TD>
  <TD>Bujur<INPUT TYPE="text" NAME="lon" id="lngbox" SIZE="8" onChange="clearOutputs()"></TD>
  <TD>Time Zone<INPUT TYPE= "text" NAME="timezone" id="zonebox" SIZE="3" VALUE="7" onChange="clearOutputs()"></TD>
  </TR>
  <TR>
  <TD>Tanggal
  <SELECT NAME="day" ID="daybox" onChange="calculate1()">
  <SCRIPT type="text/javascript">
	thisday = _tanggal.getDate();
     	for (i = 1; i < 32; i++) {
	  if (i == thisday) {
    	    document.writeln("<OPTION SELECTED>" + i);
	  }
	  else {
	    document.writeln("<OPTION>" + i);
	  }
	}
  </SCRIPT>
  </SELECT>
  </TD><TD> Bulan
  <SELECT NAME="mos" ID="mosbox" onChange="calculate1()">
  <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
    	thismonth = _tanggal.getMonth();
     	for (i = 0; i < monthList.length; i++) {
	  if (i == thismonth) {
    	    document.writeln("<OPTION SELECTED>" + monthList[i].abbr);
	  }
	  else {
	    document.writeln("<OPTION>" + monthList[i].abbr);
	  }
	}
  </SCRIPT>
  </SELECT>
  </TD><TD>Tahun
  <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	thisYear = _tanggal.getYear();
	if(thisYear <=199){
	  thisYear=thisYear+1900;
	}
	document.writeln("<INPUT TYPE=\"text\" ID=\"yearbox\" NAME=\"year\" SIZE=\"4\"VALUE=\"" + thisYear + "\" onChange=\"clearOutputs()\">");
  </SCRIPT>
  </TD>
  </TR>
  <tr>
  <TD colspan="3">
  Waktu Setempat
  <script language="javascript" type="text/javascript">
    thisHour = _tanggal.getHours();
	thisMin = _tanggal.getMinutes();
	thisSec = _tanggal.getSeconds();
    document.writeln("<INPUT TYPE=\"text\" ID=\"hrbox\" SIZE=\"2\" VALUE=\"" + thisHour + "\"  onChange=\"clearOutputs()\"> <b> : </b>");
    document.writeln("<INPUT TYPE=\"text\" ID=\"mnbox\" SIZE=\"2\"  VALUE=\"" + thisMin + "\" onChange=\"clearOutputs()\" > <b>:</b>");
    document.writeln("<INPUT TYPE=\"text\" ID=\"scbox\" SIZE=\"2\" VALUE=\"" + thisSec + "\" onChange=\"clearOutputs()\">");
  </script>
  <input type="button" value="Sekarang" onClick="sekarang()">
  <input type="button" id = "butDzu" value="Dzuhur" onClick="tDzuhur()">
  <input type="button" id ="butQib" value="Kiblat" onClick="tKiblat()">
 </TD>
  </TR>
  </TABLE>
<br>
<TABLE  align="center" border="1" cellspacing="0" width="600">
<caption align="top"><strong>Hasil perhitungan</strong></caption>
  <TR align="center">
  <TD>Azimuth <BR>Kiblat<BR>(derajat):</TD>
  <TD>Equation <BR>of Time<BR>(menit):</TD>
  <TD>Solar<BR>Declination<BR> (derajat):</TD>
  <TD>Tengah hari<br>(Dzuhur)</TD>
  </TR>
  <TR align="center">
   <TD><INPUT TYPE="text" ID="azkiblatbox" NAME="azkiblat" SIZE = "8" readonly="readonly"></TD>
   <TD><INPUT TYPE="text" ID="eqtbox" NAME="eqTime" SIZE = "8" readonly="readonly"></TD>
   <TD><INPUT TYPE="text" ID="sdbox" NAME="solarDec" SIZE = "8" readonly="readonly"></TD>
   <TD><INPUT TYPE="text" ID="noonbox" NAME="solnoon" SIZE="8" readonly="readonly"></TD>
  </TR> 
  <tr><td colspan="4">&nbsp;</td></tr>
  <TR align="center">
  <TD>Waktu<br>
  Kiblat T-M <br>
  melawan bayangan </TD>
  <TD>Waktu<br> 
  Kiblat M-T <br>
  searah bayangan </TD>
  <TD>Azimuth<br>matahari<BR>(derajat)</TD>
  <TD>Elevation<br>matahari<BR>(derajat):</TD>
  </tr>
  <TR align="center">
  <TD><INPUT TYPE="text" ID="kiblatbox" NAME="kiblat" SIZE="8" readonly="readonly"></TD>
  <TD><INPUT TYPE="text" ID="kiblatbox2" NAME="kiblat2" SIZE="8" readonly="readonly"></TD>
  <TD><INPUT TYPE="text" ID="azbox" NAME="az" SIZE="5" readonly="readonly"></TD>
  <TD><INPUT TYPE="text" ID="elbox" NAME="el" SIZE="5" readonly="readonly"></td>
  </tr>
</table>

 <br/>
<table align="center" width="600" border="1" cellspacing="0">
  <caption align="top">
    Kostruksi Arah Kiblat Dengan  bayangan dan sudut koreksi &alpha;
  </caption>
  <tr>
    <td width="239">Pusat(M atau T)</td>
    <td width="181" id="pusat">&nbsp;</td>
  </tr>
  <tr>
    <td>Sudut kiblat &alpha; dari bayangan</td>
    <td id="shadow">&nbsp;</td>
  </tr>
  <tr>
    <td>Segi-Tiga Siku-siku</td>
    <td id="tri1">&nbsp;</td>
  </tr>
  <tr>
    <td>Segi-Tiga Sama-kaki</td>
    <td id="tri2">&nbsp;</td>
  </tr>
</table>
<br/><br/>
</body>
</html>

