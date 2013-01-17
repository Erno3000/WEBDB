<?xml version="1.1" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
        "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Create event</title>
    <link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/create_item.css" />
    <link rel="shortcut icon" type="image/x-icon" href="smile.ico" />
</head>
<body>

<?php
	include('header.php');
?>

<div id="content">
    <h1>Create calendar item</h1>

    <div id="ccform">
        <fieldset>
            <legend>Fill in this form to create a new calendar item</legend>

            <form action="">
                <ul>
                    <li>
                        <label for="subject">Subject*:</label>
                        <input type="text" id="subject" name="subject" /><br />
                        <p class="maximum">Maximum of 50 characters.</p>
                    </li>
                    <li>
                        <label for="target">Target audience*:</label>
                        <div id="target">
                            <input type="checkbox" name="target audience" value="employees" />Employees<br />
                            <input type="checkbox" name="target audience" value="shareholders" />Shareholders<br />
                            <input type="checkbox" name="target audience" value="customers" />Customers<br />
                        </div>
                    </li>
                    <li>
                        <label for="description">Description*:</label>
                        <textarea id="description" cols="40" rows="5">Give a description of the event here...</textarea><br />
                        <p class="maximum">Maximum of 500 characters.</p>
                    </li>
                    <li>
                        <label for="date">Start Date*:</label>
                        <div id="startdate">
		                    Month: 
		                    <select name="Month">
		                    <option value="January">January</option>
		                    <option value="February">February</option>
		                    <option value="March">March</option>
		                    <option value="April">April</option>
		                    <option value="May">May</option>
		               		<option value="June">June</option>
		                    <option value="July">July</option>
		                    <option value="August">August</option>
		                    <option value="September">September</option>
		                    <option value="October">October</option>
		                    <option value="November">November</option>
		                    <option value="December">December</option>
		                    </select>
		                    Day: 
		                    <select name="Day">
		                    <option value="1">1</option><option value="2">2</option>
		                    <option value="3">3</option><option value="4">4</option>
		                    <option value="5">5</option><option value="6">6</option>
		                    <option value="7">7</option><option value="8">8</option>
		                    <option value="9">9</option><option value="10">10</option>
		                    <option value="11">11</option><option value="12">12</option>
		                    <option value="13">13</option><option value="14">14</option>
		                    <option value="15">15</option><option value="16">16</option>
		                    <option value="17">17</option><option value="18">18</option>
		                    <option value="19">19</option><option value="20">20</option>
		                    <option value="21">21</option><option value="22">22</option>
		                    <option value="23">23</option><option value="24">24</option>
		                    <option value="25">25</option><option value="26">26</option>
		                    <option value="27">27</option><option value="28">28</option>
		                    <option value="29">29</option><option value="30">30</option>
		                    <option value="31">31</option>
		                    </select>
		                    Year: <input type="text" name="Year" size="4" /><br />
		                </div>
		                <label for="date">End Date*:</label>
		                <div id="enddate">  
		                    Month: 
		                    <select name="Month">
		                    <option value="January">January</option>
		                    <option value="February">February</option>
		                    <option value="March">March</option>
		                    <option value="April">April</option>
		                    <option value="May">May</option>
		                    <option value="June">June</option>
		                    <option value="July">July</option>
		                    <option value="August">August</option>
		                    <option value="September">September</option>
		                    <option value="October">October</option>
		                    <option value="November">November</option>
		                    <option value="December">December</option>
		                    </select>
		                    Day: 
		                    <select name="Day">
		                    <option value="1">1</option><option value="2">2</option>
		                    <option value="3">3</option><option value="4">4</option>
		                    <option value="5">5</option><option value="6">6</option>
		                    <option value="7">7</option><option value="8">8</option>
		                    <option value="9">9</option><option value="10">10</option>
		                    <option value="11">11</option><option value="12">12</option>
		                    <option value="13">13</option><option value="14">14</option>
		                    <option value="15">15</option><option value="16">16</option>
		                    <option value="17">17</option><option value="18">18</option>
		                    <option value="19">19</option><option value="20">20</option>
		                    <option value="21">21</option><option value="22">22</option>
		                    <option value="23">23</option><option value="24">24</option>
		                    <option value="25">25</option><option value="26">26</option>
		                    <option value="27">27</option><option value="28">28</option>
		                    <option value="29">29</option><option value="30">30</option>
		                    <option value="31">31</option>
		                    </select>
		                    Year: <input type="text" name="Year" size="4" /><br>
                        </div>
                    </li>
                    <li>
                        <label for="time1">Time*:</label>
                        <input type="text" id="time1" name="time1" size="2" />:<input type="text" name="time2" size="2" />
                        - <input type="text" name="time" size="2" />:<input type="text" name="time4" size="2" /><br />
                    </li>
                    <li>
                        <label for="place">Place*:</label>
                        <input type="text" id="place" name="place" /><br />
                        <p class="maximum">Maximum of 100 characters.</p>
                    </li>
                    <li>
                        <label for="submit">&nbsp;</label>
                        <input type="submit" id="submit" value="Submit" />
                    </li>
                </ul>

                <p class="mandatory">The fields marked with an asterisk (*) are mandatory.</p>

            </form>
        </fieldset>
</div>
</div>

<?php
	include('footer.php');
?>

</body>

</html>