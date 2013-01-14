body 
{
	color: #000000;
	font-family: arial;
	font-size: 15px;

	margin: 0px;
	padding: 0px;
	
	/*minimum en standaard formaat van de body*/
	min-width: 800px;
	min-height: 600px;
	width: 100%;
	height: 100%;
}



/*header*/
#header
{
	background:url(../img/noise1.png);

	/*formaat*/
	min-height: 80px;
	height: 5%;
	width: 100%;
	min-width: 1000px;
    margin-bottom:10px;

}


/*login gedeelte*/
#login
{
	font-size: 12px;
	position: absolute;
	right: 0px;
	top: 0px;
	width: 500px;
}

#login form
{
    margin-top:30px;
    height:60px;
    margin-left:80px;
}

#login form .register {
    margin-left:10px;
    font-style:italic;
}

#login form p
{
	padding: 0px;
	margin: 0px;
	border: 0px;
}

#login form input[type="button"]
{
    padding:3px;
    color:#fff;
    font-size:90%;
    background: rgb(96,108,136); /* Old browsers */
    background: -moz-linear-gradient(top,  rgba(96,108,136,1) 0%, rgba(63,76,107,1) 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(96,108,136,1)), color-stop(100%,rgba(63,76,107,1))); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  rgba(96,108,136,1) 0%,rgba(63,76,107,1) 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  rgba(96,108,136,1) 0%,rgba(63,76,107,1) 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  rgba(96,108,136,1) 0%,rgba(63,76,107,1) 100%); /* IE10+ */
    background: linear-gradient(to bottom,  rgba(96,108,136,1) 0%,rgba(63,76,107,1) 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#606c88', endColorstr='#3f4c6b',GradientType=0 ); /* IE6-9 */
}

#login form input[type="button"]:hover {
    background: rgb(176,212,227); /* Old browsers */
    background: -moz-linear-gradient(top,  rgba(176,212,227,1) 0%, rgba(136,186,207,1) 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(176,212,227,1)), color-stop(100%,rgba(136,186,207,1))); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  rgba(176,212,227,1) 0%,rgba(136,186,207,1) 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  rgba(176,212,227,1) 0%,rgba(136,186,207,1) 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  rgba(176,212,227,1) 0%,rgba(136,186,207,1) 100%); /* IE10+ */
    background: linear-gradient(to bottom,  rgba(176,212,227,1) 0%,rgba(136,186,207,1) 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b0d4e3', endColorstr='#88bacf',GradientType=0 ); /* IE6-9 */
}

#login form input[type="text"]
{
	height: 10px;
}

#login form input[type="password"]
{
	height: 10px;
}


/*navigatie gedeelte*/
#navigatie
{
	background-color: #e9e9e9;
	position: relative;
	width: 630px;
	height: 30px;
	top: 49px;
	left: 20px;
    border-top:1px solid #ccc;
    border-left:1px solid #ccc;
    border-right:1px solid #ccc;
}

#navigatie ul
{
	list-style-type:none;
	margin:0;
	padding-left: 2px;
	padding-top:2px;
	padding-bottom:2px;
}

#navigatie li
{
	display: inline;
}

#navigatie a:link,#navigatie a:visited,#navigatie a
{
	display: inline-block;
	font-weight:bold;
	color:grey;
	text-align:center;
	padding: 7px 4px 12px 4px;
	width: 130px;
	text-decoration:none;
    margin-left:10px;
}

.sep {
    border-left:1px solid #c8c8c8;
}

#navigatie a:hover,#navigatie a:active
{
	display: inline-block;
	width: 130px;
    color: #3e3e3e;
    background-color:#fff;
    border-left:1px solid #e0e0e0;
    border-right:1px solid #e0e0e0;
    border-top:1px solid #e0e0e0;
}



/*standaard pagina opmaak*/
#content
{
	background-color: white;
	margin-left: 15%;
	margin-right: 15%;
	margin-top: 2%;
	margin-bottom: 80px;

	position: relative;
	z-index: 0;
	
	/*formaat*/
	width: 70%;
	min-width: 560px;
	min-height: 75%;
	max-height: 80%;
}

#footer
{
	background-color: #3f3f3f;
	
	/*formaat*/
	height: 56px;
	width: 100%;
	min-width: 800px;
    clear:both;
}

#footer img
{
	/*posities ten opzichte van andere objecten*/
	margin: 10px;
	border: 0px;
	padding: 0px;
}
