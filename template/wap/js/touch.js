/*
	[Destoon B2B System] Copyright (c) 2008-2013 Destoon.COM
	This is NOT a freeware, use is subject to license.txt
*/
function Dd(i) {return document.getElementById(i);}
function Ds(i) {Dd(i).style.display = '';}
function Dh(i) {Dd(i).style.display = 'none';}
function Dback(url) {
	if(document.referrer) {
		window.history.back();
		//window.location.href = document.referrer;
	} else {
		window.location.href = url ? url : 'index.php';
	}
}