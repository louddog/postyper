<?php

new Postype(array(
	'slug' => 'department',
	'archive' => 'departments',
));

new Postype(array(
	'slug' => 'employee',
	'archive' => 'employees',
	'fields' => array(
		array('name' => '_job_title', 'type' => 'text', 'label' => "Job Title", 'description' => "This job title for this employee"),
		array('name' => '_computer_num', 'type' => 'int', 'label' => "Computer Number", 'description' => "The employee's computer's number."),
		array('name' => '_hire_date', 'type' => 'date', 'label' => "Hire date", 'description' => "The date the employee was hired."),
		array('name' => '_lunch_time', 'type' => 'time', 'label' => "Lunch Time", 'description' => "Time the employee starts their lunch break."),
		array('name' => '_last_review', 'type' => 'date-time', 'label' => "Last Review", 'description' => "The date and time of the employee's last review."),
		array('name' => '_shift', 'type' => 'time-range', 'label' => "Shift", 'description' => "The start and end time for the employee's shift."),
		array('name' => '_wage', 'type' => 'money', 'label' => "Wage", 'description' => "The employee's hourly wage."),
		array('name' => '_department', 'type' => 'post', 'label' => "Department", 'description' => "The department the employee works in.", 'options' => array('post_type' => 'department')),
		array('name' => '_location', 'type' => 'select', 'label' => "Location", 'description' => "The location the employee is working.", 'options' => array("California", "Hawaii", "New York", "London", "Hong Kong", "Brisbane")),
		array('name' => '_retired', 'type' => 'checkbox', 'label' => "Retired", 'description' => "This employee has retired."),
		array('name' => '_bio', 'type' => 'textarea', 'label' => "Bio", 'description' => "Provide a short bio for this employee."),
		array('name' => '_clearance', 'type' => 'slider', 'label' => "Security Clearance", 'description' => "The security clearance for the employee.", 'options' => array('min' => 1, 'max' => 10)),
		array('name' => '_client_load', 'type' => 'range', 'label' => "Clients", 'description' => "Number of clients the employee can handle concurrently.", 'options' => array('min' => 1, 'max' => 100)),
	),
));